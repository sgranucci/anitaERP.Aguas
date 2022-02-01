<?php

namespace App\Providers;

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
        	'App\Queries\Ventas\ClienteQueryInterface',
        	'App\Queries\Ventas\ClienteQuery',
    	);

	    $this->app->bind(
        	'App\Repositories\Ventas\TransporteRepositoryInterface',
        	'App\Repositories\Ventas\TransporteRepository',
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
        	'App\Services\Configuracion\IIBBService',
    	);
    }
}
