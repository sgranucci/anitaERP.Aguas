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
 * Monedas
 */

Route::get('configuracion/moneda', 'Configuracion\MonedaController@index')->name('moneda');
Route::get('configuracion/moneda/crear', 'Configuracion\MonedaController@crear')->name('crear_moneda');
Route::post('configuracion/moneda', 'Configuracion\MonedaController@guardar')->name('guardar_moneda');
Route::get('configuracion/moneda/{id}/editar', 'Configuracion\MonedaController@editar')->name('editar_moneda');
Route::put('configuracion/moneda/{id}', 'Configuracion\MonedaController@actualizar')->name('actualizar_moneda');
Route::delete('configuracion/moneda/{id}', 'Configuracion\MonedaController@eliminar')->name('eliminar_moneda');

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

// Reportes de stock

Route::get('stock/catalogo', 'Stock\CombinacionController@catalogo')->name('catalogo');
Route::post('stock/crearCatalogo', 'Stock\CombinacionController@crearCatalogo')->name('crear_catalogo');

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
 * Cuentas contables
 */

Route::get('contable/cuentacontable', 'Contable\CuentacontableController@index')->name('cuentacontable');
Route::get('contable/cuentacontable/crear', 'Contable\CuentacontableController@crear')->name('crear_cuentacontable');
Route::post('contable/cuentacontable', 'Contable\CuentacontableController@guardar')->name('guardar_cuentacontable');
Route::get('contable/cuentacontable/{id}/editar', 'Contable\CuentacontableController@editar')->name('editar_cuentacontable');
Route::put('contable/cuentacontable/{id}', 'Contable\CuentacontableController@actualizar')->name('actualizar_cuentacontable');
Route::get('contable/cuentacontable/{id}/eliminar', 'Contable\CuentacontableController@eliminar')->name('eliminar_cuentacontable');
Route::post('contable/cuentacontable/guardar-orden', 'Contable\CuentacontableController@guardarOrden')->name('guardar_orden');

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

Route::get('stock/leercombinaciones/{id}', 'Stock\CombinacionController@leerCombinaciones')->name('leer_combinaciones');
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

// Reportes de ventas

Route::get('ventas/reppercepcioniibb', 'Ventas\ReppercepcioniibbController@index')->name('listar_percepcioniibb');
Route::post('ventas/crearreppercepcioniibb', 'Ventas\ReppercepcioniibbController@crearReporteControlPercepcionesIIBB')->name('crear_reppercepcioniibb');


/* Modulo de ventas

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
 * Clientes
 */

Route::get('ventas/cliente', 'Ventas\ClienteController@index')->name('cliente');
Route::get('ventas/cliente/crear', 'Ventas\ClienteController@crear')->name('crear_cliente');
Route::post('ventas/cliente', 'Ventas\ClienteController@guardar')->name('guardar_cliente');
Route::get('ventas/cliente/{id}/editar', 'Ventas\ClienteController@editar')->name('editar_cliente');
Route::put('ventas/cliente/{id}', 'Ventas\ClienteController@actualizar')->name('actualizar_cliente');
Route::delete('ventas/cliente/{id}', 'Ventas\ClienteController@eliminar')->name('eliminar_cliente');

/* 
 * Pedidos
 */

Route::get('ventas/pedido', 'Ventas\PedidoController@index')->name('pedido');
Route::get('ventas/pedido/crear', 'Ventas\PedidoController@crear')->name('crear_pedido');
Route::post('ventas/pedido', 'Ventas\PedidoController@guardar')->name('guardar_pedido');
Route::get('ventas/pedido/{id}/editar', 'Ventas\PedidoController@editar')->name('editar_pedido');
Route::put('ventas/pedido/{id}', 'Ventas\PedidoController@actualizar')->name('actualizar_pedido');
Route::delete('ventas/pedido/{id}', 'Ventas\PedidoController@eliminar')->name('eliminar_pedido');

