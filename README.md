# SeminovosBH RESTFul API

API RESTFul para consulta interativa e dinâmica de veículos comercializados pelo SeminovosBH.

***

## URL Base

Todos os métodos disponibilizados nessa API deverão ser preceditos pela URL Base. 
A URL Base a ser utilizada é **/api/veiculos**.

Exemplo: **/api/veiculos**/visualizar/:id

## Changes
* 2019-01-25 Publicação da versão 1.0 da API.

# Endpoints

## 1. Tipos de Veículo

    GET /busca/opcoesveiculo

### Descrição
``Retorna todas as possibilidades de tipos de veículos para realizar as buscas.``

### Parâmetros
``Não posssui parametros``

## 2. Marcas de Veículos

    GET /busca/marcas/:tipoveiculo

### Descrição
``Retorna todas as possibilidades de marcas associadas a um tipo de veículo.``

### Parâmetros
- **tipoveiculo : integer** — Código identificador do tipo do veículo. O parâmetro pode ser obtido através do método ``GET /busca/opcoesveiculo`` . Campo obrigatório.


## 3. Modelos de Veículos

    GET /busca/modelos/:idmarca

### Descrição
``Retorna todas os modelos de veículos associados a uma marca.``

### Parâmetros
- **idmarca : integer** — Código identificador da marca veículo. O parâmetro pode ser obtido através do método ``GET /busca/marcas/:tipoveiculo`` . Campo obrigatório.


## 4. Busca de cidades por tipos de veículo

    GET /busca/cidades/veiculo/:tipoveiculo

### Descrição
``Retorna todas as cidades em que ha´ disponibilidade de um tipo de veículo``

### Parâmetros
- **tipoveiculo : integer** — Código identificador do tipo de veículo. O parâmetro pode ser obtido através do método ``GET /buscas/opcoesveiculo`` . Campo obrigatório.



## 5. Busca de cidades por marca de veículo

    GET /busca/cidades/veiculo/:tipoveiculo/marca/:idmarca

### Descrição
``Retorna todas as cidades em que ha´ disponibilidade de uma marca de veículo``

### Parâmetros
- **tipoveiculo : integer** — Código identificador do tipo de veículo. O parâmetro pode ser obtido através do método ``GET /buscas/opcoesveiculo`` . Campo obrigatório.
- **idmarca : integer** — Código identificador da marca do veículo. O parâmetro pode ser obtido através do método ``/busca/marcas/:tipoveiculo`` . Campo obrigatório.



## 6. Busca de cidades por modelo de veículo

    GET /busca/cidades/veiculo/:tipoveiculo/marca/:idmarca/modelo/:idmodelo

### Descrição
``Retorna todas as cidades em que ha´ disponibilidade de um modelo de veículo``

### Parâmetros
- **tipoveiculo : integer** — Código identificador do tipo de veículo. O parâmetro pode ser obtido através do método ``GET /buscas/opcoesveiculo`` . Campo obrigatório.
- **idmarca : integer** — Código identificador da marca do veículo. O parâmetro pode ser obtido através do método ``/busca/marcas/:tipoveiculo`` . Campo obrigatório.
- **idmodelo : integer** — Código identificador do modelo do veículo. O parâmetro pode ser obtido através do método ``GET /busca/modelos/:idmarca`` . Campo obrigatório.


## 7. Filtragem de Veículos

    POST /busca/filtragem

### Descrição
``Retorna uma listagem de veículos de acordo com os parâmetros de filtragem de resultados.``

### Parâmetros
- **veiculo : integer** — Código identificador do tipo de veículo. O parâmetro pode ser obtido através do método ``GET /buscas/opcoesveiculo`` . Campo obrigatório.
- **estado : integer** —  Informe *1* para carros novos ou *0*, para seminovos.
- **marca : integer** — Código identificador da marca do veículo. O parâmetro pode ser obtido através do método ``GET /busca/marcas/:tipoveiculo`` . Campo obrigatório.
- **modelo : integer** — Código identificador do modelo do veículo. O parâmetro pode ser obtido através do método ``GET /busca/modelos/:idmarca`` . Campo obrigatório.
- **cidade : integer** — Código identificador da cidade de busca desejada. O parâmetro pode ser obtido através dos endpoits ``GET /busca/cidades/{metodos}`` .
- **valorI : integer** — Preço mínimo do veículo.
- **valorF : integer** — Preço máximo do veículo.
- **anoI : integer** — Ano mínimo de fabricação do veículo.
- **anoF : integer** — Ano máximo de fabricação do veículo.
- **modalidade : string** —  Informe *particular* para busca de veículos particulares, *revendas* para busca de veículos de revendedores/concessionárias ou *todos* para abranger as duas opções anteriores. Campo obrigatório.
- **pagina: integer** — Permite a navegação entre as páginas de resultados.

