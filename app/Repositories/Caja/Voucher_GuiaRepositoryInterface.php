<?php

namespace App\Repositories\Caja;

interface Voucher_GuiaRepositoryInterface 
{

    public function create(array $data, $id);
    public function update(array $data, $id);
    public function find($id);
    public function findOrFail($id);
    public function delete($proveedor_id, $codigo);
    public function leeVoucherGuia($proveedor_id);
}

