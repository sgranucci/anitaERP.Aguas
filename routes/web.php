<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*RUTAS PASSWORD RESET*/

use Illuminate\Support\Facades\Route;

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::get('/', 'InicioController@index')->name('inicio');
Route::get('seguridad/login', 'Seguridad\LoginController@index')->name('login');
Route::post('seguridad/login', 'Seguridad\LoginController@login')->name('login_post');
Route::get('seguridad/logout', 'Seguridad\LoginController@logout')->name('logout');
Route::post('ajax-sesion', 'AjaxController@setSession')->name('ajax')->middleware('auth');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'superadmin']], function () {
    Route::get('', 'AdminController@index');
    /*RUTAS DE USUARIO*/
    Route::get('usuario', 'UsuarioController@index')->name('usuario');
    Route::get('usuario/crear', 'UsuarioController@crear')->name('crear_usuario');
    Route::post('usuario', 'UsuarioController@guardar')->name('guardar_usuario');
    Route::get('usuario/{id}/editar', 'UsuarioController@editar')->name('editar_usuario');
    Route::put('usuario/{id}', 'UsuarioController@actualizar')->name('actualizar_usuario');
    Route::delete('usuario/{id}', 'UsuarioController@eliminar')->name('eliminar_usuario');
    /*RUTAS DE PERMISO*/
    Route::get('permiso', 'PermisoController@index')->name('permiso');
    Route::get('permiso/crear', 'PermisoController@crear')->name('crear_permiso');
    Route::post('permiso', 'PermisoController@guardar')->name('guardar_permiso');
    Route::get('permiso/{id}/editar', 'PermisoController@editar')->name('editar_permiso');
    Route::put('permiso/{id}', 'PermisoController@actualizar')->name('actualizar_permiso');
    Route::delete('permiso/{id}', 'PermisoController@eliminar')->name('eliminar_permiso');
    /*RUTAS DEL MENU*/
    Route::get('menu', 'MenuController@index')->name('menu');
    Route::get('menu/crear', 'MenuController@crear')->name('crear_menu');
    Route::post('menu', 'MenuController@guardar')->name('guardar_menu');
    Route::get('menu/{id}/editar', 'MenuController@editar')->name('editar_menu');
    Route::put('menu/{id}', 'MenuController@actualizar')->name('actualizar_menu');
    Route::get('menu/{id}/eliminar', 'MenuController@eliminar')->name('eliminar_menu');
    Route::post('menu/guardar-orden', 'MenuController@guardarOrden')->name('guardar_orden');
    /*RUTAS ROL*/
    Route::get('rol', 'RolController@index')->name('rol');
    Route::get('rol/crear', 'RolController@crear')->name('crear_rol');
    Route::post('rol', 'RolController@guardar')->name('guardar_rol');
    Route::get('rol/{id}/editar', 'RolController@editar')->name('editar_rol');
    Route::put('rol/{id}', 'RolController@actualizar')->name('actualizar_rol');
    Route::delete('rol/{id}', 'RolController@eliminar')->name('eliminar_rol');
    /*RUTAS MENU_ROL*/
    Route::get('menu-rol', 'MenuRolController@index')->name('menu_rol');
    Route::post('menu-rol', 'MenuRolController@guardar')->name('guardar_menu_rol');
    /*RUTAS PERMISO_ROL*/
    Route::get('permiso-rol', 'PermisoRolController@index')->name('permiso_rol');
    Route::post('permiso-rol', 'PermisoRolController@guardar')->name('guardar_permiso_rol');
});

/* Rutas de configuracion */

/* 
 * Salidas
 */

 Route::get('configuracion/salida', 'Configuracion\SalidaController@index')->name('salida');
 Route::get('configuracion/salida/crear', 'Configuracion\SalidaController@crear')->name('crear_salida');
 Route::post('configuracion/salida', 'Configuracion\SalidaController@guardar')->name('guardar_salida');
 Route::get('configuracion/salida/{id}/editar', 'Configuracion\SalidaController@editar')->name('editar_salida');
 Route::put('configuracion/salida/{id}', 'Configuracion\SalidaController@actualizar')->name('actualizar_salida');
 Route::delete('configuracion/salida/{id}', 'Configuracion\SalidaController@eliminar')->name('eliminar_salida');
 Route::get('configuracion/configurarsalida/{programa?}', 'Configuracion\SalidaController@configurarSalida')->name('configurar_salida');
 Route::get('configuracion/setearsalida/{programa}/{salida}', 'Configuracion\SalidaController@setearSalida')->name('setear_salida');
 Route::get('configuracion/buscarsalida/{programa?}', 'Configuracion\SalidaController@buscarSalida')->name('buscar_salida');
 
/* 
 * Monedas
 */

Route::get('configuracion/moneda', 'Configuracion\MonedaController@index')->name('moneda');
Route::get('configuracion/moneda/crear', 'Configuracion\MonedaController@crear')->name('crear_moneda');
Route::post('configuracion/moneda', 'Configuracion\MonedaController@guardar')->name('guardar_moneda');
Route::get('configuracion/moneda/{id}/editar', 'Configuracion\MonedaController@editar')->name('editar_moneda');
Route::put('configuracion/moneda/{id}', 'Configuracion\MonedaController@actualizar')->name('actualizar_moneda');
Route::delete('configuracion/moneda/{id}', 'Configuracion\MonedaController@eliminar')->name('eliminar_moneda');
Route::get('configuracion/leermoneda', 'Configuracion\MonedaController@leerMoneda')->name('leer_moneda');

/* 
 * Cotizacion
 */

 Route::get('configuracion/cotizacion', 'Configuracion\CotizacionController@index')->name('cotizacion');
 Route::get('configuracion/cotizacion/crear', 'Configuracion\CotizacionController@crear')->name('crear_cotizacion');
 Route::post('configuracion/cotizacion', 'Configuracion\CotizacionController@guardar')->name('guardar_cotizacion');
 Route::get('configuracion/cotizacion/{id}/editar', 'Configuracion\CotizacionController@editar')->name('editar_cotizacion');
 Route::put('configuracion/cotizacion/{id}', 'Configuracion\CotizacionController@actualizar')->name('actualizar_cotizacion');
 Route::delete('configuracion/cotizacion/{id}', 'Configuracion\CotizacionController@eliminar')->name('eliminar_cotizacion');
 Route::get('configuracion/cotizacion/{formato?}/{busqueda?}', 'Configuracion\CotizacionController@listar')->name('lista_cotizacion');
 Route::get('configuracion/leercotizacion/{fecha}/{moneda_id}', 'Configuracion\CotizacionController@leeCotizacionDiaria')->name('leer_cotizacion');
/* 
 * Paises
 */

Route::get('configuracion/pais', 'Configuracion\PaisController@index')->name('pais');
Route::get('configuracion/pais/crear', 'Configuracion\PaisController@crear')->name('crear_pais');
Route::post('configuracion/pais', 'Configuracion\PaisController@guardar')->name('guardar_pais');
Route::get('configuracion/pais/{id}/editar', 'Configuracion\PaisController@editar')->name('editar_pais');
Route::put('configuracion/pais/{id}', 'Configuracion\PaisController@actualizar')->name('actualizar_pais');
Route::delete('configuracion/pais/{id}', 'Configuracion\PaisController@eliminar')->name('eliminar_pais');

/* 
 * Provincias
 */

Route::get('configuracion/provincia', 'Configuracion\ProvinciaController@index')->name('provincia');
Route::get('configuracion/provincia/crear', 'Configuracion\ProvinciaController@crear')->name('crear_provincia');
Route::post('configuracion/provincia', 'Configuracion\ProvinciaController@guardar')->name('guardar_provincia');
Route::get('configuracion/provincia/{id}/editar', 'Configuracion\ProvinciaController@editar')->name('editar_provincia');
Route::put('configuracion/provincia/{id}', 'Configuracion\ProvinciaController@actualizar')->name('actualizar_provincia');
Route::delete('configuracion/provincia/{id}', 'Configuracion\ProvinciaController@eliminar')->name('eliminar_provincia');

/* 
 * Localidades
 */

Route::get('configuracion/localidad', 'Configuracion\LocalidadController@index')->name('localidad');
Route::get('configuracion/localidad/crear', 'Configuracion\LocalidadController@crear')->name('crear_localidad');
Route::post('configuracion/localidad', 'Configuracion\LocalidadController@guardar')->name('guardar_localidad');
Route::get('configuracion/localidad/{id}/editar', 'Configuracion\LocalidadController@editar')->name('editar_localidad');
Route::put('configuracion/localidad/{id}', 'Configuracion\LocalidadController@actualizar')->name('actualizar_localidad');
Route::delete('configuracion/localidad/{id}', 'Configuracion\LocalidadController@eliminar')->name('eliminar_localidad');
Route::get('configuracion/leerlocalidades/{id}', 'Configuracion\LocalidadController@leerLocalidades')->name('leer_localidad');
Route::get('configuracion/leercodigopostal/{id}', 'Configuracion\LocalidadController@leerCodigoPostal')->name('leer_codigo_postal');

/* 
 * Condiciones de iva
 */

Route::get('configuracion/condicioniva', 'Configuracion\CondicionivaController@index')->name('condicioniva');
Route::get('configuracion/condicioniva/crear', 'Configuracion\CondicionivaController@crear')->name('crear_condicioniva');
Route::post('configuracion/condicioniva', 'Configuracion\CondicionivaController@guardar')->name('guardar_condicioniva');
Route::get('configuracion/condicioniva/{id}/editar', 'Configuracion\CondicionivaController@editar')->name('editar_condicioniva');
Route::put('configuracion/condicioniva/{id}', 'Configuracion\CondicionivaController@actualizar')->name('actualizar_condicioniva');
Route::delete('configuracion/condicioniva/{id}', 'Configuracion\CondicionivaController@eliminar')->name('eliminar_condicioniva');

/* 
 * Condiciones de IIBB
 */

 Route::get('configuracion/condicionIIBB', 'Configuracion\CondicionIIBBController@index')->name('condicionIIBB');
 Route::get('configuracion/condicionIIBB/crear', 'Configuracion\CondicionIIBBController@crear')->name('crear_condicionIIBB');
 Route::post('configuracion/condicionIIBB', 'Configuracion\CondicionIIBBController@guardar')->name('guardar_condicionIIBB');
 Route::get('configuracion/condicionIIBB/{id}/editar', 'Configuracion\CondicionIIBBController@editar')->name('editar_condicionIIBB');
 Route::put('configuracion/condicionIIBB/{id}', 'Configuracion\CondicionIIBBController@actualizar')->name('actualizar_condicionIIBB');
 Route::delete('configuracion/condicionIIBB/{id}', 'Configuracion\CondicionIIBBController@eliminar')->name('eliminar_condicionIIBB');
 
/* 
 * Fondos
 */

Route::get('stock/fondo', 'Stock\FondoController@index')->name('fondo');
Route::get('stock/fondo/crear', 'Stock\FondoController@crear')->name('crear_fondo');
Route::post('stock/fondo', 'Stock\FondoController@guardar')->name('guardar_fondo');
Route::get('stock/fondo/{id}/editar', 'Stock\FondoController@editar')->name('editar_fondo');
Route::put('stock/fondo/{id}', 'Stock\FondoController@actualizar')->name('actualizar_fondo');
Route::delete('stock/fondo/{id}', 'Stock\FondoController@eliminar')->name('eliminar_fondo');

/* 
 * Forro
 */

Route::get('stock/forro', 'Stock\ForroController@index')->name('forro');
Route::get('stock/forro/crear', 'Stock\ForroController@crear')->name('crear_forro');
Route::post('stock/forro', 'Stock\ForroController@guardar')->name('guardar_forro');
Route::get('stock/forro/{id}/editar', 'Stock\ForroController@editar')->name('editar_forro');
Route::put('stock/forro/{id}', 'Stock\ForroController@actualizar')->name('actualizar_forro');
Route::delete('stock/forro/{id}', 'Stock\ForroController@eliminar')->name('eliminar_forro');

/* 
 * Subcategorias
 */

Route::get('stock/subcategoria', 'Stock\SubcategoriaController@index')->name('subcategoria');
Route::get('stock/subcategoria/crear', 'Stock\SubcategoriaController@crear')->name('crear_subcategoria');
Route::post('stock/subcategoria', 'Stock\SubcategoriaController@guardar')->name('guardar_subcategoria');
Route::get('stock/subcategoria/{id}/editar', 'Stock\SubcategoriaController@editar')->name('editar_subcategoria');
Route::put('stock/subcategoria/{id}', 'Stock\SubcategoriaController@actualizar')->name('actualizar_subcategoria');
Route::delete('stock/subcategoria/{id}', 'Stock\SubcategoriaController@eliminar')->name('eliminar_subcategoria');

