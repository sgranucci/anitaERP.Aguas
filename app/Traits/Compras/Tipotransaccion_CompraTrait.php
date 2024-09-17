<?php

namespace App\Traits\Compras;

trait Tipotransaccion_CompraTrait {

	public static $enumOperacion = [
		'C' => 'Compra mercado local',
		'D' => 'Devolución mercado local',
		'E' => 'Importación',
		'V' => 'Devolución de importación',
		];
	
	public static $enumSigno = [
		'S' => 'Suma',
		'R' => 'Resta',
		];

	public static $enumSubdiario = [
		'C' => 'Compras',
		'N' => 'No va al subdiario',
		];

	public static $enumAsientoContable = [
		'S' => 'Genera asiento contable',
		'N' => 'No genera asiento contable',
		];
	
	public static $enumEstado = [
		'A' => 'Activa',
		'S' => 'Suspendida',
		];

	public static $enumRetiene = [
		'S' => 'Retiene',
		'N' => 'No retiene',
		];
}
