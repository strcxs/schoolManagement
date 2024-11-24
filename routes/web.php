<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('login');
})->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::prefix('kelas')->group(function(){
        Route::get('/',[DashboardController::class,'index'])->name('kelas.index');
        Route::post('/edit',[DashboardController::class,'edit'])->name('kelas.edit');
        Route::post('/add',[DashboardController::class,'add'])->name('kelas.add');
        Route::get('/delete',[DashboardController::class,'delete'])->name('kelas.delete');
    });

    Route::prefix('mapel')->group(function(){
        Route::get('/',[MapelController::class,'index'])->name('mapel.index');
        Route::post('/edit',[MapelController::class,'edit'])->name('mapel.edit');
        Route::post('/add',[MapelController::class,'add'])->name('mapel.add');
        Route::post('/delete',[MapelController::class,'delete'])->name('mapel.delete');
    });

    Route::prefix('guru')->group(function(){
        Route::get('/',[GuruController::class,'index'])->name('guru.index');
        Route::post('/edit',[GuruController::class,'edit'])->name('guru.edit');
        Route::post('/add',[GuruController::class,'add'])->name('guru.add');
        Route::post('/delete',[GuruController::class,'delete'])->name('guru.delete');
    });

    Route::prefix('siswa')->group(function(){
        Route::get('/',[SiswaController::class,'index'])->name('siswa.index');
        Route::post('/edit',[SiswaController::class,'edit'])->name('siswa.edit');
        Route::post('/add',[SiswaController::class,'add'])->name('siswa.add');
        Route::post('/delete',[SiswaController::class,'delete'])->name('siswa.delete');
    });

    Route::prefix('agenda')->group(function(){
        Route::get('/',[AgendaController::class,'index'])->name('agenda.index');
        Route::post('/edit',[AgendaController::class,'edit'])->name('agenda.edit');
        Route::post('/add',[AgendaController::class,'add'])->name('agenda.add');
        Route::post('/delete',[AgendaController::class,'delete'])->name('agenda.delete');

        Route::get('/download',[AgendaController::class,'generatePdf'])->name('agenda.generatePdf');

        
        Route::get('/absensi/{data}',[AgendaController::class,'absensi'])->name('agenda.absensi');
        Route::post('/absensi/save',[AgendaController::class,'save'])->name('agenda.save');
    });

    Route::prefix('role')->group(function(){
        Route::get('/',[RoleController::class,'index'])->name('role.index');
        Route::post('/edit',[RoleController::class,'edit'])->name('role.edit');
        Route::post('/add',[RoleController::class,'add'])->name('role.add');
        Route::post('/delete',[RoleController::class,'delete'])->name('role.delete');
    });

    Route::prefix('absensi')->group(function(){
        Route::get('/',[AbsensiController::class,'index'])->name('absensi.index');
        Route::post('/edit',[AbsensiController::class,'edit'])->name('absensi.edit');

        Route::get('/download',[AbsensiController::class,'generatePdf'])->name('absensi.generatePdf');
    });
});