/* 
 * Marcas de venta
 */

Route::get('stock/mventa', 'Stock\MventaController@index')->name('mventa');
Route::get('stock/mventa/crear', 'Stock\MventaController@crear')->name('crear_mventa');
Route::post('stock/mventa', 'Stock\MventaController@guardar')->name('guardar_mventa');
Route::get('stock/mventa/{id}/editar', 'Stock\MventaController@editar')->name('editar_mventa');
Route::put('stock/mventa/{id}', 'Stock\MventaController@actualizar')->name('actualizar_mventa');
Route::delete('stock/mventa/{id}', 'Stock\MventaController@eliminar')->name('eliminar_mventa');

/* 
 * Depositos
 */

Route::get('stock/depmae', 'Stock\DepmaeController@index')->name('depmae');
Route::get('stock/depmae/crear', 'Stock\DepmaeController@crear')->name('crear_depmae');
Route::post('stock/depmae', 'Stock\DepmaeController@guardar')->name('guardar_depmae');
Route::get('stock/depmae/{id}/editar', 'Stock\DepmaeController@editar')->name('editar_depmae');
Route::put('stock/depmae/{id}', 'Stock\DepmaeController@actualizar')->name('actualizar_depmae');
Route::delete('stock/depmae/{id}', 'Stock\DepmaeController@eliminar')->name('eliminar_depmae');

/* 
 * Numeracion
 */

Route::get('stock/numeracion', 'Stock\NumeracionController@index')->name('numeracion');
Route::get('stock/numeracion/crear', 'Stock\NumeracionController@crear')->name('crear_numeracion');
Route::post('stock/numeracion', 'Stock\NumeracionController@guardar')->name('guardar_numeracion');
Route::get('stock/numeracion/{id}/editar', 'Stock\NumeracionController@editar')->name('editar_numeracion');
Route::put('stock/numeracion/{id}', 'Stock\NumeracionController@actualizar')->name('actualizar_numeracion');
Route::delete('stock/numeracion/{id}', 'Stock\NumeracionController@eliminar')->name('eliminar_numeracion');

/* 
 * Hormas
 */

Route::get('stock/horma', 'Stock\HormaController@index')->name('horma');
Route::get('stock/horma/crear', 'Stock\HormaController@crear')->name('crear_horma');
Route::post('stock/horma', 'Stock\HormaController@guardar')->name('guardar_horma');
Route::get('stock/horma/{id}/editar', 'Stock\HormaController@editar')->name('editar_horma');
Route::put('stock/horma/{id}', 'Stock\HormaController@actualizar')->name('actualizar_horma');
Route::delete('stock/horma/{id}', 'Stock\HormaController@eliminar')->name('eliminar_horma');

/* 
 * Plantillas de armado
 */

Route::get('stock/plarmado', 'Stock\PlarmadoController@index')->name('plarmado');
Route::get('stock/plarmado/crear', 'Stock\PlarmadoController@crear')->name('crear_plarmado');
Route::post('stock/plarmado', 'Stock\PlarmadoController@guardar')->name('guardar_plarmado');
Route::get('stock/plarmado/{id}/editar', 'Stock\PlarmadoController@editar')->name('editar_plarmado');
Route::put('stock/plarmado/{id}', 'Stock\PlarmadoController@actualizar')->name('actualizar_plarmado');
Route::delete('stock/plarmado/{id}', 'Stock\PlarmadoController@eliminar')->name('eliminar_plarmado');

/* 
 * Colores
 */

Route::get('stock/color', 'Stock\ColorController@index')->name('color');
Route::get('stock/color/crear', 'Stock\ColorController@crear')->name('crear_color');
Route::post('stock/color', 'Stock\ColorController@guardar')->name('guardar_color');
Route::get('stock/color/{id}/editar', 'Stock\ColorController@editar')->name('editar_color');
Route::put('stock/color/{id}', 'Stock\ColorController@actualizar')->name('actualizar_color');
Route::delete('stock/color/{id}', 'Stock\ColorController@eliminar')->name('eliminar_color');

/* 
 * Composicion de fondos
 */

Route::get('stock/compfondo', 'Stock\CompfondoController@index')->name('compfondo');
Route::get('stock/compfondo/crear', 'Stock\CompfondoController@crear')->name('crear_compfondo');
Route::post('stock/compfondo', 'Stock\CompfondoController@guardar')->name('guardar_compfondo');
Route::get('stock/compfondo/{id}/editar', 'Stock\CompfondoController@editar')->name('editar_compfondo');
Route::put('stock/compfondo/{id}', 'Stock\CompfondoController@actualizar')->name('actualizar_compfondo');
Route::delete('stock/compfondo/{id}', 'Stock\CompfondoController@eliminar')->name('eliminar_compfondo');

/* 
 * Tipo de cortes
 */

Route::get('stock/tipocorte', 'Stock\TipocorteController@index')->name('tipocorte');
Route::get('stock/tipocorte/crear', 'Stock\TipocorteController@crear')->name('crear_tipocorte');
Route::post('stock/tipocorte', 'Stock\TipocorteController@guardar')->name('guardar_tipocorte');
Route::get('stock/tipocorte/{id}/editar', 'Stock\TipocorteController@editar')->name('editar_tipocorte');
Route::put('stock/tipocorte/{id}', 'Stock\TipocorteController@actualizar')->name('actualizar_tipocorte');
Route::delete('stock/tipocorte/{id}', 'Stock\TipocorteController@eliminar')->name('eliminar_tipocorte');

/* 
 * Materiales
 */

Route::get('stock/material', 'Stock\MaterialController@index')->name('material');
Route::get('stock/material/crear', 'Stock\MaterialController@crear')->name('crear_material');
Route::post('stock/material', 'Stock\MaterialController@guardar')->name('guardar_material');
Route::get('stock/material/{id}/editar', 'Stock\MaterialController@editar')->name('editar_material');
Route::put('stock/material/{id}', 'Stock\MaterialController@actualizar')->name('actualizar_material');
Route::delete('stock/material/{id}', 'Stock\MaterialController@eliminar')->name('eliminar_material');

/* 
 * Serigrafias
 */

Route::get('stock/serigrafia', 'Stock\SerigrafiaController@index')->name('serigrafia');
Route::get('stock/serigrafia/crear', 'Stock\SerigrafiaController@crear')->name('crear_serigrafia');
Route::post('stock/serigrafia', 'Stock\SerigrafiaController@guardar')->name('guardar_serigrafia');
Route::get('stock/serigrafia/{id}/editar', 'Stock\SerigrafiaController@editar')->name('editar_serigrafia');
Route::put('stock/serigrafia/{id}', 'Stock\SerigrafiaController@actualizar')->name('actualizar_serigrafia');
Route::delete('stock/serigrafia/{id}', 'Stock\SerigrafiaController@eliminar')->name('eliminar_serigrafia');

/* 
 * Materiales de capelladas
 */

Route::get('stock/materialcapellada', 'Stock\MaterialcapelladaController@index')->name('materialcapellada');
Route::get('stock/materialcapellada/crear', 'Stock\MaterialcapelladaController@crear')->name('crear_materialcapellada');
Route::post('stock/materialcapellada', 'Stock\MaterialcapelladaController@guardar')->name('guardar_materialcapellada');
Route::get('stock/materialcapellada/{id}/editar', 'Stock\MaterialcapelladaController@editar')->name('editar_materialcapellada');
Route::put('stock/materialcapellada/{id}', 'Stock\MaterialcapelladaController@actualizar')->name('actualizar_materialcapellada');
Route::delete('stock/materialcapellada/{id}', 'Stock\MaterialcapelladaController@eliminar')->name('eliminar_materialcapellada');

/* 
 * Materiales de avios
 */

Route::get('stock/materialavio', 'Stock\MaterialavioController@index')->name('materialavio');
Route::get('stock/materialavio/crear', 'Stock\MaterialavioController@crear')->name('crear_materialavio');
Route::post('stock/materialavio', 'Stock\MaterialavioController@guardar')->name('guardar_materialavio');
Route::get('stock/materialavio/{id}/editar', 'Stock\MaterialavioController@editar')->name('editar_materialavio');
Route::put('stock/materialavio/{id}', 'Stock\MaterialavioController@actualizar')->name('actualizar_materialavio');
Route::delete('stock/materialavio/{id}', 'Stock\MaterialavioController@eliminar')->name('eliminar_materialavio');

/* 
 * Talles
 */

Route::get('stock/talle', 'Stock\TalleController@index')->name('talle');
Route::get('stock/talle/crear', 'Stock\TalleController@crear')->name('crear_talle');
Route::post('stock/talle', 'Stock\TalleController@guardar')->name('guardar_talle');
Route::get('stock/talle/{id}/editar', 'Stock\TalleController@editar')->name('editar_talle');
Route::put('stock/talle/{id}', 'Stock\TalleController@actualizar')->name('actualizar_talle');
Route::delete('stock/talle/{id}', 'Stock\TalleController@eliminar')->name('eliminar_talle');

/* 
 * Modulos
 */

Route::get('stock/modulo', 'Stock\ModuloController@index')->name('modulo');
Route::get('stock/modulo/crear', 'Stock\ModuloController@crear')->name('crear_modulo');
Route::post('stock/modulo', 'Stock\ModuloController@guardar')->name('guardar_modulo');
Route::get('stock/modulo/{id}/editar', 'Stock\ModuloController@editar')->name('editar_modulo');
Route::put('stock/modulo/{id}', 'Stock\ModuloController@actualizar')->name('actualizar_modulo');
Route::delete('stock/modulo/{id}', 'Stock\ModuloController@eliminar')->name('eliminar_modulo');

/* 
 * Tipo de articulos
 */

Route::get('stock/tipoarticulo', 'Stock\TipoarticuloController@index')->name('tipoarticulo');
Route::get('stock/tipoarticulo/crear', 'Stock\TipoarticuloController@crear')->name('crear_tipoarticulo');
Route::post('stock/tipoarticulo', 'Stock\TipoarticuloController@guardar')->name('guardar_tipoarticulo');
Route::get('stock/tipoarticulo/{id}/editar', 'Stock\TipoarticuloController@editar')->name('editar_tipoarticulo');
Route::put('stock/tipoarticulo/{id}', 'Stock\TipoarticuloController@actualizar')->name('actualizar_tipoarticulo');
Route::delete('stock/tipoarticulo/{id}', 'Stock\TipoarticuloController@eliminar')->name('eliminar_tipoarticulo');

/* 
 * Categorias
 */

Route::get('stock/categoria', 'Stock\CategoriaController@index')->name('categoria');
Route::get('stock/categoria/crear', 'Stock\CategoriaController@crear')->name('crear_categoria');
Route::post('stock/categoria', 'Stock\CategoriaController@guardar')->name('guardar_categoria');
Route::get('stock/categoria/{id}/editar', 'Stock\CategoriaController@editar')->name('editar_categoria');
Route::put('stock/categoria/{id}', 'Stock\CategoriaController@actualizar')->name('actualizar_categoria');
Route::delete('stock/categoria/{id}', 'Stock\CategoriaController@eliminar')->name('eliminar_categoria');

/* 
 * Listas de precio
 */

Route::get('stock/listaprecio', 'Stock\ListaprecioController@index')->name('listaprecio');
Route::get('stock/listaprecio/crear', 'Stock\ListaprecioController@crear')->name('crear_listaprecio');
Route::post('stock/listaprecio', 'Stock\ListaprecioController@guardar')->name('guardar_listaprecio');
Route::get('stock/listaprecio/{id}/editar', 'Stock\ListaprecioController@editar')->name('editar_listaprecio');
Route::put('stock/listaprecio/{id}', 'Stock\ListaprecioController@actualizar')->name('actualizar_listaprecio');
Route::delete('stock/listaprecio/{id}', 'Stock\ListaprecioController@eliminar')->name('eliminar_listaprecio');

/* 
 * Tipos de numeracion
 */

Route::get('stock/tiponumeracion', 'Stock\TiponumeracionController@index')->name('tiponumeracion');
Route::get('stock/tiponumeracion/crear', 'Stock\TiponumeracionController@crear')->name('crear_tiponumeracion');
Route::post('stock/tiponumeracion', 'Stock\TiponumeracionController@guardar')->name('guardar_tiponumeracion');
Route::get('stock/tiponumeracion/{id}/editar', 'Stock\TiponumeracionController@editar')->name('editar_tiponumeracion');
Route::put('stock/tiponumeracion/{id}', 'Stock\TiponumeracionController@actualizar')->name('actualizar_tiponumeracion');
Route::delete('stock/tiponumeracion/{id}', 'Stock\TiponumeracionController@eliminar')->name('eliminar_tiponumeracion');

