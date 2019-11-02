<?php
    // Se não houver uma sessão iniciada, será iniciada uma nova
    if(!isset($_SESSION)){
        session_start();
    }

    // Script com métodos para várias coisas.
    require_once 'dados.php';
    $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Criando uma sessão 'cupom' que está recebendo o cupom enviado pelo ajax.
    $_SESSION['cupom'] = $_POST['cupom'];

    // Variável desconto recebendo a porcentagem de desconto apartir do nome do cupom.
    $desconto = $dados->BuscarDescontoCupom($_SESSION['cupom']);

    // Se o cupom de desconto existe, uma sessão com o desconto do cupom será criada, uma com o total do carrinho com o desconto, e um array com um aviso que existe o cupom, a sessão total_carrinho_desconto, a sessão com desconto do cupom e uma modificação na palavra 'Total', sendo passados para o ajax em forma de 'json'.
    if($desconto['desconto']){
        $_SESSION['desconto_cupom'] = $desconto['desconto'];
        $_SESSION['total_carrinho_desconto'] = $_SESSION['total_carrinho'] - ($_SESSION['total_carrinho'] * $_SESSION['desconto_cupom'] / 100);
        $TotalCarrinhoValor_TotalCarrinhoNome = ["desconto", "R$".number_format($_SESSION['total_carrinho_desconto'], 2), "Total com <span class='text-success'>$_SESSION[desconto_cupom]% desconto</span>"];
        echo json_encode($TotalCarrinhoValor_TotalCarrinhoNome);
    }else{
        // Se o cupom de desconto não existe, as sessões abaixo serão removidas se existirem.
        // Um array com um aviso que não existe o cupom, o total do carrinho e uma modificação na palavra 'Total', sendo passados para o ajax em forma de 'json'.
        unset($_SESSION['total_carrinho_desconto']);
        unset($_SESSION['desconto_cupom']);
        unset($_SESSION['cupom']);
        $TotalCarrinhoValor_TotalCarrinhoNome = ["sem-desconto", "R$".number_format($_SESSION['total_carrinho'], 2), "Total"];
        echo json_encode($TotalCarrinhoValor_TotalCarrinhoNome);
    }
?>