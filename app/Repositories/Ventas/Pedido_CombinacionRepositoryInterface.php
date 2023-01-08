<?php

namespace App\Repositories\Ventas;

interface Pedido_CombinacionRepositoryInterface
{
    public function all();
	public function create(array $data, $pedido_id, $articulo_id, $combinacion_id, $numeroitem, $modulo_id, $cantidad, $precio, 
	  						$listaprecio_id, $incluyeimpuesto, $moneda_id, $descuento, $categoria_id, $subcategoria_id, $linea_id,
                            $ot_id, $observacion, $medidas, $lote_id, $funcion);
    public function update(array $data, $id);
    public function updatePorOtId($ot_id);
    public function delete($id);
    public function deleteporpedido($pedido_id);
    public function find($id);
    public function findOrFail($id);
    public function sincronizarConAnita();
	public function actualizarAnitaEstado($estado, $codigo, $orden);
	public function actualizarAnitaNroOt($nro_orden, $codigo, $orden);
}