/* 
 * Lineas
 */

Route::get('stock/linea', 'Stock\LineaController@index')->name('linea');
Route::get('stock/linea/crear', 'Stock\LineaController@crear')->name('crear_linea');
Route::post('stock/linea', 'Stock\LineaController@guardar')->name('guardar_linea');
Route::get('stock/linea/{id}/editar', 'Stock\LineaController@editar')->name('editar_linea');
Route::put('stock/linea/{id}', 'Stock\LineaController@actualizar')->name('actualizar_linea');
Route::delete('stock/linea/{id}', 'Stock\LineaController@eliminar')->name('eliminar_linea');

/* 
 * Precios
 */

Route::get('stock/precio', 'Stock\PrecioController@index')->name('precio');
Route::get('stock/precio/crear', 'Stock\PrecioController@crear')->name('crear_precio');
Route::post('stock/precio', 'Stock\PrecioController@guardar')->name('guardar_precio');
Route::get('stock/precio/{id}/editar', 'Stock\PrecioController@editar')->name('editar_precio');
Route::put('stock/precio/{id}', 'Stock\PrecioController@actualizar')->name('actualizar_precio');
Route::delete('stock/precio/{id}', 'Stock\PrecioController@eliminar')->name('eliminar_precio');
Route::get('stock/asignaprecio/{id}/{talle?}', 'Stock\PrecioController@asignaPrecio')->name('asigna_precio');
Route::get('stock/precio/crearimportacionprecio', 'Stock\PrecioController@crearImportacion')->name('crear_importacion_precio');
Route::post('stock/importarprecio', 'Stock\PrecioController@importar')->name('importar_precio');
Route::post('stock/precio/limpiafiltro', 'Stock\PrecioController@limpiafiltro')->name('precio.limpiafiltro');

/* 
 * Tienda nube
 */

 Route::get('stock/crearimportaciontiendanube', 'Stock\TiendaNubeController@crearImportacion')->name('crear_importacion_tiendanube');
 Route::post('stock/importartiendanube', 'Stock\TiendaNubeController@importar')->name('importar_tiendanube');
 Route::get('ventas/crearimportacionfacturastiendanube', 'Ventas\FacturanteController@crearImportacion')->name('crear_importacion_facturas_tiendanube');
 Route::post('ventas/listarfacturastiendanube', 'Ventas\FacturanteController@listarComprobanteFull')->name('listar_facturas_tiendanube');
 Route::post('ventas/generarfacturastiendanube', 'Ventas\FacturanteController@generarFacturasTiendaNube')->name('generar_facturas_tiendanube');

/* 
 * Unidades de medida
 */

Route::get('stock/unidadmedida', 'Stock\UnidadmedidaController@index')->name('unidadmedida');
Route::get('stock/unidadmedida/crear', 'Stock\UnidadmedidaController@crear')->name('crear_unidadmedida');
Route::post('stock/unidadmedida', 'Stock\UnidadmedidaController@guardar')->name('guardar_unidadmedida');
Route::get('stock/unidadmedida/{id}/editar', 'Stock\UnidadmedidaController@editar')->name('editar_unidadmedida');
Route::put('stock/unidadmedida/{id}', 'Stock\UnidadmedidaController@actualizar')->name('actualizar_unidadmedida');
Route::delete('stock/unidadmedida/{id}', 'Stock\UnidadmedidaController@eliminar')->name('eliminar_unidadmedida');

/* 
 * Uso de articulos
 */

Route::get('stock/usoarticulo', 'Stock\UsoarticuloController@index')->name('usoarticulo');
Route::get('stock/usoarticulo/crear', 'Stock\UsoarticuloController@crear')->name('crear_usoarticulo');
Route::post('stock/usoarticulo', 'Stock\UsoarticuloController@guardar')->name('guardar_usoarticulo');
Route::get('stock/usoarticulo/{id}/editar', 'Stock\UsoarticuloController@editar')->name('editar_usoarticulo');
Route::put('stock/usoarticulo/{id}', 'Stock\UsoarticuloController@actualizar')->name('actualizar_usoarticulo');
Route::delete('stock/usoarticulo/{id}', 'Stock\UsoarticuloController@eliminar')->name('eliminar_usoarticulo');

/* 
 * Punteras
 */

Route::get('stock/puntera', 'Stock\PunteraController@index')->name('puntera');
Route::get('stock/puntera/crear', 'Stock\PunteraController@crear')->name('crear_puntera');
Route::post('stock/puntera', 'Stock\PunteraController@guardar')->name('guardar_puntera');
Route::get('stock/puntera/{id}/editar', 'Stock\PunteraController@editar')->name('editar_puntera');
Route::put('stock/puntera/{id}', 'Stock\PunteraController@actualizar')->name('actualizar_puntera');
Route::delete('stock/puntera/{id}', 'Stock\PunteraController@eliminar')->name('eliminar_puntera');

/* 
 * Contrafuertes
 */

Route::get('stock/contrafuerte', 'Stock\ContrafuerteController@index')->name('contrafuerte');
Route::get('stock/contrafuerte/crear', 'Stock\ContrafuerteController@crear')->name('crear_contrafuerte');
Route::post('stock/contrafuerte', 'Stock\ContrafuerteController@guardar')->name('guardar_contrafuerte');
Route::get('stock/contrafuerte/{id}/editar', 'Stock\ContrafuerteController@editar')->name('editar_contrafuerte');
Route::put('stock/contrafuerte/{id}', 'Stock\ContrafuerteController@actualizar')->name('actualizar_contrafuerte');
Route::delete('stock/contrafuerte/{id}', 'Stock\ContrafuerteController@eliminar')->name('eliminar_contrafuerte');

/* 
 * Plantillas a la vista
 */

Route::get('stock/plvista', 'Stock\PlvistaController@index')->name('plvista');
Route::get('stock/plvista/crear', 'Stock\PlvistaController@crear')->name('crear_plvista');
Route::post('stock/plvista', 'Stock\PlvistaController@guardar')->name('guardar_plvista');
Route::get('stock/plvista/{id}/editar', 'Stock\PlvistaController@editar')->name('editar_plvista');
Route::put('stock/plvista/{id}', 'Stock\PlvistaController@actualizar')->name('actualizar_plvista');
Route::delete('stock/plvista/{id}', 'Stock\PlvistaController@eliminar')->name('eliminar_plvista');

/* 
 * Caja
 */

Route::get('stock/caja', 'Stock\CajaController@index')->name('caja');
Route::get('stock/caja/crear', 'Stock\CajaController@crear')->name('crear_caja');
Route::post('stock/caja', 'Stock\CajaController@guardar')->name('guardar_caja');
Route::get('stock/caja/{id}/editar', 'Stock\CajaController@editar')->name('editar_caja');
Route::put('stock/caja/{id}', 'Stock\CajaController@actualizar')->name('actualizar_caja');
Route::delete('stock/caja/{id}', 'Stock\CajaController@eliminar')->name('eliminar_caja');

/* 
 * Lotes de stock
 */

 Route::get('stock/lote', 'Stock\LoteController@index')->name('lote');
 Route::get('stock/lote/crear', 'Stock\LoteController@crear')->name('crear_lote');
 Route::post('stock/lote', 'Stock\LoteController@guardar')->name('guardar_lote');
 Route::get('stock/lote/{id}/editar', 'Stock\LoteController@editar')->name('editar_lote');
 Route::put('stock/lote/{id}', 'Stock\LoteController@actualizar')->name('actualizar_lote');
 Route::delete('stock/lote/{id}', 'Stock\LoteController@eliminar')->name('eliminar_lote');
 
// Reportes de stock

// Catalogo de productos
Route::get('stock/catalogo', 'Stock\CombinacionController@catalogo')->name('catalogo');
Route::post('stock/crearCatalogo', 'Stock\CombinacionController@crearCatalogo')->name('crear_catalogo');

// Combinaciones
Route::get('stock/repcombinacion', 'Stock\RepCombinacionController@index')->name('rep_combinacion');
Route::post('stock/crearrepcombinacion', 'Stock\RepCombinacionController@crearReporteCombinacion')->name('crear_repcombinacion');

// Stock OT
Route::get('stock/repstockot', 'Stock\RepStockOtController@index')->name('rep_stockot');
Route::post('stock/crearrepstockot', 'Stock\RepStockOtController@crearReporteStockOt')->name('crear_repstockot');

// Stock Listas de Precio
Route::get('stock/replistaprecio', 'Stock\RepListaPrecioController@index')->name('rep_listaprecio');
Route::post('stock/crearreplistaprecio', 'Stock\RepListaPrecioController@crearReporteListaPrecio')->name('crear_replistaprecio');

/* 
 * Impuestos
 */

Route::get('configuracion/impuesto', 'Configuracion\ImpuestoController@index')->name('impuesto');
Route::get('configuracion/impuesto/crear', 'Configuracion\ImpuestoController@crear')->name('crear_impuesto');
Route::post('configuracion/impuesto', 'Configuracion\ImpuestoController@guardar')->name('guardar_impuesto');
Route::get('configuracion/impuesto/{id}/editar', 'Configuracion\ImpuestoController@editar')->name('editar_impuesto');
Route::put('configuracion/impuesto/{id}', 'Configuracion\ImpuestoController@actualizar')->name('actualizar_impuesto');
Route::delete('configuracion/impuesto/{id}', 'Configuracion\ImpuestoController@eliminar')->name('eliminar_impuesto');

/* 
 * Empresas
 */

Route::get('configuracion/empresa', 'Configuracion\EmpresaController@index')->name('empresa');
Route::get('configuracion/empresa/crear', 'Configuracion\EmpresaController@crear')->name('crear_empresa');
Route::post('configuracion/empresa', 'Configuracion\EmpresaController@guardar')->name('guardar_empresa');
Route::get('configuracion/empresa/{id}/editar', 'Configuracion\EmpresaController@editar')->name('editar_empresa');
Route::put('configuracion/empresa/{id}', 'Configuracion\EmpresaController@actualizar')->name('actualizar_empresa');
Route::delete('configuracion/empresa/{id}', 'Configuracion\EmpresaController@eliminar')->name('eliminar_empresa');

/* 
 * Rubros contables
 */

Route::get('contable/rubrocontable', 'Contable\RubrocontableController@index')->name('rubrocontable');
Route::get('contable/rubrocontable/crear', 'Contable\RubrocontableController@crear')->name('crear_rubrocontable');
Route::post('contable/rubrocontable', 'Contable\RubrocontableController@guardar')->name('guardar_rubrocontable');
Route::get('contable/rubrocontable/{id}/editar', 'Contable\RubrocontableController@editar')->name('editar_rubrocontable');
Route::put('contable/rubrocontable/{id}', 'Contable\RubrocontableController@actualizar')->name('actualizar_rubrocontable');
Route::delete('contable/rubrocontable/{id}', 'Contable\RubrocontableController@eliminar')->name('eliminar_rubrocontable');

/* 
 * Centros de costo
 */

 Route::get('contable/centrocosto', 'Contable\CentrocostoController@index')->name('centrocosto');
 Route::get('contable/centrocosto/crear', 'Contable\CentrocostoController@crear')->name('crear_centrocosto');
 Route::post('contable/centrocosto', 'Contable\CentrocostoController@guardar')->name('guardar_centrocosto');
 Route::get('contable/centrocosto/{id}/editar', 'Contable\CentrocostoController@editar')->name('editar_centrocosto');
 Route::put('contable/centrocosto/{id}', 'Contable\CentrocostoController@actualizar')->name('actualizar_centrocosto');
 Route::delete('contable/centrocosto/{id}', 'Contable\CentrocostoController@eliminar')->name('eliminar_centrocosto');
 
/* 
 * Cuentas contables
 */

Route::get('contable/cuentacontable', 'Contable\CuentacontableController@index')->name('cuentacontable');
Route::get('contable/cuentacontable/crear', 'Contable\CuentacontableController@crear')->name('crear_cuentacontable');
Route::post('contable/cuentacontable', 'Contable\CuentacontableController@guardar')->name('guardar_cuentacontable');
Route::get('contable/cuentacontable/{id}/editar', 'Contable\CuentacontableController@editar')->name('editar_cuentacontable');
Route::put('contable/cuentacontable/{id}', 'Contable\CuentacontableController@actualizar')->name('actualizar_cuentacontable');
Route::get('contable/cuentacontable/{id}/eliminar', 'Contable\CuentacontableController@eliminar')->name('eliminar_cuentacontable');
Route::post('contable/cuentacontable/guardar-orden', 'Contable\CuentacontableController@guardarOrden')->name('guardar_orden');

