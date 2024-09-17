<?php

namespace App\Repositories\Contable;

interface CuentacontableRepositoryInterface extends RepositoryInterface
{

    public function all();
    public function sincronizarConAnita();
    public function traerRegistroDeAnita($empresa, $key);
	public function guardarAnita($request);
	public function actualizarAnita($request);
	public function eliminarAnita($empresa, $id);
    public function findPorId($id);
    public function findPorCodigo($codigo);

}

