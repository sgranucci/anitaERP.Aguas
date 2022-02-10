<?php

namespace App\Repositories\Ventas;

interface Pedido_CombinacionRepositoryInterface
{
    public function all();
	public function create(array $data, $pedido_id, $articulo_id, $combinacion_id, $numeroitem, $modulo_id, $cantidad, $precio, 
	  						$listaprecio_id, $incluyeimpuesto, $moneda_id, $descuento, $categoria_id, $subcategoria_id, $linea_id,
                            $ot_id, $observacion, $medidas, $funcion);
    public function delete($id);
    public function deleteporpedido($pedido_id, $tipo, $letra, $sucursal, $nro);
    public function find($id);
    public function findOrFail($id);
    public function sincronizarConAnita();
}