// Rutas de consulta de cuentas contables
Route::post('contable/cuentacontable/consultacuentacontable', 'Contable\CuentacontableController@consultaCuentaContable')->name('consulta_cuentacontable');
Route::get('contable/cuentacontable/leercuentacontableporcodigo/{empresa_id}/{codigo}', 'Contable\CuentacontableController@leerCuentaContablePorCodigo')->name('leer_cuentacontable_por_codigo');
Route::get('contable/cuentacontable/leercuentacontablecentrocosto/{cuentacontable_id}', 'Contable\CuentacontableController@leerCuentaContableCentroCosto')->name('leer_cuentacontable_centrocosto');

/* 
 * Tipos de asiento
 */

 Route::get('contable/tipoasiento', 'Contable\TipoasientoController@index')->name('tipoasiento');
 Route::get('contable/tipoasiento/crear', 'Contable\TipoasientoController@crear')->name('crear_tipoasiento');
 Route::post('contable/tipoasiento', 'Contable\TipoasientoController@guardar')->name('guardar_tipoasiento');
 Route::get('contable/tipoasiento/{id}/editar', 'Contable\TipoasientoController@editar')->name('editar_tipoasiento');
 Route::put('contable/tipoasiento/{id}', 'Contable\TipoasientoController@actualizar')->name('actualizar_tipoasiento');
 Route::delete('contable/tipoasiento/{id}', 'Contable\TipoasientoController@eliminar')->name('eliminar_tipoasiento');
 
/* 
 * Asientos contables
 */

 Route::get('contable/asiento', 'Contable\AsientoController@index')->name('asiento');
 Route::get('contable/asiento/crear', 'Contable\AsientoController@crear')->name('crear_asiento');
 Route::post('contable/asiento', 'Contable\AsientoController@guardar')->name('guardar_asiento');
 Route::get('contable/asiento/{id}/editar', 'Contable\AsientoController@editar')->name('editar_asiento');
 Route::put('contable/actualizarasiento/{id}', 'Contable\AsientoController@actualizar')->name('actualizar_asiento');
 Route::delete('contable/asiento/{id}', 'Contable\AsientoController@eliminar')->name('eliminar_asiento');
 Route::get('contable/listaasiento/{formato?}/{busqueda?}', 'Contable\AsientoController@listar')->name('lista_asiento');
 Route::post('contable/copiar_asiento', 'Contable\AsientoController@copiarAsiento')->name('copiar_asiento');
 Route::post('contable/revertir_asiento', 'Contable\AsientoController@revertirAsiento')->name('revertir_asiento');
 
 /* 
 * Cuentas contables por usuario
 */

 Route::get('contable/usuario_cuentacontable', 'Contable\Usuario_CuentacontableController@index')->name('usuario_cuentacontable');
 Route::get('contable/usuario_cuentacontable/{id}/editar', 'Contable\Usuario_CuentacontableController@editar')->name('editar_usuario_cuentacontable');
 Route::put('contable/actualizar_usuario_cuentacontable', 'Contable\Usuario_CuentacontableController@actualizar')->name('actualizar_usuario_cuentacontable');
 Route::delete('contable/usuario_cuentacontable/{id}', 'Contable\Usuario_CuentacontableController@eliminar')->name('eliminar_usuario_cuentacontable');
 
/*
 * Productos
 */

Route::get('stock/producto/{id}', 'Stock\ArticuloController@consultaProducto')->name('consultar_producto');

Route::get('stock/products', 'Stock\ArticuloController@index')->name('products.index');
Route::get('stock/products/list', 'Stock\ArticuloController@list')->name('products.list');
Route::get('stock/product/{sku}/{codigo}', 'Stock\ArticuloController@download')->name('product.download');
Route::get('stock/products/create', 'Stock\ArticuloController@create')->name('product.create');
Route::put('stock/product/save', 'Stock\ArticuloController@save')->name('product.save');
Route::get('stock/product/edit/{id}/{tipo?}/{filtros?}', 'Stock\ArticuloController@edit')->name('product.edit');
Route::get('stock/product/datos-tecnicos/edit/{id}', 'Stock\ArticuloController@edit')->name('product.edittecnica');
Route::put('stock/product/update/{id}/{filtros?}', 'Stock\ArticuloController@actualizar')->name('product.update');
Route::delete('stock/product/delete/{id}', 'Stock\ArticuloController@delete')->name('product.delete');
Route::post('stock/product/limpiafiltro', 'Stock\ArticuloController@limpiafiltro')->name('product.limpiafiltro');
Route::post('stock/product/consultaarticulo', 'Stock\ArticuloController@consultaArticulo')->name('consulta_articulo');

Route::get('stock/leercombinaciones/{id}', 'Stock\CombinacionController@leerCombinaciones')->name('leer_combinaciones');
Route::get('stock/leercombinacionesactivas/{id}', 'Stock\CombinacionController@leerCombinacionesActivas')->name('leer_combinaciones_activas');
Route::get('stock/leermodulos/{id}/{modulo?}', 'Stock\LineaController@leerModulos')->name('leer_modulos');
Route::get('stock/leertalles/{id}', 'Stock\ModuloController@leerTalles')->name('leer_talles');

Route::put('stock/product/contaduria/update/{id}', 'Stock\ArticuloController@updateContaduria')->name('product.contaduria.update');
Route::put('stock/product/tecnica/update/{id}', 'Stock\ArticuloController@updateTecnica')->name('product.tecnica.update');

Route::get('stock/combinacion/list', 'Stock\CombinacionController@list')->name('combinacion.list');
Route::get('stock/combinacion/index/{id?}', 'Stock\CombinacionController@index')->name('combinacion.index');

Route::post('stock/combinacion/updateState', 'Stock\CombinacionController@updateState')->name('combinacion.updateState');
Route::post('stock/combinacion/updateStateAll', 'Stock\CombinacionController@updateStateAll')->name('combinacion.updateStateAll');
Route::get('stock/combinacion/edit/{id}/{tipo?}', 'Stock\CombinacionController@edit')->name('combinacion.edit');
Route::put('stock/combinacion/update/{id}', 'Stock\CombinacionController@update')->name('combinacion.update');
Route::put('stock/combinacion/updateTecnica', 'Stock\CombinacionController@updateTecnica')->name('combinacion.tecnica.update');
Route::get('stock/combinacion/create/{id}', 'Stock\CombinacionController@create')->name('combinacion.create');
Route::put('stock/combinacion/save', 'Stock\CombinacionController@save')->name('combinacion.save');
Route::delete('stock/combinacion/delete/{id}', 'Stock\CombinacionController@delete')->name('eliminar_combinacion');
Route::get('stock/combinacion/product/{sku}', 'Stock\CombinacionController@create')->name('combinacion.product');

/* 
 * Movimientos de stock
 */

 Route::get('stock/movimientostock', 'Stock\MovimientoStockController@index')->name('movimientostock');
 Route::get('stock/movimientostock/crear', 'Stock\MovimientoStockController@crear')->name('crear_movimientostock');
 Route::post('stock/movimientostock', 'Stock\MovimientoStockController@guardar')->name('guardar_movimientostock');
 Route::get('stock/movimientostock/{id}/editar', 'Stock\MovimientoStockController@editar')->name('editar_movimientostock');
 Route::put('stock/movimientostock/{id}', 'Stock\MovimientoStockController@actualizar')->name('actualizar_movimientostock');
 Route::delete('stock/movimientostock/{id}', 'Stock\MovimientoStockController@eliminar')->name('eliminar_movimientostock');
 Route::get('stock/listarmovimientostock/{id}', 'Stock\MovimientoStockController@listarMovimientoStock')->name('listar_movimientostock');
// Modulo de ventas
// Reportes de ventas

// Percepciones de IIBB
Route::get('ventas/reppercepcioniibb', 'Ventas\ReppercepcioniibbController@index')->name('listar_percepcioniibb');
Route::post('ventas/crearreppercepcioniibb', 'Ventas\ReppercepcioniibbController@crearReporteControlPercepcionesIIBB')->name('crear_reppercepcioniibb');

// Pedidos
Route::get('ventas/reppedido', 'Ventas\PedidoController@indexReportePedido')->name('rep_pedido');
Route::post('ventas/crearreppedido', 'Ventas\PedidoController@crearReportePedido')->name('crear_reppedido');

// Totales de Pedidos
Route::get('ventas/reptotalpedido', 'Ventas\PedidoController@indexReporteTotalPedido')->name('rep_totalpedido');
Route::post('ventas/crearreptotalpedido', 'Ventas\PedidoController@crearReporteTotalPedido')->name('crear_reptotalpedido');

// General de Pedidos
Route::get('ventas/repgeneralpedido', 'Ventas\PedidoController@indexReporteGeneralPedido')->name('rep_generalpedido');
Route::post('ventas/crearrepgeneralpedido', 'Ventas\PedidoController@crearReporteGeneralPedido')->name('crear_repgeneralpedido');

// Consumo de materiales
Route::get('ventas/repconsumomaterial', 'Ventas\PedidoController@indexReporteConsumoMaterial')->name('rep_consumomaterial');
Route::post('ventas/crearrepconsumomaterial', 'Ventas\PedidoController@crearReporteConsumoMaterial')->name('crear_repconsumomaterial');

// Etiquetas de OT
Route::get('ventas/repetiquetaot', 'Ventas\OrdentrabajoController@indexEtiqueta')->name('repetiquetaot');
Route::post('ventas/crearetiquetaot', 'Ventas\OrdentrabajoController@crearEtiquetaOt')->name('crear_repetiquetaot');
Route::get('ventas/generazpl', 'Ventas\OrdentrabajoController@generaZPL')->name('genera_zpl');
Route::get('ventas/generaetiquetaprueba', 'Ventas\OrdentrabajoController@generaEtiquetaPruebaOt')->name('generaetiquetaprueba');
Route::post('ventas/crearetiquetapruebaot', 'Ventas\OrdentrabajoController@crearEtiquetaPruebaOt')->name('crear_repetiquetapruebaot');

// Emision de OT
Route::get('ventas/repemisionot', 'Ventas\OrdentrabajoController@indexEmisionOT')->name('repemisionot');
Route::post('ventas/crearemisionot', 'Ventas\OrdentrabajoController@crearEmisionOt')->name('crearemisionot');

// Clientes
Route::get('ventas/repcliente', 'Ventas\ClienteController@indexReporteCliente')->name('rep_cliente');
Route::post('ventas/crearrepcliente', 'Ventas\ClienteController@crearReporteCliente')->name('crear_repcliente');

/* 
 * Vendedores
 */

Route::get('ventas/vendedor', 'Ventas\VendedorController@index')->name('vendedor');
Route::get('ventas/vendedor/crear', 'Ventas\VendedorController@crear')->name('crear_vendedor');
Route::post('ventas/vendedor', 'Ventas\VendedorController@guardar')->name('guardar_vendedor');
Route::get('ventas/vendedor/{id}/editar', 'Ventas\VendedorController@editar')->name('editar_vendedor');
Route::put('ventas/vendedor/{id}', 'Ventas\VendedorController@actualizar')->name('actualizar_vendedor');
Route::delete('ventas/vendedor/{id}', 'Ventas\VendedorController@eliminar')->name('eliminar_vendedor');

/* 
 * Zonas de venta
 */

Route::get('ventas/zonavta', 'Ventas\ZonavtaController@index')->name('zonavta');
Route::get('ventas/zonavta/crear', 'Ventas\ZonavtaController@crear')->name('crear_zonavta');
Route::post('ventas/zonavta', 'Ventas\ZonavtaController@guardar')->name('guardar_zonavta');
Route::get('ventas/zonavta/{id}/editar', 'Ventas\ZonavtaController@editar')->name('editar_zonavta');
Route::put('ventas/zonavta/{id}', 'Ventas\ZonavtaController@actualizar')->name('actualizar_zonavta');
Route::delete('ventas/zonavta/{id}', 'Ventas\ZonavtaController@eliminar')->name('eliminar_zonavta');

/* 
 * Subzonas de venta
 */

