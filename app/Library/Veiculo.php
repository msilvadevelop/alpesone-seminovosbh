<?php

namespace App\Library;

use Weidner\Goutte\GoutteFacade as Goutte;

class Veiculo {

    // URL DE VISUALIZACAO DO VEICULO
    const BUSCA_URL = 'https://www.seminovosbh.com.br/comprar////';
    
    // URL DE BUSCA DE VEICULOS
    const BASE_URL_FILTROVEICULO = 'https://www.seminovosbh.com.br/resultadobusca/index';

    protected $dataCadastroVeiculo;
    protected $numVisualizacoes;

    // BUSCA PAGINA DE DADOS DE UM VEICULO
    public function getVeiculoID($id)
    {
        $fullUrlPath = self::BUSCA_URL . $id;
        $dataWebContent = Goutte::request('GET', $fullUrlPath);
        $veiculoData = $this->mapBuscaResult($dataWebContent, $id);
        if(count($veiculoData)>0){
            return ['success' => true, 'data' => $veiculoData];
        }else{
            return ['success' => false, 'error' => 'Nenhum resultado encontrado.'];
        }
    }

    // MAPEIA OS RESULTADOS DA BUSCA
    protected function mapBuscaResult($crawler, $id)
    {     
        // CHECA SE O VEICULO INFORMADO EXISTE           
        if (!$this->veiculoExiste($crawler)){ return ['message' => 'Veículo Inexistente'];}

        // MAPEAMENTO DOS DADOS DO VEICULO
        $veiculo = [];
        $veiculo['id'] = $id;
        $veiculo['imagens'] = $this->mapImgVeiculo($crawler);                
        $veiculo['anuncio'] = $this->mapTitleVeiculo($crawler);        
        $veiculo['preco'] = $this->mapPriceVeiculo($crawler);
        $veiculo['detalhes'] = $this->mapDetailsVeiculo($crawler);
        $veiculo['acessorios'] = $this->mapAcessoriosVeiculo($crawler);
        $veiculo['observacoes'] = $this->mapObsVeiculo($crawler);
        $veiculo['contato'] = $this->mapContatoVeiculo($crawler); 
        $veiculo['data_cadastro'] = $this->dataCadastroVeiculo;
        $veiculo['visualizacoes'] = $this->numVisualizacoes;               
        return $veiculo;
    }

    /* VERIFICA SE O VEICULO EXISTE */
    protected function veiculoExiste($dataWebCont)
    {
        return $dataWebCont->filter('#conteudoVeiculo')->count();
    }

    /* RECUPERA AS IMAGENS DO VEICULO */
    protected function mapImgVeiculo($dataWebCont)
    {
        $baseVeiculoImg = $dataWebCont->filter('#fotoVeiculo')->filterXPath('//img[contains(@src, "")]')->each(function ($item) {
            $itemSrc = $item->extract(['src']);
            return reset($itemSrc);
        });

        $listImgVeiculo = $dataWebCont->filter('#conteudoVeiculo')->filterXPath('//img[contains(@src, "")]')->each(function ($item) {
            $itemSrc = $item->extract(['src']);
            return reset($itemSrc);
        });

        $imgVeiDataset = [];
        foreach ($listImgVeiculo as $k => $contImg) {
            if (strpos($contImg, 'photoNone.jpg') === false) {
                $imgVeiDataset[] = $contImg;
            }
        }        

        return ['principalImg' => reset($baseVeiculoImg), 'galeria' => $imgVeiDataset];
    }

    /* RECUPERA TITULO DO ANUNCIO DO VEICULO */
    protected function mapTitleVeiculo($dataWebCont)
    {
        $nameAnunc = $dataWebCont->filter('#textoBoxVeiculo > h5')->each(function ($item) {
            return trim($item->text());
        });

        return reset($nameAnunc);
    }

    /* RECUPERA PRECO DO VEICULO */
    protected function mapPriceVeiculo($dataWebCont)
    {
        $precoVeiculo = $dataWebCont->filter('#textoBoxVeiculo > p')->each(function ($item) {
            return trim($item->text());
        });  

        $dataPriceVeiculo = reset($precoVeiculo);
        if(empty($dataPriceVeiculo)){
            return 'Sem preço';
        }else{            
            $arDataPriceVeiculo = explode(' ', $dataPriceVeiculo);        

            $precoVeiculoData = [];
            $precoVeiculoData['valor_formatado_moeda'] = $dataPriceVeiculo;
            $precoVeiculoData['valor_formatado'] = $arDataPriceVeiculo[1];
            $precoVeiculoData['valor'] = number_format(floatval(str_replace(".","", $arDataPriceVeiculo[1])), 2, ".", '');
            return $precoVeiculoData;
        }
    }

    /* RECUPERA DETALHES DO VEICULO */
    protected function mapDetailsVeiculo($dataWebCont)
    {
        return $detailsVeic = $dataWebCont->filter('#infDetalhes > span > ul > li')->each(function ($item) {
            return trim($item->text());
        });
    }

