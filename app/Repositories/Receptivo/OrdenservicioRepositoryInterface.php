<?php

namespace App\Repositories\Receptivo;

interface OrdenservicioRepositoryInterface extends RepositoryInterface
{

    public function consultaOrdenservicio($consulta);
    public function leeUnaOrdenservicio($ordenservicio_id);
    
}