Route::get('ventas/subzonavta', 'Ventas\SubzonavtaController@index')->name('subzonavta');
Route::get('ventas/subzonavta/crear', 'Ventas\SubzonavtaController@crear')->name('crear_subzonavta');
Route::post('ventas/subzonavta', 'Ventas\SubzonavtaController@guardar')->name('guardar_subzonavta');
Route::get('ventas/subzonavta/{id}/editar', 'Ventas\SubzonavtaController@editar')->name('editar_subzonavta');
Route::put('ventas/subzonavta/{id}', 'Ventas\SubzonavtaController@actualizar')->name('actualizar_subzonavta');
Route::delete('ventas/subzonavta/{id}', 'Ventas\SubzonavtaController@eliminar')->name('eliminar_subzonavta');

/* 
 * Condiciones de venta
 */

Route::get('ventas/condicionventa', 'Ventas\CondicionventaController@index')->name('condicionventa');
Route::get('ventas/condicionventa/crear', 'Ventas\CondicionventaController@crear')->name('crear_condicionventa');
Route::post('ventas/condicionventa', 'Ventas\CondicionventaController@guardar')->name('guardar_condicionventa');
Route::get('ventas/condicionventa/{id}/editar', 'Ventas\CondicionventaController@editar')->name('editar_condicionventa');
Route::put('ventas/condicionventa/{id}', 'Ventas\CondicionventaController@actualizar')->name('actualizar_condicionventa');
Route::delete('ventas/condicionventa/{id}', 'Ventas\CondicionventaController@eliminar')->name('eliminar_condicionventa');

/* 
 * Transportes
 */

Route::get('ventas/transporte', 'Ventas\TransporteController@index')->name('transporte');
Route::get('ventas/transporte/crear', 'Ventas\TransporteController@crear')->name('crear_transporte');
Route::post('ventas/transporte', 'Ventas\TransporteController@guardar')->name('guardar_transporte');
Route::get('ventas/transporte/{id}/editar', 'Ventas\TransporteController@editar')->name('editar_transporte');
Route::put('ventas/transporte/{id}', 'Ventas\TransporteController@actualizar')->name('actualizar_transporte');
Route::delete('ventas/transporte/{id}', 'Ventas\TransporteController@eliminar')->name('eliminar_transporte');

/* 
 * Motivos de cierre de pedido
 */

Route::get('ventas/motivocierrepedido', 'Ventas\MotivocierrepedidoController@index')->name('motivocierrepedido');
Route::get('ventas/motivocierrepedido/crear', 'Ventas\MotivocierrepedidoController@crear')->name('crear_motivocierrepedido');
Route::post('ventas/motivocierrepedido', 'Ventas\MotivocierrepedidoController@guardar')->name('guardar_motivocierrepedido');
Route::get('ventas/motivocierrepedido/{id}/editar', 'Ventas\MotivocierrepedidoController@editar')->name('editar_motivocierrepedido');
Route::put('ventas/motivocierrepedido/{id}', 'Ventas\MotivocierrepedidoController@actualizar')->name('actualizar_motivocierrepedido');
Route::delete('ventas/motivocierrepedido/{id}', 'Ventas\MotivocierrepedidoController@eliminar')->name('eliminar_motivocierrepedido');

/* 
 * Tipos suspension de clientes
 */

Route::get('ventas/tiposuspensioncliente', 'Ventas\TiposuspensionclienteController@index')->name('tiposuspensioncliente');
Route::get('ventas/tiposuspensioncliente/crear', 'Ventas\TiposuspensionclienteController@crear')->name('crear_tiposuspensioncliente');
Route::post('ventas/tiposuspensioncliente', 'Ventas\TiposuspensionclienteController@guardar')->name('guardar_tiposuspensioncliente');
Route::get('ventas/tiposuspensioncliente/{id}/editar', 'Ventas\TiposuspensionclienteController@editar')->name('editar_tiposuspensioncliente');
Route::put('ventas/tiposuspensioncliente/{id}', 'Ventas\TiposuspensionclienteController@actualizar')->name('actualizar_tiposuspensioncliente');
Route::delete('ventas/tiposuspensioncliente/{id}', 'Ventas\TiposuspensionclienteController@eliminar')->name('eliminar_tiposuspensioncliente');

/* 
 * Incoterms
 */

 Route::get('ventas/incoterm', 'Ventas\IncotermController@index')->name('incoterm');
 Route::get('ventas/incoterm/crear', 'Ventas\IncotermController@crear')->name('crear_incoterm');
 Route::post('ventas/incoterm', 'Ventas\IncotermController@guardar')->name('guardar_incoterm');
 Route::get('ventas/incoterm/{id}/editar', 'Ventas\IncotermController@editar')->name('editar_incoterm');
 Route::put('ventas/incoterm/{id}', 'Ventas\IncotermController@actualizar')->name('actualizar_incoterm');
 Route::delete('ventas/incoterm/{id}', 'Ventas\IncotermController@eliminar')->name('eliminar_incoterm');
 
/* 
 * Forma de pago
 */

 Route::get('ventas/formapago', 'Ventas\FormapagoController@index')->name('formapago');
 Route::get('ventas/formapago/crear', 'Ventas\FormapagoController@crear')->name('crear_formapago');
 Route::post('ventas/formapago', 'Ventas\FormapagoController@guardar')->name('guardar_formapago');
 Route::get('ventas/formapago/{id}/editar', 'Ventas\FormapagoController@editar')->name('editar_formapago');
 Route::put('ventas/formapago/{id}', 'Ventas\FormapagoController@actualizar')->name('actualizar_formapago');
 Route::delete('ventas/formapago/{id}', 'Ventas\FormapagoController@eliminar')->name('eliminar_formapago');
 
/* 
 * Tipos de transacciones de ventas
 */

Route::get('ventas/tipotransaccion', 'Ventas\TipotransaccionController@index')->name('tipotransaccion');
Route::get('ventas/tipotransaccion/crear', 'Ventas\TipotransaccionController@crear')->name('crear_tipotransaccion');
Route::post('ventas/tipotransaccion', 'Ventas\TipotransaccionController@guardar')->name('guardar_tipotransaccion');
Route::get('ventas/tipotransaccion/{id}/editar', 'Ventas\TipotransaccionController@editar')->name('editar_tipotransaccion');
Route::put('ventas/tipotransaccion/{id}', 'Ventas\TipotransaccionController@actualizar')->name('actualizar_tipotransaccion');
Route::delete('ventas/tipotransaccion/{id}', 'Ventas\TipotransaccionController@eliminar')->name('eliminar_tipotransaccion');

/* 
 * Puntos de venta
 */

Route::get('ventas/puntoventa', 'Ventas\PuntoventaController@index')->name('puntoventa');
Route::get('ventas/puntoventa/crear', 'Ventas\PuntoventaController@crear')->name('crear_puntoventa');
Route::post('ventas/puntoventa', 'Ventas\PuntoventaController@guardar')->name('guardar_puntoventa');
Route::get('ventas/puntoventa/{id}/editar', 'Ventas\PuntoventaController@editar')->name('editar_puntoventa');
Route::put('ventas/puntoventa/{id}', 'Ventas\PuntoventaController@actualizar')->name('actualizar_puntoventa');
Route::delete('ventas/puntoventa/{id}', 'Ventas\PuntoventaController@eliminar')->name('eliminar_puntoventa');

// Llamada desde jquery
Route::get('ventas/chequeapuntoventa/{id}', 'Ventas\PuntoventaController@chequeapuntoventa')->name('chequea_puntoventa');

/* 
 * Clientes
 */

Route::get('ventas/cliente', 'Ventas\ClienteController@index')->name('cliente');
Route::get('ventas/cliente/crear/{tipoalta?}', 'Ventas\ClienteController@crear')->name('crear_cliente');
Route::post('ventas/cliente', 'Ventas\ClienteController@guardar')->name('guardar_cliente');
Route::post('ventas/clienteprovisorio', 'Ventas\ClienteController@guardarClienteProvisorio')->name('guardar_cliente_provisorio');
Route::get('ventas/cliente/{id}/editar', 'Ventas\ClienteController@editar')->name('editar_cliente');
Route::put('ventas/cliente/{id}', 'Ventas\ClienteController@actualizar')->name('actualizar_cliente');
Route::delete('ventas/cliente/{id}', 'Ventas\ClienteController@eliminar')->name('eliminar_cliente');
Route::get('ventas/leercliente_entrega/{cliente_id}', 'Ventas\ClienteController@leerCliente_Entrega')->name('leer_cliente_entrega');
Route::get('ventas/leercliente/{cliente_id}', 'Ventas\ClienteController@leerCliente')->name('leer_cliente');

/* 
 * Pedidos
 */

Route::get('ventas/pedido', 'Ventas\PedidoController@indexp')->name('pedido');
//Route::get('ventas/pedidop', 'Ventas\PedidoController@indexp')->name('pedidop');
Route::get('ventas/pedido/crear', 'Ventas\PedidoController@crear')->name('crear_pedido');
Route::post('ventas/pedido', 'Ventas\PedidoController@guardar')->name('guardar_pedido');
Route::get('ventas/pedido/{id}/editar', 'Ventas\PedidoController@editar')->name('editar_pedido');
Route::put('ventas/pedido/{id}', 'Ventas\PedidoController@actualizar')->name('actualizar_pedido');
Route::delete('ventas/pedido/{id}', 'Ventas\PedidoController@eliminar')->name('eliminar_pedido');
Route::get('ventas/listarpedido/{id}/{cliente_id?}', 'Ventas\PedidoController@listarPedido')->name('listar_pedido');
Route::get('ventas/listarprefactura/{id}/{itemid}/{descuentolinea?}', 'Ventas\PedidoController@listarPreFactura')->name('listar_prefactura');
Route::get('ventas/anularitempedido/{id}/{codigoot}/{motivocierrepedido_id}/{cliente_id?}', 'Ventas\PedidoController@anularItemPedido')->name('anular_item_pedido');
Route::get('ventas/pedido/cerrar', 'Ventas\PedidoController@cerrarPedido')->name('cerrar_pedido');
Route::post('ventas/pedido/ejecutacierre', 'Ventas\PedidoController@ejecutaCierre')->name('ejecuta_cierre_pedido');
Route::post('ventas/pedido/limpiafiltro', 'Ventas\PedidoController@limpiafiltro')->name('pedido.limpiafiltro');
Route::get('ventas/listapedido/{formato?}/{busqueda?}', 'Ventas\PedidoController@listar')->name('lista_pedido');

/* 
 * Ordenes de trabajo
 */

Route::get('ventas/ordenestrabajo', 'Ventas\OrdentrabajoController@indexp')->name('ordentrabajo');
//Route::get('ventas/ordenestrabajop', 'Ventas\OrdentrabajoController@indexp')->name('ordentrabajop');
Route::get('ventas/consultaordenestrabajo', 'Ventas\OrdentrabajoController@indexp')->name('consulta_ordentrabajo');
Route::get('ventas/ordenestrabajo/crear', 'Ventas\OrdentrabajoController@crear')->name('crear_ordentrabajo');
Route::get('ventas/ordenestrabajo/{id}/editar', 'Ventas\OrdentrabajoController@editar')->name('editar_ordentrabajo');
Route::put('ventas/ordenestrabajo/{id}', 'Ventas\OrdentrabajoController@actualizar')->name('actualizar_ordentrabajo');
Route::delete('ventas/ordenestrabajo/{id}', 'Ventas\OrdentrabajoController@eliminar')->name('eliminar_ordentrabajo');
Route::post('ventas/pedido/consultapendientesot', 'Ventas\PedidoController@consultarPendienteOt')->name('consultar_pendiente_ot');
Route::post('ventas/ordenestrabajo/generar', 'Ventas\OrdentrabajoController@generar')->name('generar_ordentrabajo');
Route::get('ventas/guardaordenestrabajo/{origen}/{ids}/{checkotstock}/{ordentrabajo_stock_codigo}/{deposito_id}/{leyenda?}', 
    'Ventas\OrdentrabajoController@guardar')->name('guardar_ordentrabajo');
Route::get('ventas/listaordenestrabajo/{id}', 'Ventas\OrdentrabajoController@listar')->name('listar_ordentrabajo');
Route::get('ventas/estadoot/{id}/{pedido_combinacion_id?}', 'Ventas\OrdentrabajoController@estadoOt')->name('estado_ot');
Route::get('ventas/controlaordentrabajostock/{id}/{articulo_id}/{combinacion_id}', 'Ventas\OrdentrabajoController@controlaOtStock')->name('controla_ordetrabajo_stock');
Route::post('ventas/ordenestrabajo/limpiafiltro', 'Ventas\OrdentrabajoController@limpiafiltro')->name('ordentrabajo.limpiafiltro');
Route::get('ventas/listaordentrabajo/{formato?}/{busqueda?}', 'Ventas\OrdentrabajoController@lista')->name('lista_ordentrabajo');
Route::post('ventas/ordenestrabajo/borrarOt', 'Ventas\OrdentrabajoController@borrarOt')->name('borrar_ot');

