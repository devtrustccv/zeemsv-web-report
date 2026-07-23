<?php

use App\Http\Controllers\ReciboPedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/recibo-pedido/{idSolicitacao}', [ReciboPedidoController::class, 'show'])
    ->name('recibo-pedido.show');
