<?php
    // Se não houver uma sessão iniciada, será iniciada uma nova
    if(!isset($_SESSION)){
        session_start();
    }

    // Aqui fica o cep de origem, e o de destino que será recebido pelo ajax.
    $cep_origem = "85930000";
    $cep_destino = $_POST['cep'];

    // Se o metodo de entrega for o sedex o código dele será armazenado numa variável, se for pac o seu código será armazenado na variável.
    // Códigos: Sedex: 40010   |  Pac: 41106
    if($_POST['metodo_entrega'] == 'metodo-entrega=Sedex'){
        $metodo_entrega = '40010';
    }else if($_POST['metodo_entrega'] == 'metodo-entrega=PAC'){
        $metodo_entrega = '41106';
    }

    // Se a sessão 'total_compra' existir, ela será armazenada numa variável, se não, o valor '0' será armazenada na variável.
    if(isset($_SESSION['total_compra'])){
        $total_carrinho = $_SESSION['total_compra'];
    }else{
        $total_carrinho = 0;
    }

    // Dados do produto a ser enviado
    $peso          = 1;
    $valor         = $total_carrinho;
    $tipo_do_frete = $metodo_entrega;
    $altura        = 6;
    $largura       = 20;
    $comprimento   = 20;

    // URL dos correios com dados recebidos.
    $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?";
    $url .= "nCdEmpresa=";
    $url .= "&sDsSenha=";
    $url .= "&sCepOrigem=" . $cep_origem;
    $url .= "&sCepDestino=" . $cep_destino;
    $url .= "&nVlPeso=" . $peso;
    $url .= "&nVlLargura=" . $largura;
    $url .= "&nVlAltura=" . $altura;
    $url .= "&nCdFormato=1";
    $url .= "&nVlComprimento=" . $comprimento;
    $url .= "&sCdMaoProria=N";
    $url .= "&nVlValorDeclarado=" . $valor;
    $url .= "&sCdAvisoRecebimento=N";
    $url .= "&nCdServico=" . $tipo_do_frete;
    $url .= "&nVlDiametro=0";
    $url .= "&StrRetorno=xml";

    // Carregando a url de formato xml numa variável.
    $xml = simplexml_load_file($url);

    // Transformando a variável anterior que está em objeto, para um array e assim armazenando ela numa variável.
    // Essa mudança é para facilitar o funcionamento dele no resto do código.
    $dados = (array)$xml->cServico;

    // Colocando o frete com um 'R$' numa variável.
    $preco_frete = "R$".$dados['Valor'];

    // Se o prazo de entrega for 1 ou menor, o prazo e uma frase será armazenada numa variável, se não for o caso, o mesmo vai acontecer só que com outra frase.
    if($dados['PrazoEntrega'] <= 1){
        $prazo = $dados['PrazoEntrega']." dia útil";
    }else{
        $prazo = $dados['PrazoEntrega']." dias úteis";
    }

    // Colocando o frete e o total do carrinho como array para uma variável.
    // Pegando essa variável e substituíndo ',' por '.', somando todos os valores no array, mudando formato para 2 casas decimais e adicionando um 'R$', tudo isso vai parar uma variável.
    // O frete, prazo de entrega, e o total da compra(que teve o '.', substituido por ',') será colocado como array numa variável.
    // Assim enviando essa variável 'CalcPrecoPrazo' como retorno em forma de JSON para o ajax.
    $valores = [$dados['Valor'], $total_carrinho];
    $total_compra = "R$".number_format(array_sum(str_replace(',', '.', $valores)), 2);
    $CalcPrecoPrazo = [$preco_frete, $prazo, str_replace('.', ',', $total_compra)];
    echo json_encode($CalcPrecoPrazo);

    // Pegando a variável 'valores' e substituíndo ',' por '.', somando todos os valores no array, mudando o formato para 2 casas decimais, substituíndo o '.' por ',', e passando o resultado para uma variável.
    // Adicionando o frete numa sessão, e o prazo de entrega numa sessão.
    $_SESSION['total_a_pagar'] = str_replace('.', ',', number_format(array_sum(str_replace(',', '.', $valores)), 2));
    $_SESSION['frete'] = $dados['Valor'];
    $_SESSION['prazo_entrega'] = $dados['PrazoEntrega'];
 ?>