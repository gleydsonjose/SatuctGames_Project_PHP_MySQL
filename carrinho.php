<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Zerando a sessão 'total_carrinho' sempre que a página for atualizada, para evitar de ir acrescentando valor sempre que atualizar ou voltar para essa página.
    $_SESSION['total_carrinho'] = 0;

    // Se a sessão array 'subtotal_game' não existir, ela será criada.
    if(!isset($_SESSION['subtotal_game'])){
        $_SESSION['subtotal_game'] = [];
    }

    // Se o botão remover-game for clicado, tudo abaixo vai entrar em ação
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remover-game"])){
        //  Verificando se o id retornado é um número
        if(!preg_match("/[^0-9]/", $_POST['id-game-carrinho'])){
            // A sessão 'games_no_carrinho' vai diminuir seu valor em menos 1;
            $_SESSION['games_no_carrinho']--;

            // Se subtotal_game for maior que o total_carrinho, a sessão total_carrinho vai receber a subtração entre subtotal_game e ele mesmo.
            if($_SESSION['subtotal_game'][$_POST['id-game-carrinho']] > $_SESSION['total_carrinho']){
                $_SESSION['total_carrinho'] = $_SESSION['subtotal_game'][$_POST['id-game-carrinho']] - $_SESSION['total_carrinho'];
            }else{
                // Se não, a sessão total_carrinho vai receber a subtração entre ele mesmo e o subtotal_game.
                $_SESSION['total_carrinho'] = $_SESSION['total_carrinho'] - $_SESSION['subtotal_game'][$_POST['id-game-carrinho']];
            }

            // Se a sessão 'total_carrinho_desconto' existir.
            if(isset($_SESSION['total_carrinho_desconto'])){
                // A variável 'subtotal_desconto' vai receber o subtotal_game - (subtotal_game * desconto do cupom / 100).
                $subtotal_desconto = $_SESSION['subtotal_game'][$_POST['id-game-carrinho']] - ($_SESSION['subtotal_game'][$_POST['id-game-carrinho']] * $_SESSION['desconto_cupom'] / 100);

                // Se a variável 'subtotal_desconto' for maior que a sessão 'total_carrinho_desconto', o 'total_carrinho_desconto' vai receber a subtração entre o 'subtotal_desconto' e o 'total_carrinho_desconto'.
                if($subtotal_desconto > $_SESSION['total_carrinho_desconto']){
                    $_SESSION['total_carrinho_desconto'] = $subtotal_desconto - $_SESSION['total_carrinho_desconto'];
                }else{
                    // Se não, o 'total_carrinho_desconto' vai receber a subtração entre ele mesmo e o 'subtotal_desconto'.
                    $_SESSION['total_carrinho_desconto'] = $_SESSION['total_carrinho_desconto'] - $subtotal_desconto;
                }
            }

            // Todas as sessões abaixo terão uma key removida, a key é pela numeração do id-game-carrinho.
            unset($_SESSION['idgame_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['nome_game_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['quantidade_escolhida_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['quantidade_total_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['versao_game_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['imagem_game_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['preco_atual_carrinho'][$_POST['id-game-carrinho']]);
            unset($_SESSION['quantidade_game'][$_POST['id-game-carrinho']]);
            unset($_SESSION['subtotal_game'][$_POST['id-game-carrinho']]);

            // Se a sessão 'idgame_carrinho' existir e a quantidade de elementos que ele tem for menor que 1, todas as sessões abaixo serão removidas.
            if(isset($_SESSION['idgame_carrinho']) && count($_SESSION['idgame_carrinho']) < 1){
                unset($_SESSION['games_no_carrinho']);
                unset($_SESSION['idgame_carrinho']);
                unset($_SESSION['nome_game_carrinho']);
                unset($_SESSION['quantidade_escolhida_carrinho']);
                unset($_SESSION['quantidade_total_carrinho']);
                unset($_SESSION['versao_game_carrinho']);
                unset($_SESSION['imagem_game_carrinho']);
                unset($_SESSION['preco_atual_carrinho']);
                unset($_SESSION['quantidade_game']);
                unset($_SESSION['subtotal_game']);
                unset($_SESSION['total_carrinho']);

                // Se a sessão 'total_carrinho_desconto' existir, as sessões abaixo serão removidas.
                if(isset($_SESSION['total_carrinho_desconto'])){
                    unset($_SESSION['cupom']);
                    unset($_SESSION['desconto_cupom']);
                    unset($_SESSION['total_carrinho_desconto']);
                }
            }

            // Atualizando a página após todo código acima.
            echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=carrinho.php'>";
        }
    }

    // Se o botão remover-todos-games for clicado, tudo abaixo vai entrar em ação
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remover-todos-games"])){
        // Verificando se a sessão 'idgame_carrinho' existe e se quantidade de elementos no array for maior que 0.
        if(isset($_SESSION['idgame_carrinho']) && !empty($_SESSION['idgame_carrinho'])){
            // Todas sessões abaixo serão removidas.
            unset($_SESSION['games_no_carrinho']);
            unset($_SESSION['idgame_carrinho']);
            unset($_SESSION['nome_game_carrinho']);
            unset($_SESSION['quantidade_escolhida_carrinho']);
            unset($_SESSION['quantidade_total_carrinho']);
            unset($_SESSION['versao_game_carrinho']);
            unset($_SESSION['imagem_game_carrinho']);
            unset($_SESSION['preco_atual_carrinho']);
            unset($_SESSION['quantidade_game']);
            unset($_SESSION['subtotal_game']);
            unset($_SESSION['total_carrinho']);
        
            // Se a sessão 'total_carrinho_desconto' existir, as sessões abaixo serão removidas.
            if(isset($_SESSION['total_carrinho_desconto'])){
                unset($_SESSION['cupom']);
                unset($_SESSION['desconto_cupom']);
                unset($_SESSION['total_carrinho_desconto']);
            }

            // Atualizando a página após todo código acima.
            echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=carrinho.php'>";
        }else{
            // Se não, o usuário vai receber uma mensagem de erro.
            $mensagem_erro_finalizar_compra = 'Seu carrinho está vazio';
        }
    }

    // Se o botão finalizar-compra for clicado, tudo abaixo vai entrar em ação
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["finalizar-compra"])){

        // Se o usuário não estiver logado, será mostrado uma mensagem de erro para ele.
        if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador']) && !isset($_SESSION['id_usuario'])){
            $mensagem_erro_finalizar_compra = 'Você precisa está logado para finalizar a compra';
        }else{
            // Se não, será criada uma sessão para confirmar a compra.
            // Logo após a isso o usuário será redirecionado para a página 'finalizar-compra'.
            $_SESSION['compra'] = true;
            echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=finalizar-compra.php'>";   
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Essa loja é apenas um projeto experimental">
    <meta name="author" content="Gleydson José">
    <meta name="keywords" content="SatuctGames Satuct Games">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="imagens/satuctgames-favicon.png?<?= time() ?>">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.2/css/all.css' integrity='sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay' crossorigin='anonymous'>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css?<?= time() ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- // BOOTSTRAP CSS -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <title>Carrinho - SatuctGames</title>
</head>
<body>
    <!-- HEADER -->
    <header class="container-fluid bg-cinza">
        <div class="row">
            <div class="col-6">
                <a href="index.php">
                    <img src="imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo mx-2 my-4">
                </a>
            </div>
            <div class="col-6">
                <div class="text-right mt-2 mr-3">
                    <a href="https://www.facebook.com/" target="_blank" class="redes-sociais pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/" target="_blank" class="redes-sociais pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/?gl=BR&hl=pt" target="_blank" class="redes-sociais pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/" target="_blank" class="redes-sociais" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 px-0">
                <nav class="navbar navbar-expand-lg navbar-dark bg-preto menu-loja py-0">
                    <button class="navbar-toggler my-2" style="border-color: #dadada;" type="button" data-toggle="collapse" data-target="#menu-navegacao" aria-controls="menu-navegacao" aria-expanded="false" aria-label="menu navegacao">
                        <i class="fas fa-bars py-1 text-cinza"></i>
                    </button>

                    <div class="collapse navbar-collapse w-100" id="menu-navegacao">
                        <ul class="navbar-nav mt-lg-0 mt-2">
                            <li class="nav-item">
                                <a href="index.php" class="nav-link btn-menu-cinza">Inicio</a>
                            </li>

                            <li class="nav-item">
                                <a href="promocoes.php" class="nav-link btn-menu-cinza">Promoções</a>
                            </li>

                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle btn-menu-cinza" id="categorias-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">Categorias</a>
                                <div class="dropdown-menu" aria-labelledby="categorias-dropdown">
                                    <a href="acao.php" class="dropdown-item">Ação</a>
                                    <a href="aventura.php" class="dropdown-item">Aventura</a>
                                    <a href="corrida.php" class="dropdown-item">Corrida</a>
                                    <a href="luta.php" class="dropdown-item">Luta</a>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="lancamentos.php" class="nav-link btn-menu-cinza">Lançamentos</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto">
                            <form class="form-inline my-lg-0 my-2" autocomplete="off">
                                <div class='form-busca d-none'>
                                    <input class="form-control form-control-sm" id="buscar" type="text" placeholder="Escreva o nome do game" aria-label="Buscar" style="border: 1px solid #e4e2e2 !important;">
                                    <button class="btn btn-buscar-form" id="fechar-busca" type="button"><i class="fas fa-times btn-busca-icon"></i></button>

                                    <div class="busca-items text-break bg-preto shadow-sm" id="busca-items"></div>
                                </div>

                                <button class="btn btn-menu-cinza btn-abrir-busca" type="button"><i class="fas fa-search btn-busca-icon"></i></button>
                            </form>

                            <li class="nav-item">
                                <a href="carrinho.php" class="nav-link btn-menu-cinza"><i class="fas fa-shopping-cart carrinho"><span class="ml-1"><?php
                                // Se houver games no carrinho, será mostrado a quantidade.
                                if(isset($_SESSION['games_no_carrinho'])){
                                    echo $_SESSION['games_no_carrinho'];
                                }else{
                                    // Se não, o valor será 0.
                                    echo '0';
                                }
                                ?></span></i></a>
                            </li>

                            <?php // Verificando se não tem ninguém logado
                            if(!isset($info)){ ?>
                            <li class="nav-item nav-usuario">
                                <a class="nav-link mt-lg-0 btn-menu-cinza" href="login.php"><i class="fas fa-user mr-1"></i>Entrar</a>
                            </li>
                      <?php // Se tem alguém logado, será mostrado um menu próprio para esse usuário.
                            }else{ ?>
                            <li class="nav-item dropdown nav-usuario">
                                <a class="nav-link dropdown-toggle mt-lg-0 btn-menu-cinza" id="menu-usuario" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button" style="cursor: pointer;"><span class="text-cinza"><?= $info['nome'] ?></span></a>
                                <div class="dropdown-menu dropdown-menu-right mb-lg-2" aria-labelledby="menu-usuario">
                                    <a href="detalhesdaconta.php" class="dropdown-item">Detalhes da conta</a>
                                <?php // Se a sessão atual for um administrador ou moderador, essa opção do menu será mostrada
                                    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador'])){ ?>
                                        <a href="paineldecontrole.php" class="dropdown-item">Painel de controle</a>
                              <?php } // ---------------------------------------- ?>
                                    <a href="sair.php" class="dropdown-item">Sair</a>
                                </div>
                            </li>
                      <?php } ?>  
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- // HEADER -->

    <!-- CARRINHO -->
    <div class="container-fluid mt-5 py-5">
        <div class="row">
            <div class="col-12">
                <div class="row mb-5 pt-5 pb-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                    <div class="col-12">
                        <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">CARRINHO</span>
                    </div>
                </div>
            </div>

            <div class="col-12-lista-carrinho mx-auto">
                <!-- TABELA HEAD -->
                <div class="row pt-2 bg-cinza-2 text-light text-center d-flex align-items-center">
                    <div class="col-4 col-md-5 px-0">
                        <h6>Games</h6>
                    </div>

                    <div class="col-2 px-0">
                        <h6>Valor unitário</h6>
                    </div>

                    <div class="col-2 col-md-1 px-0">
                        <h6>Qtde</h6>
                    </div>

                    <div class="col-2 px-0">
                        <h6>Subtotal</h6>
                    </div>

                    <div class="col-2 px-0">
                        <h6>Ações</h6>
                    </div>
                </div>
                <!-- // TABELA HEAD -->

                <!-- TABELA BODY -->
                <?php
                    // Se existir a sessão 'idgame_carrinho'.
                    if(isset($_SESSION['idgame_carrinho'])){
                        // Será mostrado todos os adicionado ao carrinho.
                        foreach($_SESSION['idgame_carrinho'] as $idgame_carrinho){ ?>
                            <div class="row bg-branco border-left-cinza border-bottom-cinza border-right-cinza">
                                <div class="col-4 col-md-5 px-0 border-right-cinza">
                                    <div class="d-flex carrinho-game">
                                        <img src="<?= $_SESSION['imagem_game_carrinho'][$idgame_carrinho] ?>" alt="" class="img-fluid" style="width: 70px; height: 70px; filter: grayscale(20%);">
                                        <h6 class="align-self-center text-break ml-2 mt-2 text-cinza-2" style="font-size: 11pt"><?= $_SESSION['nome_game_carrinho'][$idgame_carrinho]." - ".$_SESSION['versao_game_carrinho'][$idgame_carrinho] ?></h6>
                                    </div>
                                </div>

                                <div class="col-2 px-0 d-flex align-items-center justify-content-center border-right-cinza">
                                    <h6 class="mt-2 text-break text-cinza-2" style="font-size: 11pt;">R$<?= $_SESSION['preco_atual_carrinho'][$idgame_carrinho] ?></h6>
                                </div>

                                <div class="col-2 col-md-1 px-0 d-flex align-items-center justify-content-center border-right-cinza">
                                    <input type="number" class="form-control form-control-sm form-control-carrinho my-2" name="quantidade" id="quantidade<?= $idgame_carrinho ?>" min="1" max="<?= $_SESSION['quantidade_total_carrinho'][$idgame_carrinho] ?>" value="<?php
                                    // Se a sessão 'quantidade_game' com o id do game existir, será mostrado essa quantidade.
                                    if(isset($_SESSION['quantidade_game'][$idgame_carrinho])){
                                        echo $_SESSION['quantidade_game'][$idgame_carrinho];
                                    }else{
                                        // Se não, a 'quantidade_escolhida_carrinho' com o id do game será mostrado.
                                        echo $_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho];
                                    }

                                    // 'quantidade_game' é uma sessão que só é criada quando o usuário tentar escolher a quantidade do game no carrinho, a outra é criada já na página do game quando o usuário escolhe a quantidade por lá.
                                    ?>" style="width: auto;">
                                </div>

                                <div class="col-2 px-0 d-flex align-items-center justify-content-center border-right-cinza">
                                    <div class="d-flex">
                                        <h6 class="mt-2 text-break text-cinza-2" name="preco-subtotal" id="valor_unitario<?= $idgame_carrinho ?>" style="font-size: 11pt;">R$<?php
                                        // Se a sessão 'quantidade_game' com o id do game existir.
                                        if(isset($_SESSION['quantidade_game'][$idgame_carrinho])){
                                            // Será mostrado o resultado da multiplicação dessa quantidade_game * preco_atual_carrinho.
                                            echo number_format(str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$idgame_carrinho]) * $_SESSION['quantidade_game'][$idgame_carrinho], 2);
                                        }else{
                                            // Se não, será mostrado o resultado da multiplicação entre quantidade_escolhida_carrinho e preco_atual_carrinho.
                                            echo number_format(str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$idgame_carrinho]) * $_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho], 2);
                                        }
                                        ?></h6>
                                    </div>
                                </div>

                                <form method="POST" class="col-2 px-0 d-flex flex-column justify-content-center align-items-center">
                                    <div class="form-check d-none">
                                        <input class="form-check-input" type="radio" name="id-game-carrinho" id="id-game-carrinho<?= $idgame_carrinho ?>" value="<?= $_SESSION['idgame_carrinho'][$idgame_carrinho] ?>" checked>
                                    </div>

                                    <button type="submit" class="btn btn-danger btn-sm" name="remover-game" style="padding: 5px 10px !important;"><i class="fas fa-trash"></i></button>
                                </form>

                                <?php
                                    // Se a sessão 'quantidade_game' com o id do game existir.
                                    if(isset($_SESSION['quantidade_game'][$idgame_carrinho])){
                                        // Sessão subtotal_game recebe o resultado entre preco_atual_carrinho * quantidade_game.
                                        $_SESSION['subtotal_game'][$idgame_carrinho] = str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$idgame_carrinho]) * $_SESSION['quantidade_game'][$idgame_carrinho];

                                        // Sessão total_carrinho recebe ele mesmo + o resultado entre preco_atual_carrinho * quantidade_game.
                                        $_SESSION['total_carrinho'] = $_SESSION['total_carrinho'] + str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$idgame_carrinho]) * $_SESSION['quantidade_game'][$idgame_carrinho];
                                    }else{
                                        // Se não, sessão subtotal_game recebe o resultado entre preco_atual_carrinho * quantidade_escolhida_carrinho.
                                        $_SESSION['subtotal_game'][$idgame_carrinho] = str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$idgame_carrinho]) * $_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho];

                                        // Sessão total_carrinho recebe ele mesmo + o resultado entre preco_atual_carrinho * quantidade_escolhida_carrinho.
                                        $_SESSION['total_carrinho'] = $_SESSION['total_carrinho'] + str_replace(',', '.', $_SESSION['preco_atual_carrinho'][$idgame_carrinho]) * $_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho];

                                        // 'quantidade_game' é uma sessão que só é criada quando o usuário tentar escolher a quantidade do game no carrinho, a outra é criada já na página do game quando o usuário escolhe a quantidade por lá.
                                    }
                                ?>
                            </div>
                            <script>
                                $(function(){
                                    // Se a quantidade for alterada, a função abaixo será executada.
                                    $("#quantidade<?= $idgame_carrinho ?>").change(function(){
                                        // Recebendo valores e passando para variáveis.
                                        var quantidade = $("#quantidade<?= $idgame_carrinho ?>").val();
                                        var id_game = $("#id-game-carrinho<?= $idgame_carrinho ?>").val();

                                        // Enviando os valores recebidos para a url escolhida.
                                        // Depois será retornado um array com dados, em formato JSON para ser decodificado.
                                        // dados[0] = subtotal recebido pelo script 'calculo_carrinho'.
                                        // dados[1] = total do carrinho recebido pelo script 'calculo_carrinho'.
                                        $.ajax({
                                            url: "scripts/calculo_carrinho.php",
                                            type: "post",
                                            data: {
                                                quantidade:quantidade,
                                                id_game:id_game
                                            },
                                            success: function(resposta){
                                                var dados = jQuery.parseJSON(resposta);
                                                $('#valor_unitario<?= $idgame_carrinho ?>').text(dados[0]);
                                                $('#total_carrinho').text(dados[1]);
                                            }
                                        })
                                    })
                                })
                            </script>
                  <?php }
                    }
                    ?>
                <!-- // TABELA BODY -->

                <!-- TABELA FOOTER -->
                <div class="row bg-branco border-left-cinza border-bottom-cinza border-right-cinza">
                    <div class="col-8 px-0 d-flex justify-content-end border-right-cinza">
                        <h6 class="mt-2 mr-2 text-cinza-2" id="total-nome"><?php
                        // Se a sessão 'total_carrinho_desconto' existir, a palavra 'Total' será alterada para uma frase.
                        if(isset($_SESSION['total_carrinho_desconto'])){
                            echo "Total com <span class='text-success'>$_SESSION[desconto_cupom]% desconto</span>";
                        }else{
                            // Se não, ela vai voltar a ser 'Total'.
                            echo "Total";
                        }
                        ?></h6>
                    </div>

                    <div class="col-2 px-0 d-flex align-items-center justify-content-center border-right-cinza">
                        <h6 class="mt-2 text-break text-cinza-2" id="total_carrinho" style="font-size: 11pt;">R$<?php
                        // Se a sessão 'total_carrinho_desconto' existir, será mostrado o total carrinho com desconto.
                        if(isset($_SESSION['total_carrinho_desconto'])){
                            echo number_format($_SESSION['total_carrinho_desconto'], 2);
                        }else{
                            // Se não, será verificado antes se a sessão 'total_carrinho' existe, logo após será mostrado o total do carrinho.
                            if(isset($_SESSION['total_carrinho'])){
                                echo str_replace('.', ',', number_format($_SESSION['total_carrinho'], 2));
                            }
                        }
                        ?></h6>
                    </div>

                    <form method="POST" class="col-2 px-0 d-flex flex-column justify-content-center align-items-center">
                        <button type="submit" class="btn btn-danger btn-sm" name="remover-todos-games" style="padding: 5px 10px !important;"><i class="fas fa-trash pr-2"></i>Todos</button>
                    </form>
                </div>
                <!-- TABELA FOOTER -->

                <div class="row mt-5">
                    <div class="col-6 col-md-4 px-0">
                        <h6>Cupom de desconto</h6>

                        <form autocomplete="off" class="d-flex">
                            <div class="form-group w-75">
                                <input type="text" class="form-control form-control-sm form-control-alt-2" placeholder="Digite seu cupom de desconto" id="cupom" value="<?php
                                // Se a sessão cupom existir, será mostrado o cupom.
                                if(isset($_SESSION['cupom'])){
                                    echo $_SESSION['cupom'];
                                }
                                ?>">
                                <small class="form-text text-danger mt-2 d-none" id="aviso-erro-cupom">Cupom inválido</small>
                            </div>
                            <div class="form-group w-25">
                                <button type="button" class="btn btn-cinza-form" id="atualizar-cupom" style="padding: 2.8px 8px !important;"><i class="fas fa-redo-alt"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="col-6 col-md-8 d-flex align-items-end flex-column" style="margin-top: 19px;">
                        <form method="POST" class="d-flex align-items-center">
                            <button type="submit" name="finalizar-compra" class="btn btn-cinza-form btn-sm py-2 px-2"><i class="fas fa-wallet pl-1 pr-2"></i>Finalizar compra</button>
                        </form>
                        <?php
                            // Se existir alguma mensagem de erro ao finalizar a compra, será mostrado aqui.
                            if(isset($mensagem_erro_finalizar_compra)){ ?>
                                <small class="form-text text-danger mt-2"><?= $mensagem_erro_finalizar_compra ?></small>
                      <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- // CARRINHO -->

    <!-- FOOTER -->
    <div class="container-fluid bg-preto mt-5">
        <div class="row">
            <div class="col-6 mt-3">
                <h6 class="text-branco mt-0 mb-1 ml-1" style="font-size: 13pt;">Fale conosco</h6>
                <p class="text-branco my-0 ml-1" style="font-size: 12pt;">Celular: (00) 00000-0000</p>
                <p class="text-branco my-0 ml-1" style="font-size: 12pt;">Email: satuctgames@gmail.com</p>
                <a href="faleconosco.php" class="btn-ver-detalhes my-0 ml-1" style="text-decoration: none; font-size: 11pt;">Envie mensagens por email para nós <i class="fas fa-arrow-left"></i></a>
            </div>

            <div class="col-6 text-right mt-3">
                <h6 class="text-branco mr-1" style="font-size: 13pt;">Redes sociais</h6>
                <div class="mr-1">
                    <a href="https://www.facebook.com/" target="_blank" class="redes-sociais-footer pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/" target="_blank" class="redes-sociais-footer pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/?gl=BR&hl=pt" target="_blank" class="redes-sociais-footer pr-2" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.instagram.com/" target="_blank" class="redes-sociais-footer" style="text-decoration: none; font-size: 14pt;">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-6 col-md-8 col-12">
                <h6 class="text-branco ml-1 mb-3" style="font-size: 13pt;">Formas de pagamento</h6>
                <img src="imagens/boleto-footer.png" alt="" class="img-fluid ml-1">
                <img src="imagens/pag-seguro-footer.png" alt="" class="img-fluid bg-branco rounded ml-2">
                <img src="imagens/paypal-footer.png" alt="" class="img-fluid bg-branco rounded ml-2">
            </div>

            <div class="col-lg-6 col-md-4 col-12 text-md-right mt-md-0 ml-md-0 mt-5 ml-1">
                <h6 class="text-branco mr-1 mb-3" style="font-size: 13pt;">Forma de entrega</h6>
                <img src="imagens/correios-footer.png" alt="" class="img-fluid bg-branco rounded mr-1">
            </div>
        </div>

        <div class="row">
            <h6 class="text-branco mx-auto mb-3 mt-5" style="font-size: 11pt;">SatuctGames Project 2019 - Todos os direitos reservados</h6>
        </div>
    </div>
    <!-- // FOOTER -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>