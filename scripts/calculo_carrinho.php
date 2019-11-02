<?php
    // Se não houver uma sessão iniciada, será iniciada uma nova
    if(!isset($_SESSION)){
        session_start();
    }

    // Se não tiver uma sessão array de quantidade de um game no carrinho, ela será criada.
    if(!isset($_SESSION['quantidade_game'])){
        $_SESSION['quantidade_game'] = [];
    }

    // A sessão array 'quantidade_game' vai receber uma key usando o id do game recebido, nessa key vai ter(como valor) a nova quantidade escolhida na página do carrinho.
    $_SESSION['quantidade_game'][$_POST['id_game']] = $_POST['quantidade'];

    // A sessão array 'subtotal_game' vai receber uma key usando o id do game recebido, nessa key vai ter(como valor) o preço atual desse game vezes a quantidade atual.
    // O preço atual vai ter sua ',' substituída por '.'
    $_SESSION['subtotal_game'][$_POST['id_game']] = str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$_POST['id_game']]) * $_SESSION['quantidade_game'][$_POST['id_game']];

    // Essa sessão vai receber a soma de todos os valores da sessão array 'subtotal_game'.
    $_SESSION['total_carrinho'] = array_sum($_SESSION['subtotal_game']);

    // Se a sessão 'total_carrinho_desconto' existir, ela vai receber a soma de todos os valores da sessão array 'subtotal_game' - o valor da sessão array 'total_carrinho' * cupom de desconto / 100.
    // Um array com o subtotal do game e o total do carrinho com desconto será criado.
    if(isset($_SESSION['total_carrinho_desconto'])){
        $_SESSION['total_carrinho_desconto'] = array_sum($_SESSION['subtotal_game']) - ($_SESSION['total_carrinho'] * $_SESSION['desconto_cupom'] / 100);
        $Subtotal_TotalCarrinho = ["R$".number_format($_SESSION['subtotal_game'][$_POST['id_game']], 2), "R$".number_format($_SESSION['total_carrinho_desconto'], 2)];    
    }else{
        // Se não, um array com o subtotal do game e o total do carrinho será criado.
        $Subtotal_TotalCarrinho = ["R$".number_format($_SESSION['subtotal_game'][$_POST['id_game']], 2), "R$".number_format($_SESSION['total_carrinho'], 2)];
    }

    // Dependendo do array que será criado acima, ele será passado para o ajax em forma de JSON.
    echo json_encode($Subtotal_TotalCarrinho);
?>