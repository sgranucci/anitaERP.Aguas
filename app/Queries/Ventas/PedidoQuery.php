<?php

namespace App\Queries\Ventas;

use App\Models\Ventas\Pedido;
use App\Models\Stock\Capeart;
use App\Models\Stock\Avioart;

class PedidoQuery implements PedidoQueryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Pedido $pedido)
    {
        $this->model = $pedido;
    }

    public function allPendienteIndex()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '2400');
		//$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')
                            //->with('pedido_combinaciones')
                            //->orderBy('id','desc')
                            //->get();
        $pedidos = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')
                                ->orderBy('id','desc')
                                ->with('pedido_combinaciones')
                                ->get();

        $datas = [];
        foreach($pedidos as $pedido)
        {
            $pares = 0;
            foreach($pedido->pedido_combinaciones as $item)
            {
                $pares += $item->cantidad;
                $estado = $item->estado;
            }
            $estado = '';
            if ($estado != 'A' && $estado != 'F')
            {
                $datas[] = ['id' => $pedido->id,
                        'fecha' => $pedido->fecha,
                        'nombrecliente' => $pedido->clientes->nombre,
                        'codigo' => $pedido->codigo,
                        'nombremarca' => $pedido->mventas->nombre,
                        'pares' => $pares
                ];
            }
        }
		return $datas;
    }

    public function allPendiente($cliente_id = null)
    {
		if ($cliente_id)
			$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')->with('pedido_combinaciones')->where('estado','0')->where('cliente_id',$cliente_id)->get();
		else
			$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')->with('pedido_combinaciones')->where('estado','0')->get();

		return $mod;
    }

    public function allPendienteOt($articulo_id, $combinacion_id)
    {
		$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')
				->where('estado','0')
				->withWhereHasOtArticuloCombinacion($articulo_id, $combinacion_id)
				->get();

		return $mod;
    }

    public function leePedidoporCodigo($codigo)
    {
        return $this->model->select('id')->where('codigo' , $codigo)->first();
    }

    public function leePedidoporId($id)
    {
      $mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')->with('pedido_combinaciones')->where('estado','0')->where('id',$id)->get();

      return $mod;
    }

    public function first()
    {
        return $this->model->first();
    }

    public function all()
    {
        return $this->model->with('pedido_combinaciones')->get();
    }

    public function findPorRangoFecha($tipolistado, $mventa_id, $desdefecha, $hastafecha, 
                                $desdevendedor_id, $hastavendedor_id,
                                $desdecliente_id, $hastacliente_id,
                                $desdearticulo_id, $hastaarticulo_id,
                                $desdelinea_id, $hastalinea_id,
                                $desdefondo_id, $hastafondo_id)
    {
        $pedido = $this->model
                        ->select('pedido.*','pedido_combinacion.*',
                                'pedido_combinacion.id as pedido_combinacion_id',
                                'pedido_combinacion_talle.talle_id as talle_id',
                                'talle.nombre as nombretalle',
                                'pedido_combinacion_talle.cantidad as cantidadportalle',
                                'articulo.linea_id as linea_id',
                                'articulo.sku as sku',
                                'articulo.id as articulo_id',
                                'articulo.descripcion as nombrearticulo',
                                'articulo.mventa_id as mventa_id',
                                'combinacion.nombre as nombrecombinacion',
                                'combinacion.fondo_id as fondo_id',
                                'combinacion.colorfondo_id as colorfondo_id',
                                'linea.nombre as nombrelinea',
                                'fondo.nombre as nombrefondo',
                                'cliente.id as cliente_id',
                                'cliente.nombre as nombrecliente',
                                'cliente.codigo as codigocliente',
                                'tiposuspensioncliente.nombre as estadocliente',
                                'ordentrabajo.id as ordentrabajo_id',
                                'ordentrabajo.codigo as codigoot',
                                'vendedor.id as vendedor_id',
                                'vendedor.nombre as nombrevendedor',
                                'color.nombre as nombrecolorfondo')
                        ->with('clientes:id,nombre')
                        ->with('mventas:id,nombre')
                        ->join('pedido_combinacion', 'pedido_combinacion.pedido_id', '=', 'pedido.id')
                        ->join('pedido_combinacion_talle', 'pedido_combinacion_talle.pedido_combinacion_id', '=', 'pedido_combinacion.id')
                        ->leftjoin('ordentrabajo_combinacion_talle', 'ordentrabajo_combinacion_talle.pedido_combinacion_talle_id', '=', 'pedido_combinacion_talle.id')
                        ->leftjoin('ordentrabajo', 'ordentrabajo.id', '=', 'ordentrabajo_combinacion_talle.ordentrabajo_id')
                        ->join('articulo', 'articulo.id', '=', 'pedido_combinacion.articulo_id')
                        ->join('combinacion', 'combinacion.id', '=', 'pedido_combinacion.combinacion_id')
                        ->join('linea', 'linea.id', '=', 'articulo.linea_id')
                        ->join('talle', 'talle.id', '=', 'pedido_combinacion_talle.talle_id')
                        ->join('fondo', 'fondo.id', '=', 'combinacion.fondo_id')
                        ->join('cliente', 'cliente.id', '=', 'pedido.cliente_id')
                        ->leftjoin('tiposuspensioncliente', 'tiposuspensioncliente.id', 'cliente.tiposuspension_id')
                        ->leftjoin('vendedor', 'vendedor.id', '=', 'pedido.vendedor_id')
                        ->leftjoin('color', 'color.id', '=', 'combinacion.colorfondo_id')
                        ->whereBetween('pedido.fecha', [$desdefecha, $hastafecha])
                        ->whereBetween('pedido.cliente_id', [$desdecliente_id, $hastacliente_id])
                        ->whereBetween('combinacion.fondo_id', [$desdefondo_id, $hastafondo_id])
                        ->whereBetween('pedido.vendedor_id', [$desdevendedor_id, $hastavendedor_id])
                        ->whereBetween('pedido_combinacion.articulo_id', [$desdearticulo_id, $hastaarticulo_id])
                        ->whereBetween('linea.id', [$desdelinea_id, $hastalinea_id]);
             
        // Selecciona marca
        if ($mventa_id > 0)
            $pedido = $pedido->where('articulo.mventa_id', $mventa_id);

        switch($tipolistado)
        {
            case 'CLIENTE':
                $pedido = $pedido->orderBy('pedido.cliente_id')
                            ->orderBy('pedido_combinacion.id')->get();
                break;
            case 'ARTICULO':
                $pedido = $pedido->orderBy('sku')
                            ->orderBy('nombrecombinacion')
                            ->orderBy('pedido_combinacion.id')->get();
                break;
            case 'LINEA':
                $pedido = $pedido->orderBy('articulo.linea_id')
                            ->orderBy('pedido_combinacion.id')->get();
                break;
            case 'VENDEDOR':
                $pedido = $pedido->orderBy('pedido.vendedor_id')
                            ->orderBy('pedido_combinacion.id')->get();
                break;
            case 'FONDO':
                $pedido = $pedido->orderBy('combinacion.fondo_id')
                            ->orderBy('combinacion.colorfondo_id')
                            ->orderBy('pedido_combinacion.id')->get();
                break;
        }
      
        return $pedido;
    }

    // Query para reporte de consumo de materiales

    public function findPorMaterialCapellada($tipolistado, $tipocapellada, 
                                $desdefecha, $hastafecha, 
                                $desdecliente_id, $hastacliente_id,
                                $desdearticulo_id, $hastaarticulo_id,
                                $desdelinea_id, $hastalinea_id,
                                $desdecolor_id, $hastacolor_id,
                                $desdematerialcapellada_id, $hastamaterialcapellada_id)
    {
        $pedido = Capeart::select('capeart.*',
                                'pedido.fecha',
                                'pedido.cliente_id',
                                'pedido_combinacion.id as pedido_combinacion_id',
                                'pedido_combinacion.articulo_id',
                                'pedido_combinacion.combinacion_id',
                                'pedido_combinacion_talle.talle_id as talle_id',
                                'talle.nombre as nombretalle',
                                'pedido_combinacion_talle.cantidad as cantidadportalle',
                                'articulo.linea_id as linea_id',
                                'articulo.sku as sku',
                                'articulo.id as articulo_id',
                                'articulo.descripcion as nombrearticulo',
                                'combinacion.nombre as nombrecombinacion',
                                'combinacion.fondo_id as fondo_id',
                                'linea.nombre as nombrelinea',
                                'fondo.nombre as nombrefondo',
                                'cliente.id as cliente_id',
                                'cliente.nombre as nombrecliente',
                                'cliente.codigo as codigocliente',
                                'ordentrabajo.id as ordentrabajo_id',
                                'ordentrabajo.codigo as codigoot',
                                'vendedor.id as vendedor_id',
                                'vendedor.nombre as nombrevendedor',
                                'materialcapellada.id as materialcapellada_id',
                                'materialcapellada.nombre as nombrematerialcapellada',
                                'color.nombre as nombrecolor')
                        ->join('pedido_combinacion', function($join)
                            {
                                $join->on('pedido_combinacion.articulo_id', 'capeart.articulo_id')
                                     ->on('pedido_combinacion.combinacion_id', 'capeart.combinacion_id');
                            })
                        ->join('pedido_combinacion_talle', 'pedido_combinacion_talle.pedido_combinacion_id', 'pedido_combinacion.id')
                        ->join('pedido', 'pedido.id', 'pedido_combinacion.pedido_id')
                        ->leftjoin('ordentrabajo_combinacion_talle', 'ordentrabajo_combinacion_talle.pedido_combinacion_talle_id', 'pedido_combinacion_talle.id')
                        ->leftjoin('ordentrabajo', 'ordentrabajo.id', 'ordentrabajo_combinacion_talle.ordentrabajo_id')
                        ->join('articulo', 'articulo.id', 'pedido_combinacion.articulo_id')
                        ->join('combinacion', 'combinacion.id', 'pedido_combinacion.combinacion_id')
                        ->leftjoin('materialcapellada', 'materialcapellada.articulo_id', 'capeart.material_id')
                        ->join('color', 'color.id', 'capeart.color_id')
                        ->join('linea', 'linea.id', 'articulo.linea_id')
                        ->join('talle', 'talle.id', 'pedido_combinacion_talle.talle_id')
                        ->leftjoin('fondo', 'fondo.id', 'combinacion.fondo_id')
                        ->join('cliente', 'cliente.id', 'pedido.cliente_id')
                        ->leftjoin('vendedor', 'vendedor.id', '=', 'pedido.vendedor_id')
                        ->whereBetween('pedido.fecha', [$desdefecha, $hastafecha])
                        ->whereBetween('pedido.cliente_id', [$desdecliente_id, $hastacliente_id])
                        ->whereBetween('capeart.material_id', [$desdematerialcapellada_id, $hastamaterialcapellada_id])
                        ->whereBetween('capeart.color_id', [$desdecolor_id, $hastacolor_id])
                        ->whereBetween('pedido_combinacion.articulo_id', [$desdearticulo_id, $hastaarticulo_id])
                        ->whereBetween('linea.id', [$desdelinea_id, $hastalinea_id])
                        ->where(function ($query) use ($tipocapellada) {
                                if ($tipocapellada != 'TODOS')
                                    $query->where('tipo', substr($tipocapellada,0,1));
                            })
                        ->orderBy('materialcapellada.id')
                        ->orderBy('color.nombre')
                        ->orderBy('capeart.tipo')
                        ->orderBy('pedido_combinacion.id')->get();

        return $pedido;
    }

    // Query para reporte de consumo de avios

    public function findPorMaterialAvio($tipolistado, $tipoavio, 
                                $desdefecha, $hastafecha, 
                                $desdecliente_id, $hastacliente_id,
                                $desdearticulo_id, $hastaarticulo_id,
                                $desdelinea_id, $hastalinea_id,
                                $desdecolor_id, $hastacolor_id,
                                $desdematerialavio_id, $hastamaterialavio_id)
    {
        $pedido = Avioart::select('avioart.*',
                                'pedido_combinacion.*',
                                'pedido.*',
                                'pedido_combinacion.id as pedido_combinacion_id',
                                'pedido_combinacion_talle.talle_id as talle_id',
                                'talle.nombre as nombretalle',
                                'pedido_combinacion_talle.cantidad as cantidadportalle',
                                'articulo.linea_id as linea_id',
                                'articulo.sku as sku',
                                'articulo.id as articulo_id',
                                'articulo.descripcion as nombrearticulo',
                                'combinacion.nombre as nombrecombinacion',
                                'combinacion.fondo_id as fondo_id',
                                'linea.nombre as nombrelinea',
                                'fondo.nombre as nombrefondo',
                                'cliente.id as cliente_id',
                                'cliente.nombre as nombrecliente',
                                'cliente.codigo as codigocliente',
                                'ordentrabajo.id as ordentrabajo_id',
                                'ordentrabajo.codigo as codigoot',
                                'vendedor.id as vendedor_id',
                                'vendedor.nombre as nombrevendedor',
                                'materialavio.id as materialavio_id',
                                'materialavio.nombre as nombrematerialavio',
                                'color.nombre as nombrecolor')
                        ->join('pedido_combinacion', function($join)
                            {
                                $join->on('pedido_combinacion.articulo_id', 'avioart.articulo_id')
                                     ->on('pedido_combinacion.combinacion_id', 'avioart.combinacion_id');
                            })
                        ->join('pedido_combinacion_talle', 'pedido_combinacion_talle.pedido_combinacion_id', 'pedido_combinacion.id')
                        ->join('pedido', 'pedido.id', 'pedido_combinacion.pedido_id')
                        ->leftjoin('ordentrabajo_combinacion_talle', 'ordentrabajo_combinacion_talle.pedido_combinacion_talle_id', 'pedido_combinacion_talle.id')
                        ->leftjoin('ordentrabajo', 'ordentrabajo.id', 'ordentrabajo_combinacion_talle.ordentrabajo_id')
                        ->join('articulo', 'articulo.id', 'pedido_combinacion.articulo_id')
                        ->join('combinacion', 'combinacion.id', 'pedido_combinacion.combinacion_id')
                        ->leftjoin('materialavio', 'materialavio.articulo_id', 'avioart.material_id')
                        ->join('color', 'color.id', 'avioart.color_id')
                        ->join('linea', 'linea.id', 'articulo.linea_id')
                        ->join('talle', 'talle.id', 'pedido_combinacion_talle.talle_id')
                        ->leftjoin('fondo', 'fondo.id', 'combinacion.fondo_id')
                        ->join('cliente', 'cliente.id', 'pedido.cliente_id')
                        ->leftjoin('vendedor', 'vendedor.id', '=', 'pedido.vendedor_id')
                        ->whereBetween('pedido.fecha', [$desdefecha, $hastafecha])
                        ->whereBetween('pedido.cliente_id', [$desdecliente_id, $hastacliente_id])
                        ->whereBetween('avioart.material_id', [$desdematerialavio_id, $hastamaterialavio_id])
                        ->whereBetween('avioart.color_id', [$desdecolor_id, $hastacolor_id])
                        ->whereBetween('pedido_combinacion.articulo_id', [$desdearticulo_id, $hastaarticulo_id])
                        ->whereBetween('linea.id', [$desdelinea_id, $hastalinea_id])
                        ->where(function ($query) use ($tipoavio) {
                                if ($tipoavio != 'TODOS')
                                    $query->where('tipo', substr($tipoavio,0,1));
                            })
                        ->orderBy('avioart.material_id')
                        ->orderBy('color.nombre')
                        ->orderBy('avioart.tipo')
                        ->orderBy('pedido_combinacion.id')->get();

        return $pedido;
    }
}
