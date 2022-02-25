<?php

namespace App\Repositories\Ventas;

use App\Http\Requests\ValidacionCliente;

interface Cliente_ArchivoRepositoryInterface 
{

    public function create(array $data);
    public function update(ValidacionCliente $request, $id);
    public function find($id);
    public function findOrFail($id);
    public function delete($cliente_id, $codigo);
    public function sincronizarConAnita();

}

