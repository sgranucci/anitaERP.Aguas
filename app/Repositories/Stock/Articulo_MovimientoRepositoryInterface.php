<?php

namespace App\Repositories\Stock;

interface Articulo_MovimientoRepositoryInterface 
{

    public function all();
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    public function findPorArticuloCombinacion($articulo_id, $combinacion_id);
    public function findPorPedidoCombinacionId($pedido_combinacion_id);
    public function updatePorPedidoCombinacionId($pedido_combinacion_id, $data);

}

