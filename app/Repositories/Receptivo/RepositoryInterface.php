<?php

namespace App\Repositories\Receptivo;

interface RepositoryInterface
{
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    public function findOrFail($id);
}

