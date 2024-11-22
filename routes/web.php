<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;

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
    Route::post('/edit',[MapelController::class,'edit'])->name('mapel.edit');
});

Route::prefix('guru')->group(function(){
    Route::get('/',[GuruController::class,'index'])->name('guru.index');
    Route::post('/edit',[GuruController::class,'edit'])->name('guru.edit');
});

Route::prefix('siswa')->group(function(){
    Route::get('/',[SiswaController::class,'index'])->name('siswa.index');
    Route::post('/edit',[SiswaController::class,'edit'])->name('siswa.edit');
});

Route::prefix('agenda')->group(function(){
    Route::get('/',[AgendaController::class,'index'])->name('agenda.index');
    Route::post('/edit',[AgendaController::class,'edit'])->name('agenda.edit');
});

Route::prefix('role')->group(function(){
    Route::get('/',[RoleController::class,'index'])->name('role.index');
    Route::post('/edit',[RoleController::class,'edit'])->name('role.edit');
});

Route::prefix('absensi')->group(function(){
    Route::get('/',[AbsensiController::class,'index'])->name('absensi.index');
    Route::post('/edit',[AbsensiController::class,'edit'])->name('absensi.edit');
});