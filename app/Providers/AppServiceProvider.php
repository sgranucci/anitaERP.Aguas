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
        View::composer("theme.lte.aside", function ($view) {
            $menus = Menu::getMenu(true);
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
        	'App\Services\Configuracion\ImpuestoService',
    	);
    }
}
