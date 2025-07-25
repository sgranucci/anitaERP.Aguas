<?php

namespace App\Providers;

use App\Observers\Ventas\Pedido_CombinacionObserver;
use App\Observers\Ventas\Ordentrabajo_TareaObserver;
use App\Observers\Ventas\Pedido_Combinacion_EstadoObserver;
use App\Models\Ventas\Pedido_Combinacion;
use App\Models\Ventas\Ordentrabajo_Tarea;
use App\Models\Ventas\Pedido_Combinacion_Estado;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Admin\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		Paginator::useBootstrap();

        View::composer("theme.lte.aside", function ($view) {
			$nivelActual = 0;
            $menus = Menu::getMenu(true, $nivelActual);
            $view->with('menusComposer', $menus);
        });
        View::share('theme', 'lte');

		Pedido_Combinacion::observe(Pedido_CombinacionObserver::class);
		Ordentrabajo_Tarea::observe(Ordentrabajo_TareaObserver::class);
		Pedido_Combinacion_Estado::observe(Pedido_Combinacion_EstadoObserver::class);
	}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
	    $this->app->bind(
        	'App\Repositories\Configuracion\RepositoryInterface',
        	'App\Repositories\Configuracion\CondicionivaRepository',
		);

	    $this->app->bind(
        	'App\Repositories\Ventas\ClienteRepositoryInterface',
        	'App\Repositories\Ventas\ClienteRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\Cliente_EntregaRepositoryInterface',
        	'App\Repositories\Ventas\Cliente_EntregaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\Cliente_ArchivoRepositoryInterface',
        	'App\Repositories\Ventas\Cliente_ArchivoRepository',
    	);

	    $this->app->bind(
        	'App\Queries\Ventas\ClienteQueryInterface',
        	'App\Queries\Ventas\ClienteQuery',
    	);

	    $this->app->bind(
        	'App\Queries\Ventas\Cliente_ComisionQueryInterface',
        	'App\Queries\Ventas\Cliente_ComisionQuery',
    	);

	    $this->app->bind(
        	'App\Queries\Ventas\Cliente_EntregaQueryInterface',
        	'App\Queries\Ventas\Cliente_EntregaQuery',
    	);

	    $this->app->bind(
        	'App\Queries\Ventas\OrdentrabajoQueryInterface',
        	'App\Queries\Ventas\OrdentrabajoQuery',
    	);

	    $this->app->bind(
        	'App\Queries\Contable\AsientoQueryInterface',
        	'App\Queries\Contable\AsientoQuery',
    	);

		$this->app->bind(
        	'App\Queries\Caja\Caja_MovimientoQueryInterface',
        	'App\Queries\Caja\Caja_MovimientoQuery',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\OrdentrabajoRepositoryInterface',
        	'App\Repositories\Ventas\OrdentrabajoRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\Ordentrabajo_Combinacion_TalleRepositoryInterface',
        	'App\Repositories\Ventas\Ordentrabajo_Combinacion_TalleRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\VentaRepositoryInterface',
        	'App\Repositories\Ventas\VentaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\Venta_EmisionRepositoryInterface',
        	'App\Repositories\Ventas\Venta_EmisionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\Venta_ImpuestoRepositoryInterface',
        	'App\Repositories\Ventas\Venta_ImpuestoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\Venta_ExportacionRepositoryInterface',
        	'App\Repositories\Ventas\Venta_ExportacionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\Cliente_CuentacorrienteRepositoryInterface',
        	'App\Repositories\Ventas\Cliente_CuentacorrienteRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\Ordentrabajo_TareaRepositoryInterface',
        	'App\Repositories\Ventas\Ordentrabajo_TareaRepository',
    	);

	    $this->app->bind(
        	'App\Queries\Stock\ArticuloQueryInterface',
        	'App\Queries\Stock\ArticuloQuery',
    	);

		$this->app->bind(
        	'App\Queries\Stock\Articulo_MovimientoQueryInterface',
        	'App\Queries\Stock\Articulo_MovimientoQuery',
    	);

	    $this->app->bind(
        	'App\Repositories\Stock\Articulo_CajaRepositoryInterface',
        	'App\Repositories\Stock\Articulo_CajaRepository',
    	);
		
		$this->app->bind(
        	'App\Repositories\Stock\LoteRepositoryInterface',
        	'App\Repositories\Stock\LoteRepository',
    	);
		
		$this->app->bind(
        	'App\Repositories\Stock\MovimientoStockRepositoryInterface',
        	'App\Repositories\Stock\MovimientoStockRepository',
    	);
	    
		$this->app->bind(
        	'App\Repositories\Stock\Articulo_CostoRepositoryInterface',
        	'App\Repositories\Stock\Articulo_CostoRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\TransporteRepositoryInterface',
        	'App\Repositories\Ventas\TransporteRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\MotivocierrepedidoRepositoryInterface',
        	'App\Repositories\Ventas\MotivocierrepedidoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\TiposuspensionclienteRepositoryInterface',
        	'App\Repositories\Ventas\TiposuspensionclienteRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\IncotermRepositoryInterface',
        	'App\Repositories\Ventas\IncotermRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\CuentacajaRepositoryInterface',
        	'App\Repositories\Caja\CuentacajaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\ConceptogastoRepositoryInterface',
        	'App\Repositories\Caja\ConceptogastoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Conceptogasto_CuentacontableRepositoryInterface',
        	'App\Repositories\Caja\Conceptogasto_CuentacontableRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\OrigenvoucherRepositoryInterface',
        	'App\Repositories\Caja\OrigenvoucherRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\TalonariovoucherRepositoryInterface',
        	'App\Repositories\Caja\TalonariovoucherRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\TalonariorendicionRepositoryInterface',
        	'App\Repositories\Caja\TalonariorendicionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\TipocuentacajaRepositoryInterface',
        	'App\Repositories\Caja\TipocuentacajaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\MediopagoRepositoryInterface',
        	'App\Repositories\Caja\MediopagoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\BancoRepositoryInterface',
        	'App\Repositories\Caja\BancoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\CajaRepositoryInterface',
        	'App\Repositories\Caja\CajaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\ChequeRepositoryInterface',
        	'App\Repositories\Caja\ChequeRepository',
    	);
		
		$this->app->bind(
        	'App\Repositories\Caja\ChequeraRepositoryInterface',
        	'App\Repositories\Caja\ChequeraRepository',
    	);
		
		$this->app->bind(
        	'App\Repositories\Caja\Caja_AsignacionRepositoryInterface',
        	'App\Repositories\Caja\Caja_AsignacionRepository',
    	);
		
		$this->app->bind(
        	'App\Queries\Caja\Caja_AsignacionQueryInterface',
        	'App\Queries\Caja\Caja_AsignacionQuery',
    	);
		
		$this->app->bind(
        	'App\Repositories\Caja\Tipotransaccion_CajaRepositoryInterface',
        	'App\Repositories\Caja\Tipotransaccion_CajaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Estadocheque_BancoRepositoryInterface',
        	'App\Repositories\Caja\Estadocheque_BancoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\CondicionpagoRepositoryInterface',
        	'App\Repositories\Compras\CondicionpagoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\CondicionpagocuotaRepositoryInterface',
        	'App\Repositories\Compras\CondicionpagocuotaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\CondicioncompraRepositoryInterface',
        	'App\Repositories\Compras\CondicioncompraRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\CondicionentregaRepositoryInterface',
        	'App\Repositories\Compras\CondicionentregaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\TipoempresaRepositoryInterface',
        	'App\Repositories\Compras\TipoempresaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\RetenciongananciaRepositoryInterface',
        	'App\Repositories\Compras\RetenciongananciaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Retencionganancia_EscalaRepositoryInterface',
        	'App\Repositories\Compras\Retencionganancia_EscalaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\RetencionivaRepositoryInterface',
        	'App\Repositories\Compras\RetencionivaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\RetencionsussRepositoryInterface',
        	'App\Repositories\Compras\RetencionsussRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\RetencionIIBBRepositoryInterface',
        	'App\Repositories\Compras\RetencionIIBBRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\RetencionIIBB_CondicionRepositoryInterface',
        	'App\Repositories\Compras\RetencionIIBB_CondicionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\TiposuspensionproveedorRepositoryInterface',
        	'App\Repositories\Compras\TiposuspensionproveedorRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\ProveedorRepositoryInterface',
        	'App\Repositories\Compras\ProveedorRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Proveedor_FormapagoRepositoryInterface',
        	'App\Repositories\Compras\Proveedor_FormapagoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Proveedor_ExclusionRepositoryInterface',
        	'App\Repositories\Compras\Proveedor_ExclusionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Proveedor_ArchivoRepositoryInterface',
        	'App\Repositories\Compras\Proveedor_ArchivoRepository',
    	);

		$this->app->bind(
        	'App\Queries\Compras\ProveedorQueryInterface',
        	'App\Queries\Compras\ProveedorQuery',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Columna_IvacompraRepositoryInterface',
        	'App\Repositories\Compras\Columna_IvacompraRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Concepto_IvacompraRepositoryInterface',
        	'App\Repositories\Compras\Concepto_IvacompraRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Concepto_Ivacompra_CondicionivaRepositoryInterface',
        	'App\Repositories\Compras\Concepto_Ivacompra_CondicionivaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Tipotransaccion_CompraRepositoryInterface',
        	'App\Repositories\Compras\Tipotransaccion_CompraRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Tipotransaccion_Compra_CentrocostoRepositoryInterface',
        	'App\Repositories\Compras\Tipotransaccion_Compra_CentrocostoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Compras\Tipotransaccion_Compra_Concepto_IvacompraRepositoryInterface',
        	'App\Repositories\Compras\Tipotransaccion_Compra_Concepto_IvacompraRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\FormapagoRepositoryInterface',
        	'App\Repositories\Ventas\FormapagoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\TipotransaccionRepositoryInterface',
        	'App\Repositories\Ventas\TipotransaccionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Ventas\PuntoventaRepositoryInterface',
        	'App\Repositories\Ventas\PuntoventaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Produccion\TareaRepositoryInterface',
        	'App\Repositories\Produccion\TareaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Produccion\OperacionRepositoryInterface',
        	'App\Repositories\Produccion\OperacionRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Produccion\EmpleadoRepositoryInterface',
        	'App\Repositories\Produccion\EmpleadoRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Produccion\MovimientoOrdentrabajoRepositoryInterface',
        	'App\Repositories\Produccion\MovimientoOrdentrabajoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\SalidaRepositoryInterface',
        	'App\Repositories\Configuracion\SalidaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\SeteosalidaRepositoryInterface',
        	'App\Repositories\Configuracion\SeteosalidaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Configuracion\PadronarbaRepositoryInterface',
        	'App\Repositories\Configuracion\PadronarbaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Configuracion\PadroncabaRepositoryInterface',
        	'App\Repositories\Configuracion\PadroncabaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\MonedaRepositoryInterface',
        	'App\Repositories\Configuracion\MonedaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\CotizacionRepositoryInterface',
        	'App\Repositories\Configuracion\CotizacionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\TipodocumentoRepositoryInterface',
        	'App\Repositories\Configuracion\TipodocumentoRepository',
    	);

		$this->app->bind(
        	'App\Queries\Configuracion\CotizacionQueryInterface',
        	'App\Queries\Configuracion\CotizacionQuery',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\Cotizacion_MonedaRepositoryInterface',
        	'App\Repositories\Configuracion\Cotizacion_MonedaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Stock\MaterialcapelladaRepositoryInterface',
        	'App\Repositories\Stock\MaterialcapelladaRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Stock\MaterialavioRepositoryInterface',
        	'App\Repositories\Stock\MaterialavioRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Stock\Articulo_MovimientoRepositoryInterface',
        	'App\Repositories\Stock\Articulo_MovimientoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Stock\Articulo_Movimiento_TalleRepositoryInterface',
        	'App\Repositories\Stock\Articulo_Movimiento_TalleRepository',
    	);

		$this->app->bind(
        	'App\Services\Ventas\PedidoService',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\PedidoRepositoryInterface',
        	'App\Repositories\Ventas\PedidoRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\Pedido_CombinacionRepositoryInterface',
        	'App\Repositories\Ventas\Pedido_CombinacionRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\Pedido_Combinacion_EstadoRepositoryInterface',
        	'App\Repositories\Ventas\Pedido_Combinacion_EstadoRepository',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\Pedido_Combinacion_TalleRepositoryInterface',
        	'App\Repositories\Ventas\Pedido_Combinacion_TalleRepository',
    	);

	    $this->app->bind(
        	'App\Queries\Ventas\PedidoQueryInterface',
        	'App\Queries\Ventas\PedidoQuery',
    	);

	    $this->app->bind(
        	'App\Queries\Ventas\Pedido_CombinacionQueryInterface',
        	'App\Queries\Ventas\Pedido_CombinacionQuery',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\CondicionivaRepositoryInterface',
        	'App\Repositories\Configuracion\CondicionivaRepository',
    	);

	    $this->app->bind(
        	'App\Services\Configuracion\IIBBService',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\CondicionIIBBRepositoryInterface',
        	'App\Repositories\Configuracion\CondicionIIBBRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\EmpresaRepositoryInterface',
        	'App\Repositories\Configuracion\EmpresaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\ProvinciaRepositoryInterface',
        	'App\Repositories\Configuracion\ProvinciaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Configuracion\ImpuestoRepositoryInterface',
        	'App\Repositories\Configuracion\ImpuestoRepository',
    	);

	    $this->app->bind(
        	'App\Services\Configuracion\ImpuestoService',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\CentrocostoRepositoryInterface',
        	'App\Repositories\Contable\CentrocostoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\CuentacontableRepositoryInterface',
        	'App\Repositories\Contable\CuentacontableRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\Cuentacontable_CentrocostoRepositoryInterface',
        	'App\Repositories\Contable\Cuentacontable_CentrocostoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\TipoasientoRepositoryInterface',
        	'App\Repositories\Contable\TipoasientoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\AsientoRepositoryInterface',
        	'App\Repositories\Contable\AsientoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\Asiento_MovimientoRepositoryInterface',
        	'App\Repositories\Contable\Asiento_MovimientoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\Asiento_ArchivoRepositoryInterface',
        	'App\Repositories\Contable\Asiento_ArchivoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Contable\Usuario_CuentacontableRepositoryInterface',
        	'App\Repositories\Contable\Usuario_CuentacontableRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\TiposervicioterrestreRepositoryInterface',
        	'App\Repositories\Receptivo\TiposervicioterrestreRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\ServicioterrestreRepositoryInterface',
        	'App\Repositories\Receptivo\ServicioterrestreRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\Proveedor_ServicioterrestreRepositoryInterface',
        	'App\Repositories\Receptivo\Proveedor_ServicioterrestreRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\IdiomaRepositoryInterface',
        	'App\Repositories\Receptivo\IdiomaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\MovilRepositoryInterface',
        	'App\Repositories\Receptivo\MovilRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\GuiaRepositoryInterface',
        	'App\Repositories\Receptivo\GuiaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\Guia_IdiomaRepositoryInterface',
        	'App\Repositories\Receptivo\Guia_IdiomaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\Comision_ServicioterrestreRepositoryInterface',
        	'App\Repositories\Receptivo\Comision_ServicioterrestreRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Receptivo\ReservaRepositoryInterface',
        	'App\Repositories\Receptivo\ReservaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\VoucherRepositoryInterface',
        	'App\Repositories\Caja\VoucherRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Voucher_GuiaRepositoryInterface',
        	'App\Repositories\Caja\Voucher_GuiaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Voucher_ReservaRepositoryInterface',
        	'App\Repositories\Caja\Voucher_ReservaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Voucher_FormapagoRepositoryInterface',
        	'App\Repositories\Caja\Voucher_FormapagoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\RendicionreceptivoRepositoryInterface',
        	'App\Repositories\Caja\RendicionreceptivoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Rendicionreceptivo_Caja_MovimientoRepositoryInterface',
        	'App\Repositories\Caja\Rendicionreceptivo_Caja_MovimientoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Rendicionreceptivo_VoucherRepositoryInterface',
        	'App\Repositories\Caja\Rendicionreceptivo_VoucherRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Rendicionreceptivo_FormapagoRepositoryInterface',
        	'App\Repositories\Caja\Rendicionreceptivo_FormapagoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Rendicionreceptivo_ComisionRepositoryInterface',
        	'App\Repositories\Caja\Rendicionreceptivo_ComisionRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Rendicionreceptivo_AdelantoRepositoryInterface',
        	'App\Repositories\Caja\Rendicionreceptivo_AdelantoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Caja_MovimientoRepositoryInterface',
        	'App\Repositories\Caja\Caja_MovimientoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Caja_Movimiento_EstadoRepositoryInterface',
        	'App\Repositories\Caja\Caja_Movimiento_EstadoRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Caja_Movimiento_CuentacajaRepositoryInterface',
        	'App\Repositories\Caja\Caja_Movimiento_CuentacajaRepository',
    	);

		$this->app->bind(
        	'App\Repositories\Caja\Caja_Movimiento_ArchivoRepositoryInterface',
        	'App\Repositories\Caja\Caja_Movimiento_ArchivoRepository',
    	);

    }
}