/*
 * Comprobantes de venta
 */

Route::get('ventas/factura', 'Ventas\FacturacionController@index')->name('factura');
Route::get('ventas/factura/crear', 'Ventas\FacturacionController@crear')->name('crear_factura');
Route::post('ventas/factura', 'Ventas\FacturacionController@guardar')->name('guardar_factura');
Route::get('ventas/factura/{id}/editar', 'Ventas\FacturacionController@editar')->name('editar_factura');
Route::delete('ventas/factura/{id}', 'Ventas\FacturacionController@eliminar')->name('eliminar_factura');
Route::get('ventas/listafactura/{formato?}/{busqueda?}', 'Ventas\FacturacionController@listar')->name('listar_factura');

/* PRODUCCION */

/* 
 * Tareas
 */

Route::get('produccion/tarea', 'Produccion\TareaController@index')->name('tarea');
Route::get('produccion/tarea/crear', 'Produccion\TareaController@crear')->name('crear_tarea');
Route::post('produccion/tarea', 'Produccion\TareaController@guardar')->name('guardar_tarea');
Route::get('produccion/tarea/{id}/editar', 'Produccion\TareaController@editar')->name('editar_tarea');
Route::put('produccion/tarea/{id}', 'Produccion\TareaController@actualizar')->name('actualizar_tarea');
Route::delete('produccion/tarea/{id}', 'Produccion\TareaController@eliminar')->name('eliminar_tarea');

/* 
 * Empleados
 */

Route::get('produccion/empleado', 'Produccion\EmpleadoController@index')->name('empleado');
Route::get('produccion/empleado/crear', 'Produccion\EmpleadoController@crear')->name('crear_empleado');
Route::post('produccion/empleado', 'Produccion\EmpleadoController@guardar')->name('guardar_empleado');
Route::get('produccion/empleado/{id}/editar', 'Produccion\EmpleadoController@editar')->name('editar_empleado');
Route::put('produccion/empleado/{id}', 'Produccion\EmpleadoController@actualizar')->name('actualizar_empleado');
Route::delete('produccion/empleado/{id}', 'Produccion\EmpleadoController@eliminar')->name('eliminar_empleado');

/* 
 * Operaciones
 */

Route::get('produccion/operacion', 'Produccion\OperacionController@index')->name('operacion');
Route::get('produccion/operacion/crear', 'Produccion\OperacionController@crear')->name('crear_operacion');
Route::post('produccion/operacion', 'Produccion\OperacionController@guardar')->name('guardar_operacion');
Route::get('produccion/operacion/{id}/editar', 'Produccion\OperacionController@editar')->name('editar_operacion');
Route::put('produccion/operacion/{id}', 'Produccion\OperacionController@actualizar')->name('actualizar_operacion');
Route::delete('produccion/operacion/{id}', 'Produccion\OperacionController@eliminar')->name('eliminar_operacion');

/* 
 * Movimientos de OT
 */

Route::get('produccion/movimientoordentrabajo', 'Produccion\MovimientoOrdentrabajoController@index')->name('movimientoordentrabajo');
Route::get('produccion/movimientoordentrabajo/crear', 'Produccion\MovimientoOrdentrabajoController@crear')->name('crear_movimientoordentrabajo');
Route::post('produccion/movimientoordentrabajo', 'Produccion\MovimientoOrdentrabajoController@guardar')->name('guardar_movimientoordentrabajo');
Route::get('produccion/movimientoordentrabajo/{id}/editar', 'Produccion\MovimientoOrdentrabajoController@editar')->name('editar_movimientoordentrabajo');
Route::put('produccion/movimientoordentrabajo/{id}', 'Produccion\MovimientoOrdentrabajoController@actualizar')->name('actualizar_movimientoordentrabajo');
Route::delete('produccion/movimientoordentrabajo/{id}', 'Produccion\MovimientoOrdentrabajoController@eliminar')->name('eliminar_movimientoordentrabajo');
Route::get('produccion/consultamovimientoordentrabajo/{id}', 'Produccion\MovimientoOrdentrabajoController@index')->name('consultamovimientoordentrabajo');

// Llamadas desde movimientos de OT
Route::get('produccion/controlsecuencia/{ots}/{operacion}/{tarea}', 'Produccion\MovimientoOrdentrabajoController@controlSecuencia')->name('control_secuencia');
Route::get('produccion/ctrlsecuencia/{ots}/{operacion}/{tarea}/{pedido}', 'Produccion\MovimientoOrdentrabajoController@ctrlSecuencia')->name('ctrl_secuencia');

// Llamadas desde consulta de OT
Route::post('produccion/empacarTarea', 'Produccion\MovimientoOrdentrabajoController@empacarTarea')->name('empacar_tarea');
Route::post('ventas/actualizarpedido', 'Ventas\PedidoController@actualizaItemPedido')->name('actualizar_pedido_desdeoc');
Route::get('produccion/leerTareas/{ot_id}', 'Produccion\MovimientoOrdentrabajoController@leerTareas')->name('leer_tareas');
Route::post('ventas/facturarItemOt', 'Ventas\FacturacionController@facturarItemOt')->name('facturar_item_ot');

// Reportes de produccion

// Estado de OT
Route::get('produccion/repestadoot', 'Produccion\RepEstadoOtController@index')->name('rep_estadoot');
Route::post('produccion/crearrepestadoot', 'Produccion\RepEstadoOtController@crearReporteEstadoOt')->name('crear_repestadoot');

// Total de pares
Route::get('produccion/reptotalpares', 'Produccion\RepTotalParesController@index')->name('rep_totalpares');
Route::post('produccion/crearreptotlapares', 'Produccion\RepTotalParesController@crearReporteTotalPares')->name('crear_reptotalpares');

// Liquidacion de tareas
Route::get('produccion/repliquidaciontarea', 'Produccion\RepLiquidacionTareaController@index')->name('rep_liquidaciontarea');
Route::post('produccion/crearrepliquidaciotarea', 'Produccion\RepLiquidacionTareaController@crearReporteLiquidacionTarea')->name('crear_repliquidaciontarea');

// Consumo de OT
Route::get('produccion/repconsumoot', 'Produccion\RepConsumoOtController@index')->name('rep_consumoot');
Route::post('produccion/crearrepconsumoot', 'Produccion\RepConsumoOtController@crearReporteConsumoOt')->name('crear_repconsumoot');

// Consumo de Cajas
Route::get('produccion/repconsumocaja', 'Produccion\RepConsumoCajaController@index')->name('rep_consumocaja');
Route::post('produccion/crearrepconsumocaja', 'Produccion\RepConsumoCajaController@crearReporteConsumoCaja')->name('crear_repconsumocaja');

// Programacion de Armado
Route::get('produccion/repprogarmado', 'Produccion\RepProgArmadoController@index')->name('rep_progarmado');
Route::post('produccion/crearrepprogarmado', 'Produccion\RepProgArmadoController@crearReporteProgArmado')->name('crear_repprogarmado');

// Graficos
Route::get('graficos/velas', 'Graficos\GraficosController@index')->name('grafico_vela');
Route::get('graficos/reporte', 'Graficos\GraficosController@indexReporteLecturas')->name('reporte_lecturas');
Route::post('graficos/crearreplecturas', 'Graficos\GraficosController@crearReporteLecturas')->name('crear_replecturas');
Route::get('graficos/leerDatosLecturas/{fecha}/{dias}', 'Graficos\GraficosController@leerDatosLecturas')->name('leer_datos_lecturas');
Route::get('graficos/reporteindicadores', 'Graficos\GraficosController@indexReporteIndicadores')->name('reporte_indicadores');
Route::post('graficos/crearindicadores', 'Graficos\GraficosController@crearReporteIndicadores')->name('crear_repindicadores');
Route::get('graficos/ordenes', 'Graficos\GraficosController@indexGeneraOrdenes')->name('ordenes');
Route::post('graficos/generaordenes', 'Graficos\GraficosController@generaOrdenes')->name('genera_ordenes');

// Modulo de caja

/* 
 * Cuentas de caja
 */

 Route::get('caja/cuentacaja', 'Caja\CuentacajaController@index')->name('cuentacaja');
 Route::get('caja/cuentacaja/crear', 'Caja\CuentacajaController@crear')->name('crear_cuentacaja');
 Route::post('caja/cuentacaja', 'Caja\CuentacajaController@guardar')->name('guardar_cuentacaja');
 Route::get('caja/cuentacaja/{id}/editar', 'Caja\CuentacajaController@editar')->name('editar_cuentacaja');
 Route::put('caja/cuentacaja/{id}', 'Caja\CuentacajaController@actualizar')->name('actualizar_cuentacaja');
 Route::delete('caja/cuentacaja/{id}', 'Caja\CuentacajaController@eliminar')->name('eliminar_cuentacaja');

 // Rutas de consulta de cuentas de caja
Route::post('caja/cuentacaja/consultacuentacaja', 'Caja\CuentacajaController@consultaCuentaCaja')->name('consulta_cuentacaja');
Route::get('caja/cuentacaja/leercuentacajaporcodigo/{codigo}', 'Caja\CuentacajaController@leerCuentaCajaPorCodigo')->name('leer_cuentacaja_por_codigo');
 
/* 
 * Conceptos de gastos
 */

 Route::get('caja/conceptogasto', 'Caja\ConceptogastoController@index')->name('conceptogasto');
 Route::get('caja/conceptogasto/crear', 'Caja\ConceptogastoController@crear')->name('crear_conceptogasto');
 Route::post('caja/conceptogasto', 'Caja\ConceptogastoController@guardar')->name('guardar_conceptogasto');
 Route::get('caja/conceptogasto/{id}/editar', 'Caja\ConceptogastoController@editar')->name('editar_conceptogasto');
 Route::put('caja/conceptogasto/{id}', 'Caja\ConceptogastoController@actualizar')->name('actualizar_conceptogasto');
 Route::delete('caja/conceptogasto/{id}', 'Caja\ConceptogastoController@eliminar')->name('eliminar_conceptogasto');
  
/* 
 * Origen de vouchers
 */

 Route::get('caja/origenvoucher', 'Caja\OrigenvoucherController@index')->name('origenvoucher');
 Route::get('caja/origenvoucher/crear', 'Caja\OrigenvoucherController@crear')->name('crear_origenvoucher');
 Route::post('caja/origenvoucher', 'Caja\OrigenvoucherController@guardar')->name('guardar_origenvoucher');
 Route::get('caja/origenvoucher/{id}/editar', 'Caja\OrigenvoucherController@editar')->name('editar_origenvoucher');
 Route::put('caja/origenvoucher/{id}', 'Caja\OrigenvoucherController@actualizar')->name('actualizar_origenvoucher');
 Route::delete('caja/origenvoucher/{id}', 'Caja\OrigenvoucherController@eliminar')->name('eliminar_origenvoucher');
 
/* 
 * Talonarios de vouchers
 */

 Route::get('caja/talonariovoucher', 'Caja\TalonariovoucherController@index')->name('talonariovoucher');
 Route::get('caja/talonariovoucher/crear', 'Caja\TalonariovoucherController@crear')->name('crear_talonariovoucher');
 Route::post('caja/talonariovoucher', 'Caja\TalonariovoucherController@guardar')->name('guardar_talonariovoucher');
 Route::get('caja/talonariovoucher/{id}/editar', 'Caja\TalonariovoucherController@editar')->name('editar_talonariovoucher');
 Route::put('caja/talonariovoucher/{id}', 'Caja\TalonariovoucherController@actualizar')->name('actualizar_talonariovoucher');
 Route::delete('caja/talonariovoucher/{id}', 'Caja\TalonariovoucherController@eliminar')->name('eliminar_talonariovoucher');
  
/* 
 * Talonarios de rendiciones
 */

 Route::get('caja/talonariorendicion', 'Caja\TalonariorendicionController@index')->name('talonariorendicion');
 Route::get('caja/talonariorendicion/crear', 'Caja\TalonariorendicionController@crear')->name('crear_talonariorendicion');
 Route::post('caja/talonariorendicion', 'Caja\TalonariorendicionController@guardar')->name('guardar_talonariorendicion');
 Route::get('caja/talonariorendicion/{id}/editar', 'Caja\TalonariorendicionController@editar')->name('editar_talonariorendicion');
 Route::put('caja/talonariorendicion/{id}', 'Caja\TalonariorendicionController@actualizar')->name('actualizar_talonariorendicion');
 Route::delete('caja/talonariorendicion/{id}', 'Caja\TalonariorendicionController@eliminar')->name('eliminar_talonariorendicion');
 
