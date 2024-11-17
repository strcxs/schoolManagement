<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('kelas')->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('kelas.index');
    Route::post('/edit',[DashboardController::class,'edit'])->name('kelas.edit');
    Route::get('/destroy',[DashboardController::class,'index'])->name('kelas.destroy');
});

Route::prefix('mapel')->group(function(){
    Route::get('/',[MapelController::class,'index'])->name('mapel.index');
});

Route::prefix('guru')->group(function(){
    Route::get('/',[GuruController::class,'index'])->name('guru.index');
});

Route::prefix('siswa')->group(function(){
    Route::get('/',[SiswaController::class,'index'])->name('siswa.index');
});

Route::prefix('agenda')->group(function(){
    Route::get('/',[AgendaController::class,'index'])->name('agenda.index');
});

Route::prefix('role')->group(function(){
    Route::get('/',[RoleController::class,'index'])->name('role.index');
});

Route::prefix('absensi')->group(function(){
    Route::get('/',[AbsensiController::class,'index'])->name('absensi.index');
});