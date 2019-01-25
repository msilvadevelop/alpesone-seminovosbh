<?php

namespace App\Http\Controllers;

use App\Library\Veiculo;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VeiculoController extends Controller
{        

    /* RECUPERA DADOS DO VEÍCULO PELO SEU ID */
    public function visualizar(Request $request, $id)
    {
        header('Content-Type: application/json');
        if(empty($id)){            
            return json_encode(['success' => false, 'error' => 'É necessário informar o ID do veiculo']);
        }else{
            $veiculolb = new Veiculo();
            return json_encode($veiculolb->getVeiculoID($id));               
        }
    }
}