/* 
 * Talonarios de rendiciones
 */

 Route::get('caja/banco', 'Caja\BancoController@index')->name('banco');
 Route::get('caja/banco/crear', 'Caja\BancoController@crear')->name('crear_banco');
 Route::post('caja/banco', 'Caja\BancoController@guardar')->name('guardar_banco');
 Route::get('caja/banco/{id}/editar', 'Caja\BancoController@editar')->name('editar_banco');
 Route::put('caja/banco/{id}', 'Caja\BancoController@actualizar')->name('actualizar_banco');
 Route::delete('caja/banco/{id}', 'Caja\BancoController@eliminar')->name('eliminar_banco');

/* 
 * Tipo de cuenta de caja
 */

 Route::get('caja/tipocuentacaja', 'Caja\TipocuentacajaController@index')->name('tipocuentacaja');
 Route::get('caja/tipocuentacaja/crear', 'Caja\TipocuentacajaController@crear')->name('crear_tipocuentacaja');
 Route::post('caja/tipocuentacaja', 'Caja\TipocuentacajaController@guardar')->name('guardar_tipocuentacaja');
 Route::get('caja/tipocuentacaja/{id}/editar', 'Caja\TipocuentacajaController@editar')->name('editar_tipocuentacaja');
 Route::put('caja/tipocuentacaja/{id}', 'Caja\TipocuentacajaController@actualizar')->name('actualizar_tipocuentacaja');
 Route::delete('caja/tipocuentacaja/{id}', 'Caja\TipocuentacajaController@eliminar')->name('eliminar_tipocuentacaja'); 

/*
 * Medio de pago
 */

 Route::get('caja/mediopago', 'Caja\MediopagoController@index')->name('mediopago');
 Route::get('caja/mediopago/crear', 'Caja\MediopagoController@crear')->name('crear_mediopago');
 Route::post('caja/mediopago', 'Caja\MediopagoController@guardar')->name('guardar_mediopago');
 Route::get('caja/mediopago/{id}/editar', 'Caja\MediopagoController@editar')->name('editar_mediopago');
 Route::put('caja/mediopago/{id}', 'Caja\MediopagoController@actualizar')->name('actualizar_mediopago');
 Route::delete('caja/mediopago/{id}', 'Caja\MediopagoController@eliminar')->name('eliminar_mediopago'); 

/*
 * Voucher
 */

 Route::get('caja/voucher', 'Caja\VoucherController@index')->name('voucher');
 Route::get('caja/voucher/crear', 'Caja\VoucherController@crear')->name('crear_voucher');
 Route::post('caja/voucher', 'Caja\VoucherController@guardar')->name('guardar_voucher');
 Route::get('caja/voucher/{id}/editar', 'Caja\VoucherController@editar')->name('editar_voucher');
 Route::put('caja/voucher/{id}', 'Caja\VoucherController@actualizar')->name('actualizar_voucher');
 Route::delete('caja/voucher/{id}', 'Caja\VoucherController@eliminar')->name('eliminar_voucher'); 
 Route::get('caja/listavoucher/{formato?}/{busqueda?}', 'Caja\VoucherController@listar')->name('lista_voucher');

/* 
 * Tipos de transacciones de caja
 */

 Route::get('caja/tipotransaccion_caja', 'Caja\Tipotransaccion_CajaController@index')->name('tipotransaccion_caja');
 Route::get('caja/tipotransaccion_caja/crear', 'Caja\Tipotransaccion_CajaController@crear')->name('crear_tipotransaccion_caja');
 Route::post('caja/tipotransaccion_caja', 'Caja\Tipotransaccion_CajaController@guardar')->name('guardar_tipotransaccion_caja');
 Route::get('caja/tipotransaccion_caja/{id}/editar', 'Caja\Tipotransaccion_CajaController@editar')->name('editar_tipotransaccion_caja');
 Route::put('caja/tipotransaccion_caja/{id}', 'Caja\Tipotransaccion_CajaController@actualizar')->name('actualizar_tipotransaccion_caja');
 Route::delete('caja/tipotransaccion_caja/{id}', 'Caja\Tipotransaccion_CajaController@eliminar')->name('eliminar_tipotransaccion_caja');
 
/* 
 * Cajas
 */

 Route::get('caja/caja', 'Caja\CajaController@index')->name('consulta_caja');
 Route::get('caja/caja/crear', 'Caja\CajaController@crear')->name('crea_caja');
 Route::post('caja/caja', 'Caja\CajaController@guardar')->name('guarda_caja');
 Route::get('caja/caja/{id}/editar', 'Caja\CajaController@editar')->name('edita_caja');
 Route::put('caja/caja/{id}', 'Caja\CajaController@actualizar')->name('actualiza_caja');
 Route::delete('caja/caja/{id}', 'Caja\CajaController@eliminar')->name('elimina_caja');
 
/* 
 * Asignacion de Cajas
 */

 Route::get('caja/cajaasignacion', 'Caja\CajaAsignacionController@index')->name('consulta_cajaasignacion');
 Route::get('caja/cajasignacion/crear', 'Caja\CajaAsignacionController@crear')->name('crea_cajaasignacion');
 Route::post('caja/cajasignacion', 'Caja\CajaAsignacionController@guardar')->name('guarda_cajaasignacion');
 Route::get('caja/cajasignacion/{id}/editar', 'Caja\CajaAsignacionController@editar')->name('edita_cajaasignacion');
 Route::put('caja/cajasignacion/{id}', 'Caja\CajaAsignacionController@actualizar')->name('actualiza_cajaasignacion');
 Route::delete('caja/cajasignacion/{id}', 'Caja\CajaAsignacionController@eliminar')->name('elimina_cajaasignacion');
 
/* 
 * Movimiento de Cajas
 */

Route::get('caja/movimientocaja', 'Caja\MovimientoCajaController@index')->name('consulta_movimiento_caja');

/* 
 * Ingresos y Egresos de Caja
 */

 Route::get('caja/ingresoegreso', 'Caja\IngresoEgresoController@index')->name('ingresoegreso');
 Route::get('caja/ingresoegreso/crear/{caja?}', 'Caja\IngresoEgresoController@crear')->name('crear_ingresoegreso');
 Route::post('caja/ingresoegreso', 'Caja\IngresoEgresoController@guardar')->name('guardar_ingresoegreso');
 Route::get('caja/ingresoegreso/{id}/{origen?}/editar', 'Caja\IngresoEgresoController@editar')->name('editar_ingresoegreso');
 Route::put('caja/actualizaringresoegreso/{id}', 'Caja\IngresoEgresoController@actualizar')->name('actualizar_ingresoegreso');
 Route::delete('caja/ingresoegreso/{id}/{origen?}', 'Caja\IngresoEgresoController@eliminar')->name('eliminar_ingresoegreso');
 Route::get('caja/listaingresoegreso/{formato?}/{busqueda?}', 'Caja\IngresoEgresoController@listar')->name('lista_ingresoegreso');
 Route::post('caja/copiar_ingresoegreso', 'Caja\IngresoEgresoController@copiarIngresoEgreso')->name('copiar_ingresoegreso');
 Route::post('caja/revertir_ingresoegreso', 'Caja\IngresoEgresoController@revertirIngresoEgreso')->name('revertir_ingresoegreso');
 Route::post('caja/generaasientocontable_ingresoegreso', 'Caja\IngresoEgresoController@generaAsientoContable')->name('generaasientocontable_ingresoegreso');
 
 // Modulo de compras
 
/* 
 * Condiciones de pago
 */

 Route::get('compras/condicionpago', 'Compras\CondicionpagoController@index')->name('condicionpago');
 Route::get('compras/condicionpago/crear', 'Compras\CondicionpagoController@crear')->name('crear_condicionpago');
 Route::post('compras/condicionpago', 'Compras\CondicionpagoController@guardar')->name('guardar_condicionpago');
 Route::get('compras/condicionpago/{id}/editar', 'Compras\CondicionpagoController@editar')->name('editar_condicionpago');
 Route::put('compras/condicionpago/{id}', 'Compras\CondicionpagoController@actualizar')->name('actualizar_condicionpago');
 Route::delete('compras/condicionpago/{id}', 'Compras\CondicionpagoController@eliminar')->name('eliminar_condicionpago');
 
/* 
 * Condiciones de compra
 */

 Route::get('compras/condicioncompra', 'Compras\CondicioncompraController@index')->name('condicioncompra');
 Route::get('compras/condicioncompra/crear', 'Compras\CondicioncompraController@crear')->name('crear_condicioncompra');
 Route::post('compras/condicioncompra', 'Compras\CondicioncompraController@guardar')->name('guardar_condicioncompra');
 Route::get('compras/condicioncompra/{id}/editar', 'Compras\CondicioncompraController@editar')->name('editar_condicioncompra');
 Route::put('compras/condicioncompra/{id}', 'Compras\CondicioncompraController@actualizar')->name('actualizar_condicioncompra');
 Route::delete('compras/condicioncompra/{id}', 'Compras\CondicioncompraController@eliminar')->name('eliminar_condicioncompra');
  
/* 
 * Condiciones de entrega
 */

 Route::get('compras/condicionentrega', 'Compras\CondicionentregaController@index')->name('condicionentrega');
 Route::get('compras/condicionentrega/crear', 'Compras\CondicionentregaController@crear')->name('crear_condicionentrega');
 Route::post('compras/condicionentrega', 'Compras\CondicionentregaController@guardar')->name('guardar_condicionentrega');
 Route::get('compras/condicionentrega/{id}/editar', 'Compras\CondicionentregaController@editar')->name('editar_condicionentrega');
 Route::put('compras/condicionentrega/{id}', 'Compras\CondicionentregaController@actualizar')->name('actualizar_condicionentrega');
 Route::delete('compras/condicionentrega/{id}', 'Compras\CondicionentregaController@eliminar')->name('eliminar_condicionentrega'); 

/* 
 * Tipos de empresa
 */

 Route::get('compras/tipoempresa', 'Compras\TipoempresaController@index')->name('tipoempresa');
 Route::get('compras/tipoempresa/crear', 'Compras\TipoempresaController@crear')->name('crear_tipoempresa');
 Route::post('compras/tipoempresa', 'Compras\TipoempresaController@guardar')->name('guardar_tipoempresa');
 Route::get('compras/tipoempresa/{id}/editar', 'Compras\TipoempresaController@editar')->name('editar_tipoempresa');
 Route::put('compras/tipoempresa/{id}', 'Compras\TipoempresaController@actualizar')->name('actualizar_tipoempresa');
 Route::delete('compras/tipoempresa/{id}', 'Compras\TipoempresaController@eliminar')->name('eliminar_tipoempresa'); 

 /* 
 * Retenciones de ganancia
 */

 Route::get('compras/retencionganancia', 'Compras\RetenciongananciaController@index')->name('retencionganancia');
 Route::get('compras/retencionganancia/crear', 'Compras\RetenciongananciaController@crear')->name('crear_retencionganancia');
 Route::post('compras/retencionganancia', 'Compras\RetenciongananciaController@guardar')->name('guardar_retencionganancia');
 Route::get('compras/retencionganancia/{id}/editar', 'Compras\RetenciongananciaController@editar')->name('editar_retencionganancia');
 Route::put('compras/retencionganancia/{id}', 'Compras\RetenciongananciaController@actualizar')->name('actualizar_retencionganancia');
 Route::delete('compras/retencionganancia/{id}', 'Compras\RetenciongananciaController@eliminar')->name('eliminar_retencionganancia'); 

/* 
 * Retenciones de iva
 */

 Route::get('compras/retencioniva', 'Compras\RetencionivaController@index')->name('retencioniva');
 Route::get('compras/retencioniva/crear', 'Compras\RetencionivaController@crear')->name('crear_retencioniva');
 Route::post('compras/retencioniva', 'Compras\RetencionivaController@guardar')->name('guardar_retencioniva');
 Route::get('compras/retencioniva/{id}/editar', 'Compras\RetencionivaController@editar')->name('editar_retencioniva');
 Route::put('compras/retencioniva/{id}', 'Compras\RetencionivaController@actualizar')->name('actualizar_retencioniva');
 Route::delete('compras/retencioniva/{id}', 'Compras\RetencionivaController@eliminar')->name('eliminar_retencioniva'); 

