<?php

use App\Http\Controllers\FaturaProformaController;
use App\Http\Controllers\ReciboPedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/recibo-pedido/{idSolicitacao}', [ReciboPedidoController::class, 'show'])
    ->name('recibo-pedido.show');

Route::get('/fatura-proforma', [FaturaProformaController::class, 'show'])
    ->name('fatura-proforma.show');