    /* RECUPERA ACESSORIOS DO VEICULO */
    protected function mapAcessoriosVeiculo($dataWebCont)
    {
        return $acessoriosVeiculo = $dataWebCont->filter('#infDetalhes2 > ul > li')->each(function ($item) {
            return trim($item->text());
        });
    }

    /* RECUPERA OBSERVACOES ANUNCIO DO VEICULO */
    protected function mapObsVeiculo($dataWebCont)
    {
        return $obsVeiculo = $dataWebCont->filter('#infDetalhes3 > ul > p')->each(function ($item) {
            return trim($item->text());
        });
    }

    /* RECUPERA CONTATO ANUNCIO DO VEICULO */
    protected function mapContatoVeiculo($dataWebCont)
    {
        $veiculoContato = $dataWebCont->filter('#infDetalhes4 .texto> ul > li')->each(function ($item) {            
            return trim($item->text());
        });

        $arDataContato = [];

        foreach($veiculoContato as $itemContato){
            // Captura de Telefones de contato
            if(preg_match('/^\([0-9]{2}\)[0-9-]*$/',$itemContato) == true){
                $arDataContato['telefones'][] = $itemContato;
                // evita possiveis duplicidades de cadastro.
                $arDataContato['telefones'] = array_unique($arDataContato['telefones']);
            }
            else if(stristr($itemContato, "Visualizações:")){
                $rexviews = preg_match('/[0-9]+/', $itemContato, $matches);                
                $this->numVisualizacoes = $matches[0]; 
            }
            else if(stristr($itemContato, "Cadastro em:")){
                $rexviews = preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $itemContato, $matches);                
                $this->dataCadastroVeiculo = $matches[0]; 
            }else if(empty($itemContato)){
                continue;
            }
            else{                
                $arDataContato[] = $itemContato;
            }
        }

