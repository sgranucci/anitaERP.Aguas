<?php

namespace App\Traits\Ventas;

trait TipotransaccionTrait {

	public static $enumOperacion = [
		'V' => 'Venta',
		'C' => 'Devolución de venta',
		'E' => 'Entradas de stock',
		'S' => 'Salidas de stock'
		];
	
	public static $enumSigno = [
			'S' => 'Suma',
			'R' => 'Resta',
			];

	public static $enumEstado = [
			'A' => 'Activa',
			'S' => 'Suspendida',
			];
}
