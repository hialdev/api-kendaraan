<?php

use App\Http\Controllers\API\AjukanController;
use App\Http\Controllers\API\KendaraanController;
use App\Http\Controllers\API\PegawaiController;
use App\Http\Controllers\API\PengemudiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('login',[PegawaiController::class,'login']);
Route::get('pegawai',[PegawaiController::class,'index']);
Route::get('pegawai/{email}',[PegawaiController::class,'byEmail']);

Route::get('pengajuan',[AjukanController::class,'index']);
Route::post('pengajuan',[AjukanController::class,'store']);
Route::get('pengajuan/{id}',[AjukanController::class,'lihat']);
Route::put('pengajuan/{id}',[AjukanController::class,'update']);
Route::delete('pengajuan/{id}',[AjukanController::class,'destroy']);
Route::get('pengajuan/u/{username}',[AjukanController::class,'ajuanKu']);
Route::post('pengajuan/{id}/setujui',[AjukanController::class,'approved']);
Route::post('pengajuan/{id}/tolak',[AjukanController::class,'reject']);

Route::get('pengemudi',[PengemudiController::class,'index']);
Route::post('pengemudi',[PengemudiController::class,'store']);
Route::get('pengemudi/s/{status}',[PengemudiController::class,'byStatus']);
Route::get('pengemudi/{id}',[PengemudiController::class,'show']);
Route::put('pengemudi/{id}',[PengemudiController::class,'update']);

Route::get('kendaraan',[KendaraanController::class,'index']);
Route::post('kendaraan',[KendaraanController::class,'store']);
Route::get('kendaraan/s/{status}',[KendaraanController::class,'byStatus']);
Route::get('kendaraan/{id}',[KendaraanController::class,'show']);
Route::put('kendaraan/{id}',[KendaraanController::class,'update']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