/* 
 * Retenciones de suss
 */

 Route::get('compras/retencionsuss', 'Compras\RetencionsussController@index')->name('retencionsuss');
 Route::get('compras/retencionsuss/crear', 'Compras\RetencionsussController@crear')->name('crear_retencionsuss');
 Route::post('compras/retencionsuss', 'Compras\RetencionsussController@guardar')->name('guardar_retencionsuss');
 Route::get('compras/retencionsuss/{id}/editar', 'Compras\RetencionsussController@editar')->name('editar_retencionsuss');
 Route::put('compras/retencionsuss/{id}', 'Compras\RetencionsussController@actualizar')->name('actualizar_retencionsuss');
 Route::delete('compras/retencionsuss/{id}', 'Compras\RetencionsussController@eliminar')->name('eliminar_retencionsuss'); 

/* 
 * Retenciones de IIBB
 */

 Route::get('compras/retencionIIBB', 'Compras\RetencionIIBBController@index')->name('retencionIIBB');
 Route::get('compras/retencionIIBB/crear', 'Compras\RetencionIIBBController@crear')->name('crear_retencionIIBB');
 Route::post('compras/retencionIIBB', 'Compras\RetencionIIBBController@guardar')->name('guardar_retencionIIBB');
 Route::get('compras/retencionIIBB/{id}/editar', 'Compras\RetencionIIBBController@editar')->name('editar_retencionIIBB');
 Route::put('compras/retencionIIBB/{id}', 'Compras\RetencionIIBBController@actualizar')->name('actualizar_retencionIIBB');
 Route::delete('compras/retencionIIBB/{id}', 'Compras\RetencionIIBBController@eliminar')->name('eliminar_retencionIIBB'); 

/* 
 * Tipo de suspension de proveedores
 */

 Route::get('compras/tiposuspensionproveedor', 'Compras\TiposuspensionproveedorController@index')->name('tiposuspensionproveedor');
 Route::get('compras/tiposuspensionproveedor/crear', 'Compras\TiposuspensionproveedorController@crear')->name('crear_tiposuspensionproveedor');
 Route::post('compras/tiposuspensionproveedor', 'Compras\TiposuspensionproveedorController@guardar')->name('guardar_tiposuspensionproveedor');
 Route::get('compras/tiposuspensionproveedor/{id}/editar', 'Compras\TiposuspensionproveedorController@editar')->name('editar_tiposuspensionproveedor');
 Route::put('compras/tiposuspensionproveedor/{id}', 'Compras\TiposuspensionproveedorController@actualizar')->name('actualizar_tiposuspensionproveedor');
 Route::delete('compras/tiposuspensionproveedor/{id}', 'Compras\TiposuspensionproveedorController@eliminar')->name('eliminar_tiposuspensionproveedor'); 

/* 
 * Columnas de iva compras
 */

 Route::get('compras/columna_ivacompra', 'Compras\Columna_IvacompraController@index')->name('columna_ivacompra');
 Route::get('compras/columna_ivacompra/crear', 'Compras\Columna_IvacompraController@crear')->name('crear_columna_ivacompra');
 Route::post('compras/columna_ivacompra', 'Compras\Columna_IvacompraController@guardar')->name('guardar_columna_ivacompra');
 Route::get('compras/columna_ivacompra/{id}/editar', 'Compras\Columna_IvacompraController@editar')->name('editar_columna_ivacompra');
 Route::put('compras/columna_ivacompra/{id}', 'Compras\Columna_IvacompraController@actualizar')->name('actualizar_columna_ivacompra');
 Route::delete('compras/columna_ivacompra/{id}', 'Compras\Columna_IvacompraController@eliminar')->name('eliminar_columna_ivacompra'); 

/* 
 * Conceptos de iva compras
 */

 Route::get('compras/concepto_ivacompra', 'Compras\Concepto_IvacompraController@index')->name('concepto_ivacompra');
 Route::get('compras/concepto_ivacompra/crear', 'Compras\Concepto_IvacompraController@crear')->name('crear_concepto_ivacompra');
 Route::post('compras/concepto_ivacompra', 'Compras\Concepto_IvacompraController@guardar')->name('guardar_concepto_ivacompra');
 Route::get('compras/concepto_ivacompra/{id}/editar', 'Compras\Concepto_IvacompraController@editar')->name('editar_concepto_ivacompra');
 Route::put('compras/concepto_ivacompra/{id}', 'Compras\Concepto_IvacompraController@actualizar')->name('actualizar_concepto_ivacompra');
 Route::delete('compras/concepto_ivacompra/{id}', 'Compras\Concepto_IvacompraController@eliminar')->name('eliminar_concepto_ivacompra'); 

/* 
 * Tipo de transaccion de compras
 */

 Route::get('compras/tipotransaccion_compra', 'Compras\Tipotransaccion_CompraController@index')->name('tipotransaccion_compra');
 Route::get('compras/tipotransaccion_compra/crear', 'Compras\Tipotransaccion_CompraController@crear')->name('crear_tipotransaccion_compra');
 Route::post('compras/tipotransaccion_compra', 'Compras\Tipotransaccion_CompraController@guardar')->name('guardar_tipotransaccion_compra');
 Route::get('compras/tipotransaccion_compra/{id}/editar', 'Compras\Tipotransaccion_CompraController@editar')->name('editar_tipotransaccion_compra');
 Route::put('compras/tipotransaccion_compra/{id}', 'Compras\Tipotransaccion_CompraController@actualizar')->name('actualizar_tipotransaccion_compra');
 Route::delete('compras/tipotransaccion_compra/{id}', 'Compras\Tipotransaccion_CompraController@eliminar')->name('eliminar_tipotransaccion_compra'); 

/* 
 * Proveedores
 */

Route::get('compras/proveedor', 'Compras\ProveedorController@index')->name('proveedor');
Route::get('compras/proveedor/crear/{tipoalta?}', 'Compras\ProveedorController@crear')->name('crear_proveedor');
Route::post('compras/proveedor', 'Compras\ProveedorController@guardar')->name('guardar_proveedor');
Route::post('compras/proveedorprovisorio', 'Compras\ProveedorController@guardarClienteProvisorio')->name('guardar_proveedor_provisorio');
Route::get('compras/proveedor/{id}/editar', 'Compras\ProveedorController@editar')->name('editar_proveedor');
Route::put('compras/proveedor/{id}', 'Compras\ProveedorController@actualizar')->name('actualizar_proveedor');
Route::delete('compras/proveedor/{id}', 'Compras\ProveedorController@eliminar')->name('eliminar_proveedor');

/* Modulo receptivo */

/* 
 * Tipo de servicio terrestre
 */

 Route::get('receptivo/tiposervicioterrestre', 'Receptivo\TiposervicioterrestreController@index')->name('tiposervicioterrestre');
 Route::get('receptivo/tiposervicioterrestre/crear', 'Receptivo\TiposervicioterrestreController@crear')->name('crear_tiposervicioterrestre');
 Route::post('receptivo/tiposervicioterrestre', 'Receptivo\TiposervicioterrestreController@guardar')->name('guardar_tiposervicioterrestre');
 Route::get('receptivo/tiposervicioterrestre/{id}/editar', 'Receptivo\TiposervicioterrestreController@editar')->name('editar_tiposervicioterrestre');
 Route::put('receptivo/tiposervicioterrestre/{id}', 'Receptivo\TiposervicioterrestreController@actualizar')->name('actualizar_tiposervicioterrestre');
 Route::delete('receptivo/tiposervicioterrestre/{id}', 'Receptivo\TiposervicioterrestreController@eliminar')->name('eliminar_tiposervicioterrestre'); 

/* 
 * Servicio terrestre
 */

 Route::get('receptivo/servicioterrestre', 'Receptivo\ServicioterrestreController@index')->name('servicioterrestre');
 Route::get('receptivo/servicioterrestre/crear', 'Receptivo\ServicioterrestreController@crear')->name('crear_servicioterrestre');
 Route::post('receptivo/servicioterrestre', 'Receptivo\ServicioterrestreController@guardar')->name('guardar_servicioterrestre');
 Route::get('receptivo/servicioterrestre/{id}/editar', 'Receptivo\ServicioterrestreController@editar')->name('editar_servicioterrestre');
 Route::put('receptivo/servicioterrestre/{id}', 'Receptivo\ServicioterrestreController@actualizar')->name('actualizar_servicioterrestre');
 Route::delete('receptivo/servicioterrestre/{id}', 'Receptivo\ServicioterrestreController@eliminar')->name('eliminar_servicioterrestre'); 

/* 
 * Servicios por proveedor
 */

 Route::get('receptivo/proveedor_servicioterrestre', 'Receptivo\Proveedor_ServicioterrestreController@index')->name('proveedor_servicioterrestre');
 Route::get('receptivo/proveedor_servicioterrestre/crear', 'Receptivo\Proveedor_ServicioterrestreController@crear')->name('crear_proveedor_servicioterrestre');
 Route::post('receptivo/proveedor_servicioterrestre', 'Receptivo\Proveedor_ServicioterrestreController@guardar')->name('guardar_proveedor_servicioterrestre');
 Route::get('receptivo/proveedor_servicioterrestre/{id}/editar', 'Receptivo\Proveedor_ServicioterrestreController@editar')->name('editar_proveedor_servicioterrestre');
 Route::put('receptivo/proveedor_servicioterrestre/{id}', 'Receptivo\Proveedor_ServicioterrestreController@actualizar')->name('actualizar_proveedor_servicioterrestre');
 Route::delete('receptivo/proveedor_servicioterrestre/{id}', 'Receptivo\Proveedor_ServicioterrestreController@eliminar')->name('eliminar_proveedor_servicioterrestre'); 

/* 
 * Idiomas
 */

 Route::get('receptivo/idioma', 'Receptivo\IdiomaController@index')->name('idioma');
 Route::get('receptivo/idioma/crear', 'Receptivo\IdiomaController@crear')->name('crear_idioma');
 Route::post('receptivo/idioma', 'Receptivo\IdiomaController@guardar')->name('guardar_idioma');
 Route::get('receptivo/idioma/{id}/editar', 'Receptivo\IdiomaController@editar')->name('editar_idioma');
 Route::put('receptivo/idioma/{id}', 'Receptivo\IdiomaController@actualizar')->name('actualizar_idioma');
 Route::delete('receptivo/idioma/{id}', 'Receptivo\IdiomaController@eliminar')->name('eliminar_idioma');

/* 
 * Guias
 */

 Route::get('receptivo/guia', 'Receptivo\GuiaController@index')->name('guia');
 Route::get('receptivo/guia/crear', 'Receptivo\GuiaController@crear')->name('crear_guia');
 Route::post('receptivo/guia', 'Receptivo\GuiaController@guardar')->name('guardar_guia');
 Route::get('receptivo/guia/{id}/editar', 'Receptivo\GuiaController@editar')->name('editar_guia');
 Route::put('receptivo/guia/{id}', 'Receptivo\GuiaController@actualizar')->name('actualizar_guia');
 Route::delete('receptivo/guia/{id}', 'Receptivo\GuiaController@eliminar')->name('eliminar_guia');

/* 
 * Comisiones por servicio
 */

 Route::get('receptivo/comision_servicioterrestre', 'Receptivo\Comision_ServicioterrestreController@index')->name('comision_servicioterrestre');
 Route::get('receptivo/comision_servicioterrestre/crear', 'Receptivo\Comision_ServicioterrestreController@crear')->name('crear_comision_servicioterrestre');
 Route::post('receptivo/comision_servicioterrestre', 'Receptivo\Comision_ServicioterrestreController@guardar')->name('guardar_comision_servicioterrestre');
 Route::get('receptivo/comision_servicioterrestre/{id}/editar', 'Receptivo\Comision_ServicioterrestreController@editar')->name('editar_comision_servicioterrestre');
 Route::put('receptivo/comision_servicioterrestre/{id}', 'Receptivo\Comision_ServicioterrestreController@actualizar')->name('actualizar_comision_servicioterrestre');
 Route::delete('receptivo/comision_servicioterrestre/{id}', 'Receptivo\Comision_ServicioterrestreController@eliminar')->name('eliminar_comision_servicioterrestre');
 Route::get('receptivo/leecomision/{formapago_id}/{tipocomision}/{servicioterrestre_id}', 'Receptivo\Comision_ServicioterrestreController@leeComision')->name('lee_comision');

/*
 * Reserva
 */

 Route::get('receptivo/leereserva/{reserva}', 'Receptivo\ReservaController@leeReserva')->name('lee_reserva');
 Route::post('receptivo/reserva/consultareserva', 'Receptivo\ReservaController@consultaReserva')->name('consulta_reserva');