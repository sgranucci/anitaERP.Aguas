<?php

namespace App\Queries\Ventas;

use App\Models\Ventas\Ordentrabajo;
use App\ApiAnita;

class OrdentrabajoQuery implements OrdentrabajoQueryInterface
{
    protected $model;
	protected $tableAnita = ['ordtmae','ordtmov'];

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Ordentrabajo $cliente)
    {
        $this->model = $cliente;
    }

    public function first()
    {
        return $this->model->first();
    }

    public function all()
    {
        return $this->model->get();
    }

    public function allQuery(array $campos)
    {
        return $this->model->select($campos)->get();
    }

    public function traeOrdentrabajoPorId($id)
    {
        $apiAnita = new ApiAnita();
        $data = array( 
		  'acc' => 'list', 
            'campos' => '
    			ordtm_cliente,
    			ordtm_nro_orden,
    			ordtm_tipo,
    			ordtm_letra,
    			ordtm_sucursal,
    			ordtm_nro,
    			ordtm_nro_renglon,
    			ordtm_fecha,
    			ordtm_estado,
    			ordtm_observacion,
    			ordtm_alfa_cliente,
    			ordtm_articulo,
    			ordtm_color,
    			ordtm_forro,
    			ordtm_alfa_art,
    			ordtm_linea,
    			ordtm_fondo,
    			ordtm_color_fondo,
    			ordtm_capellada,
    			ordtm_color_cap,
    			ordtm_color_forro,
    			ordtm_tipo_fact,
    			ordtm_letra_fact,
    			ordtm_suc_fact,
    			ordtm_nro_fact,
    			ordtm_aplique,
    			ordtm_fl_impresa,
				ordtm_fl_stock,
				ordtv_articulo,
				ordtv_color,
				ordtv_medida,
				ordtv_cantidad,
				ordtv_forro
			',
		  	'tabla' => $this->tableAnita[1].", ".$this->tableAnita[0]." ",
			  'whereArmado' => " WHERE ordtv_nro_orden = '".$id."' 
			  						AND ordtv_nro_orden=ordtm_nro_orden"
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        return $dataAnita;
    }

    public function allOrdentrabajoPorEstado($estado){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita[0], 
            'campos' => '
    			ordtm_cliente,
    			ordtm_nro_orden,
    			ordtm_tipo,
    			ordtm_letra,
    			ordtm_sucursal,
    			ordtm_nro,
    			ordtm_nro_renglon,
    			ordtm_fecha,
    			ordtm_estado,
    			ordtm_observacion,
    			ordtm_alfa_cliente,
    			ordtm_articulo,
    			ordtm_color,
    			ordtm_forro,
    			ordtm_alfa_art,
    			ordtm_linea,
    			ordtm_fondo,
    			ordtm_color_fondo,
    			ordtm_capellada,
    			ordtm_color_cap,
    			ordtm_color_forro,
    			ordtm_tipo_fact,
    			ordtm_letra_fact,
    			ordtm_suc_fact,
    			ordtm_nro_fact,
    			ordtm_aplique,
    			ordtm_fl_impresa,
    			ordtm_fl_stock
			',
            'whereArmado' => " WHERE ordtm_fecha>20220100 and ordtm_estado = '".$estado."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		return($dataAnita);
	}

}

