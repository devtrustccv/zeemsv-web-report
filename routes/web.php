<?php

use App\Http\Controllers\FaturaProformaController;
use App\Http\Controllers\ReciboPedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/recibo-pedido/{idSolicitacao}', [ReciboPedidoController::class, 'show'])
    ->name('recibo-pedido.show');

Route::get('/recibo-pedido/{idSolicitacao}/html', [ReciboPedidoController::class, 'showHtml'])
    ->name('recibo-pedido.html');

Route::get('/fatura-proforma/{idSolicitacao}', [FaturaProformaController::class, 'show'])
    ->whereNumber('idSolicitacao')
    ->name('fatura-proforma.show');

Route::get('/fatura-proforma/{idSolicitacao}/html', [FaturaProformaController::class, 'showHtml'])
    ->whereNumber('idSolicitacao')
    ->name('fatura-proforma.html');

Route::get('/fatura-proforma', [FaturaProformaController::class, 'show'])
    ->name('fatura-proforma.preview');
