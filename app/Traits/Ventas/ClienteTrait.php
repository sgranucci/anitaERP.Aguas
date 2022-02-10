<?php

namespace App\Traits\Ventas;

trait ClienteTrait {

	public static $enumRetieneiva = [
		'N' => 'No retiene iva',
		'S' => 'Retiene iva',
		];

	public static $enumCondicioniibb = [
		'L' => 'Local',
		'C' => 'Convenio',
		'E' => 'Exento',
		'N' => 'No retener',
		];

	public static $enumVaweb = [
		'S' => 'Si va a web',
		'N' => 'No va a web',
		];

	public static $enumEstado = [
		'0' => 'Activo',
		'1' => 'Suspendido',
		];
}
