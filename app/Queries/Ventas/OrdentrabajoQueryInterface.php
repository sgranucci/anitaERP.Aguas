<?php

namespace App\Queries\Ventas;

interface OrdentrabajoQueryInterface
{
    public function first();
    public function all();
    public function allQuery(array $campos);
    public function allOrdentrabajoPorEstado($estado);
    public function traeOrdentrabajoPorId($id);
}