        return $arDataContato;
    }

    // BUSCA PAGINA DE DADOS DE UM VEICULO
    public function filterVeiculos($dataPost = null)
    {
        if(empty($dataPost)){
            return json_encode(['success' => false, 'error' => 'Sem parâmetros de busca.']);
        }        

        // realiza requisicao de filtragem         
        $dataWebContent = Goutte::request('GET', $this->montagemURL($dataPost));

        // tratativa do resultado
        $arDataBusca = $this->obterResultadosBusca($dataWebContent);

        $arDataPag = $this->obterPaginacaoData($dataWebContent);
        if(count($arDataBusca)>0){ 
            return json_encode(['success' => true, 'data' => array_merge($arDataPag, $arDataBusca)]);
        }
        else{
            return json_encode(['success' => false, 'error' => 'Não possível retornar dados.']);
        }

    }

    // OBTEM A QUANTIDADE DE PAGINAS DE RESULTADOS
    public function obterPaginacaoData($dataWeb)
    {
        $totalPaginas = $dataWeb->filter('.total')->each(function ($item) {
            return intval($item->text());
        });

        $paginaAtual = $dataWeb->filter('.contador')->each(function ($item) {
            return intval($item->text());
        });
        return ['qtdPages' => reset($totalPaginas), 'ativePage' => reset($paginaAtual)];
    }

    // OBTEM OS RESULTADOS DA FILTRAGEM
    public function obterResultadosBusca($dataWeb)
    {
        // rastreia a listagem de nomes de veículos
        $veiculoDataPrice = $dataWeb->filter('.bg-busca .titulo-busca')->each(function ($item) {
            if(preg_match('/\s+([A-Za-z]\$\s+([0-9.]*\,[0-9]{2}))\s+/',$item->text(), $matches)){
                // formatacao do preco
                $price['preco_formatado'] = $matches[1];
                $price['preco'] = number_format(floatval(str_replace('.', '', $matches[2])), 2, '.', '');

                // formatacao do modelo do veiculo
                $title = trim(str_replace($matches[0], '', $item->text()));
                return ['modelo' => $title, 'preco' => $price];
            }
            else{
                return null;
            }
                                                          
        });        

        // recupera o ID do veiculo
        $codigos = $dataWeb->filter('.bg-busca > dt')->filterXPath('//a[contains(@href, "")]')->each(function ($item) {
            $linkToVeiculo = $item->extract(['href'])[0];
            if(preg_match('/[0-9]{4}\-[0-9]{4}\/([0-9]+)\//', $linkToVeiculo, $matches))
            {
                return $matches[1];
            }
            else{
                return null;
            }            
        });

        // contagem de dados retornados
        $in = 0; 
        $tot = count($veiculoDataPrice);
        while($in < $tot){
            $arDataSearch[$in] = [
                'codigo' => $codigos[$in], 
                'modelo' => $veiculoDataPrice[$in]['modelo'],
                'precos' => $veiculoDataPrice[$in]['preco']
            ];
            $in++;
        }        

        return $arDataSearch;
    }

    // MONTAGEM DA URL DE FILTRAGEM
    public function montagemURL(&$dataPost)
    {
        $returnURL = self::BASE_URL_FILTROVEICULO;

        // checa o tipo do veículo
        if(empty($dataPost['veiculo'])){
            return ['success' => false, 'error' => 'Campo veiculo é obrigatório.'];
        }elseif(!in_array(intval($dataPost['veiculo']), [1, 2, 3])){
            return ['success' => false, 'error' => 'Campo veiculo é inválido.'];
        }
        else{

            /* TIPOS DE VEICULO
             1 - Automovel
             2 - Caminhões
             3 - Motos
            */
            $returnURL .= "/veiculo/".$dataPost['veiculo'];
        }

        // checagem do estado de conservacao
        if(!empty($dataPost['estado']))
        {
            if(!in_array( intval($dataPost['estado']), [1, 2])){
                return ['success' => false, 'error' => 'Campo estado é inválido.'];
            }else{
                /* TIPOS DE VEICULO
                    1 - 0km
                    0 - Seminovo
                */
            $returnURL .= "/estado-conservacao/".$dataPost['estado'];
            }
        }

        // checa marca do veículo
        if(empty($dataPost['marca'])){
            return ['success' => false, 'error' => 'Campo marca é obrigatório.'];
        }elseif(!preg_match('/^[0-9]+$/', $dataPost['marca'])){
            return ['success' => false, 'error' => 'Campo marca é invalido.'];
        }
        else{
            $returnURL .= "/marca/".$dataPost['marca'];
        }

        // checa modelo do veículo
        if(empty($dataPost['modelo'])){
            return ['success' => false, 'error' => 'Campo modelo é obrigatório.'];
        }elseif(!preg_match('/^[0-9]+$/', $dataPost['modelo'])){
            return ['success' => false, 'error' => 'Campo modelo é invalido.'];
        }
        else{
            $returnURL .= "/modelo/".$dataPost['modelo'];
        }

        // checa cidade do veículo
        if(!empty($dataPost['cidade'])){
            if(!preg_match('/^[0-9]+$/', $dataPost['cidade'])){
                return ['success' => false, 'error' => 'Campo cidade é invalido.'];
            }
            else{
                $returnURL .= "/cidade/".$dataPost['cidade'];
            }     
        }        
        
        // checa valorInicial do veículo
        if(!empty($dataPost['valorI'])){
            if(!preg_match('/^[0-9]+$/', $dataPost['valorI'])){
                return ['success' => false, 'error' => 'Campo valorI é invalido.'];
            }
            else{
                $returnURL .= "/valor1/".$dataPost['valorI'];
            }     
        }  
        
        // checa valorFinal do veículo
        if(!empty($dataPost['valorF'])){
            if(!preg_match('/^[0-9]+$/', $dataPost['valorF'])){
                return ['success' => false, 'error' => 'Campo valorI é invalido.'];
            }elseif(!empty($dataPost['valorI']) && ($dataPost['valorI'] > $dataPost['valorF']) ){
                return ['success' => false, 'error' => 'O valor inicial não pode ser maior que o final.'];
            }
            else{
                $returnURL .= "/valor2/".$dataPost['valorF'];
            }     
        }  
        
        // checa anoInicial do veículo
        if(!empty($dataPost['anoI'])){
            if(!preg_match('/^[0-9]{4}$/', $dataPost['anoI'])){
                return ['success' => false, 'error' => 'Campo anoI é invalido.'];
            }
            else{
                $returnURL .= "/ano1/".$dataPost['anoI'];
            }     
        }  
        
        // checa anoFinal do veículo
        if(!empty($dataPost['anoF'])){
            if(!preg_match('/^[0-9]{4}$/', $dataPost['anoF'])){
                return ['success' => false, 'error' => 'Campo anoF é invalido.'];
            }elseif(!empty($dataPost['anoI']) && ($dataPost['anoI'] > $dataPost['anoF']) ){
                return ['success' => false, 'error' => 'O ano inicial não pode ser maior que o ano final.'];
            }
            else{
                $returnURL .= "/ano2/".$dataPost['anoF'];
            }     
        }
        
        // checa modalidade de vendedor
        if(empty($dataPost['modalidade'])){
            return ['success' => false, 'error' => 'Campo modalidade é obrigatório.'];
        }elseif(!preg_match('/^[A-Za-z]+$/', $dataPost['modalidade'])){
            return ['success' => false, 'error' => 'Campo modalidade é invalido.'];
        }elseif(!in_array($dataPost['modalidade'], ['particular', 'todos', 'revenda'])){
            return ['success' => false, 'error' => 'Campo modalidade deve ser \'particular\', \'revenda\' ou \'todos\'.'];
        }
        else{
            $returnURL .= "/usuario/".$dataPost['modalidade'];
        }
        
        // checa paginacao
        if(!empty($dataPost['pagina'])){

            if(!preg_match('/^[0-9]+$/', $dataPost['pagina'])){
                return ['success' => false, 'error' => 'Número de página é invalido.'];
            }
            else{
                $returnURL .= "/pagina/".$dataPost['pagina'];
            } 
        }  
        
        return $returnURL;

    }

}