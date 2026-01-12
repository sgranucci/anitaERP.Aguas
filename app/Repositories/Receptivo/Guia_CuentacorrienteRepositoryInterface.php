<?php

namespace App\Repositories\Receptivo;

interface Guia_CuentacorrienteRepositoryInterface 
{

    public function create(array $data);
    public function update(array $data, $id, $campo);
    public function updateUnique(array $data, $id);
    public function find($id);
    public function findOrFail($id);
    public function findPorRendicionreceptivoId($rendicionreceptivo_id);
    public function delete($id);
    public function deletePorCajaMovimientoId($caja_movimiento_id);
    
}

