<?php

namespace App\Http\Controllers;

use App\Library\Veiculo;
use GuzzleHttp\Client as Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BuscaController extends Controller
{        

    // URL PARA A BUSCA DE MARCAS DE UM TIPO DE VEICULO
    const BASE_URL_MARCAS = 'https://www.seminovosbh.com.br/marcas/buscamarca/tipo/';

    // URL PARA A BUSCA DE MODELOS DE UMA MARCA
    const BASE_URL_MODELOS = 'https://www.seminovosbh.com.br/json/modelos/buscamodelo/marca/:marca:/data.js';

    // URL PARA A BUSCA DE CIDADES
    const BASE_URL_CIDADE = 'https://www.seminovosbh.com.br/json/index/busca-cidades/:veiculoid:/:marcaid:/:modeloid:/:cidadeid:/data.js';    

    // RETORNA OS TIPOS DE VEICULOS DISPONIVEIS PARA A BUSCA
    public function opcoesVeiculos()
    {
        header('Content-Type: application/json');
        return json_encode(['success' => true, 'data' => ['1' => 'carros', '2' => 'caminhoes', '3' => 'motos']]);
    }

    // RETORNA AS MARCAS DISPONIVEIS DE UM TIPO DE VEICULOS
    public function marcasVeiculos($idTipoVeiculo = null)
    {
        header('Content-Type: application/json');

        $return = [];
        if(empty($idTipoVeiculo)){
            $return['success'] = false;
            $return['error'] = 'O tipo de veiculo é obrigatório';
        }else{
            try{
                // busca das marcas
                $httpClient = new Client();
                $res = $httpClient->request('GET', self::BASE_URL_MARCAS . $idTipoVeiculo);
                if(intval($res->getStatusCode()) == 200){
                  $data = $res->getBody();                 
                  $arMarcas = json_decode($data->getContents(), true);
                  $return['success'] = true;
                  $return['data'] = $arMarcas;                  
                }else{
                    // requisicao retornou um status nao esperado
                    $return['success'] = false;
                    $return['error'] = "Não foi possível obter as marcas.";
                }
            }catch(\Exception $e){
                // tratativa de excessao 
                $return['success'] = false;
                $return['error'] = "Não foi possível obter as marcas.";
            }                                    
        }

        return json_encode($return);
    }

    // RETORNA OS MODELOS DE VEICULOS DE UMA MARCA
    public function modelosVeiculos($idMarca = null)
    {
        header('Content-Type: application/json');

        $return = [];
        if(empty($idMarca)){
            $return['success'] = false;
            $return['error'] = 'O código da marca é obrigatório';
        }else{
            try{
                // busca das modelos
                $httpClient = new Client();
                $res = $httpClient->request('GET', str_replace(':marca:', $idMarca, self::BASE_URL_MODELOS));             
                if(intval($res->getStatusCode()) == 200){
                  $data = $res->getBody();                 
                  $arMarcas = json_decode($data->getContents(), true);
                  $return['success'] = true;
                  $return['data'] = $arMarcas;                  
                }else{
                    // requisicao retornou um status nao esperado
                    $return['success'] = false;
                    $return['error'] = "Não foi possível obter os modelos.";
                }
            }catch(\Exception $e){
                // tratativa de excessao 
                $return['success'] = false;
                $return['error'] = "Não foi possível obter os modelos.";
            }                                    
        }

        return json_encode($return); 
    }

    // RETORNA AS CIDADES COM DISPONIBILIDADE
    public function buscaCidades($idTipoVeiculo = null, $idMarca = 0, $idModelo = 0, $idCidade = 0)
    {

        header('Content-Type: application/json');

        $return = [];
        if(empty($idTipoVeiculo)){
            $return['success'] = false;
            $return['error'] = 'O tipo do veiculo é obrigatório';
        }else{
            try{                                

                // checagem de parametros
                $completeURL = str_replace(
                    [':veiculoid:', ':marcaid:', ':modeloid:', ':cidadeid:'], 
                    ['veiculo/'.$idTipoVeiculo, 'marca/'.$idMarca, 'modelo/'.$idModelo, 'cidade/'.$idCidade],
                    self::BASE_URL_CIDADE
                );

                $httpClient = new Client();
                $res = $httpClient->request('GET', $completeURL);             
                if(intval($res->getStatusCode()) == 200){
                  $data = $res->getBody();                 
                  $arMarcas = json_decode($data->getContents(), true);
                  $return['success'] = true;
                  $return['data'] = $arMarcas;                  
                }else{
                    // requisicao retornou um status nao esperado
                    $return['success'] = false;
                    $return['error'] = "Não foi possível obter cidades.";
                }
            }catch(\Exception $e){
                // tratativa de excessao 
                $return['success'] = false;
                $return['error'] = "Não foi possível obter cidades.";
            }                                    
        }

        return json_encode($return);
    }

    // BUSCA VEICULOS CORRESPONDENTES AO FILTRO
    public function buscaVeiculos(Request $request)
    {
        // recupera os parametros enviados via post
        $post = $request->all();

        // aciona a filtragem de resultados
        $veiculo = new Veiculo(); 
        header('Content-Type: application/json');       
        return $veiculo->filterVeiculos($post);
    }
    
}