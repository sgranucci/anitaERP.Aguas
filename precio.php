<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;
use App\Models\Seguridad\Usuario;
use Carbon\Carbon;
use Auth;

class Precio extends Model
{
    protected $fillable = ['articulo_id', 'listaprecio_id', 'fechavigencia', 'moneda_id', 'precio', 'precioanterior', 'usuarioultcambio_id'];
    protected $table = 'precio';
    protected $tableAnita = 'stkpre';
    protected $keyField = 'sku';
    protected $keyFieldAnita = ['stkp_articulo','stkp_lista'];

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuarioultcambio_id');
    }

    public function listaprecios()
    {
        return $this->belongsTo(Listaprecio::class, 'listaprecio_id');
    }

    public function articulos()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id');
    }

    public function monedas()
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'campos' => 'stkp_articulo, stkp_lista', 'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		if ($dataAnita)
		{
        	foreach ($dataAnita as $value) {
                $this->traerRegistroDeAnita($value->{$this->keyFieldAnita[0]}, $value->{$this->keyFieldAnita[1]});
        	}
		}
    }

    public function traerRegistroDeAnita($articulo, $lista){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'campos' => '
                stkp_articulo,
                stkp_lista,
				stkp_precio,
				stkp_precio_ant,
				stkp_cod_mon,
				stkp_fe_ult_act,
				stkp_usuario,
				stkp_terminal
            ' , 
            'whereArmado' => " WHERE ".$this->keyFieldAnita[0]." = '".$articulo."' AND ".$this->keyFieldAnita[1]." = '".$lista."' "
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];
			$usuario_id = Auth::user()->id;
			$sku = ltrim($data->stkp_articulo, '0');
			$articulo = Articulo::where('sku', $sku)->first();
			$listaprecio = Listaprecio::where('codigo', $data->stkp_lista)->first();

			if ($data->stkp_fe_ult_act < 19000000)
				$data->stkp_fe_ult_act = 20100101;
			$fechavigencia = date('Y-m-d', strtotime($data->stkp_fe_ult_act));

            Precio::create([
				"articulo_id" => $articulo->id,
				"listaprecio_id" => $listaprecio->id,
				"fechavigencia" => $fechavigencia,
				"moneda_id" => $data->stkp_cod_mon,
				"precio" => $data->stkp_precio,
				"precioanterior" => $data->stkp_precio_ant,
				"usuarioultcambio_id" => $usuario_id
            ]);
        }
    }

	public function guardarAnita($request) {
        $apiAnita = new ApiAnita();

		$fecha_vigencia = Carbon::now();
		$fecha_vigencia = $fecha_vigencia->format('Ymd');

		$codigo = str_pad($request->articulos->sku, 13, "0", STR_PAD_LEFT);
		$usuario = Auth::user()->nombre;

        $data = array( 'tabla' => $this->tableAnita, 
			'acc' => 'insert',
            'campos' => ' stkp_articulo, stkp_lista, stkp_precio, stkp_precio_ant, stkp_cod_mon, stkp_fe_ult_act, stkp_usuario, stkp_terminal',
            'valores' => " '".$codigo."', '".$request->listaprecios->codigo."', '".$request->precio."', '".$request->precioanterior."', '".$request->moneda_id."', '".$fecha_vigencia."', '".$usuario."', 'www'"
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request) {
        $apiAnita = new ApiAnita();

		$fecha_vigencia = Carbon::now();
		$fecha_vigencia = $fecha_vigencia->format('Ymd');

		$codigo = str_pad($request->articulos->sku, 13, "0", STR_PAD_LEFT);
		$usuario = Auth::user()->nombre;

		$data = array( 'acc' => 'update', 
				'tabla' => $this->tableAnita,
            	'valores' => " stkp_articulo = '".$codigo."', stkp_lista = '".$request->listaprecios->codigo."', stkp_precio = '".$request-precio."', stkp_precio_ant = '".$request->precioant."', stkp_cod_mon = '".$request->moneda_id."', stkp_fe_ult_act = '".$fecha_vigencia."', stkp_usuario = '".$usuario."', stkp_terminal = ' '",
            	'whereArmado' => " WHERE ".$this->keyFieldAnita[0]." = '".$codigo."' AND ".$this->keyFieldAnita[1]." = '".$request->listaprecios->codigo."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($codigo, $lista) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 
						'tabla' => $this->tableAnita, 
            			'whereArmado' => " WHERE ".$this->keyFieldAnita[0]." = '".$codigo."' AND ".$this->keyFieldAnita[1]." = '".$lista."' " );
        $apiAnita->apiCall($data);
	}
}
