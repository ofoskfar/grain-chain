<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CargarArchivo;
use App\Http\Controllers\CalculoBombillos;

Route::get('/', [CargarArchivo::class, 'muestraForm'])->name('Inicio');

Route::post('/cargar_txt', [CargarArchivo::class, 'cargarTxt'])->name('cargarTxt');

Route::get('/calcular_bombillos', [CalculoBombillos::class, 'index'])->name('indexBombillos');