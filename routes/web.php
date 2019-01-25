<?php

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

/* ROTAS PARA A API */
Route::prefix('api')->group(function () {
    
    Route::prefix('veiculos')->group(function () {        

        // visualiza dados de veiculo especifico
        Route::get('visualizar/{id}', 'VeiculoController@visualizar')->name('api.veiculo.visualizar');

        // mecanimos de busca e filtragem de veiculos
        Route::prefix('busca')->group(function(){

            // recupera os tipos de veiculos disponíveis para a busca
            Route::get('opcoesveiculo', 'BuscaController@opcoesVeiculos')->name('api.veiculo.busca.opcoesveiculo');

            // recupera as marcas de um tipo de veiculo
            Route::get('marcas/{idTipoVeiculo}', 'BuscaController@marcasVeiculos')->name('api.veiculo.busca.marcasveiculos');

            // recupera os modelos de veiculos de uma marca
            Route::get('modelos/{idMarca}', 'BuscaController@modelosVeiculos')->name('api.veiculo.busca.modelosveiculos');

            // recupera as cidades com disponibilidade de veiculos
            Route::prefix('cidades')->group(function(){

                // buscando cidades pelo tipo de veiculo
                Route::get('veiculo/{idTipoVeiculo}', 'BuscaController@buscaCidades')->name('api.veiculo.busca.cidade');

                // buscando cidade pelo tipo de veiculo e marca
                Route::get('veiculo/{idTipoVeiculo}/marca/{idMarca?}', 'BuscaController@buscaCidades')->name('api.veiculo.busca.cidade.marcas');

                // buscando cidade pelo tipo de veiculo, marca e modelo
                Route::get('veiculo/{idTipoVeiculo}/marca/{idMarca?}/modelo/{idModelo?}', 'BuscaController@buscaCidades')->name('api.veiculo.busca.cidade.marca.modelo');

            });

            // filtragem de resultados de veiculos
            Route::post('filtragem', 'BuscaController@buscaVeiculos')->name('api.veiculo.busca.filtragem');
            
        });
    });
});