<?php

namespace App\Repositories\Caja;

interface Caja_Movimiento_EstadoRepositoryInterface 
{

    public function create(array $data, $id);
    public function createUnique(array $data);
    public function update(array $data, $id);
    public function find($id);
    public function findOrFail($id);
    public function delete($asiento_id, $codigo);
}

