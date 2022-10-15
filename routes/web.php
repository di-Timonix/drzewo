<?php

use App\Http\Controllers\QueryDataWood;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Name;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api', [QueryDataWood::class, "getData"])->name("post.getData");


Route::post('/api/update', [QueryDataWood::class, "updateData"]);
