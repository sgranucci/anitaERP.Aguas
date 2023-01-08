<?php

namespace App\Repositories\Ventas;

interface Cliente_EntregaRepositoryInterface 
{

    public function create(array $data);
    public function update(array $data, $id);
    public function find($id);
    public function findOrFail($id);
    public function delete($cliente_id, $codigo);
    public function sincronizarConAnita();

}

