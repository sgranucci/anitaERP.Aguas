<?php

namespace App\Queries\Ventas;

interface PedidoQueryInterface
{
    public function all();
    public function first();
    public function allPendiente($cliente_id = null);
    public function leePedidoporCodigo($codigo);
    public function leePedidoporId($id);
}

