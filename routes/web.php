<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\ImageController;

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
/*** Moved some web.config rewrites/redirects to these routes. Since Laravel toolkit, and others, for Plesk messes web.config up) ******************/
Route::get('/media/{year}/{month}/{file}', [ImageController::class, 'renderImage'])->where(['year' => '[0-9]{4}','month' => '[0-9]{2}']); // rewrite for media
Route::get('/wp-json/carbon-fields/v1/attachment', function () {return redirect(str_replace('/wp-json/carbon-fields/v1/attachment', '/_mcfu638b-cms/index.php/wp-json/carbon-fields/v1/attachment', Request::fullUrl()));}); // redirect for Carbon Fields -bug
Route::get('/wp-json/carbon-fields/v1/association', function () {return redirect(str_replace('/wp-json/carbon-fields/v1/association', '/_mcfu638b-cms/index.php/wp-json/carbon-fields/v1/association', Request::fullUrl()));}); // redirect for Carbon Fields -bug
Route::get('/wp-json/carbon-fields/v1/association/options', function () {return redirect(str_replace('/wp-json/carbon-fields/v1/association/options', '/_mcfu638b-cms/index.php/wp-json/carbon-fields/v1/association/options', Request::fullUrl()));}); // redirect for Carbon Fields -bug
Route::get('/admin', function () {return redirect('/_mcfu638b-cms/wp-admin');}); // redirect /admin to wp-cms
/***************************************************************************************************************************************************/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::get('/homepage', [PagesController::class, 'showOnePager'])->name('home');


Route::get('/{section}', [PagesController::class, 'showPage'])->defaults('page', false)->defaults('subpage', false)->where([
    'section' => '[a-z0-9_-]+',
]);
Route::get('/{section}/{page}', [PagesController::class, 'showPage'])->defaults('subpage', false)->where([
    'section' => '[a-z0-9_-]+',
    'page' => '[a-z0-9_-]+',
]);
Route::get('/{section}/{page}/{subpage}', [PagesController::class, 'showPage'])->where([
    'section' => '[a-z0-9_-]+',
    'page' => '[a-z0-9_-]+',
    'subpage' => '[a-z0-9_-]+',
]);
