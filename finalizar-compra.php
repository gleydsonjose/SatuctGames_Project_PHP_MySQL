<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador, moderador ou usuário, a página será redirecionada para a página inicial
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador']) && !isset($_SESSION['id_usuario'])){
        header('location: index.php');
    }

    // Se o usuário não tiver comprado algo, ele não vai conseguir entrar nessa página
    if(!isset($_SESSION['compra'])){
        header('location: index.php');
    }

    // O array 'precos' vai receber todos os preços de cada game(que está em sessões), porque todos eles começam com a tag 'game-' em sua key(chave), todas as outras sessões não serão atribuídas a este array por causa do strpos.
    // Lembrando que os preços terão seus ',' substituídos por '.'
    $precos = [];
    foreach($_SESSION as $chave=>$valor){
        $busca_tag = strpos($chave, 'game-');
        if($busca_tag === false){
            continue;
        }else{
            $precos[] = str_replace(',', '.', $valor);
        }
    }

    // Se a sessão 'total_carrinho_desconto' existir, a sessão 'total_compra' vai receber o total do carrinho com desconto.
    // Se não for o caso, será verificado se a sessão 'total_carrinho' existe, se sim, a sessão 'total_compra' vai receber o total do carrinho.
    if(isset($_SESSION['total_carrinho_desconto'])){
        $_SESSION['total_compra'] = str_replace('.', ',', number_format($_SESSION['total_carrinho_desconto'], 2));
    }else if(isset($_SESSION['total_carrinho'])){
        $_SESSION['total_compra'] = str_replace('.', ',', number_format($_SESSION['total_carrinho'], 2));
    }else{
        // Se não, o array(precos) criado anteriormente, terá todos os seus valores somados, o formato será modificado para 2 casas decimais, os '.' serão substituídas por ',', e assim tudo isso será armazenado numa sessão.
        $_SESSION['total_compra'] = str_replace('.', ',', number_format(array_sum($precos), 2));
    }

    // Array para armazenar todos os erros e mostrar como mensagem.
    $mensagem_erro = [];

    // Pegando o arquivo json e decodificando ele em array associativo.
    $estados = json_decode(file_get_contents('scripts/estados.json'), true);

    // Se o método requisitado for POST, e o botão submit 'finalizar-compra' for clicado, tudo abaixo será executado.
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar-compra'])){
        // Dados recebidos serão armazenados em sessões e uma variável
        $_SESSION['cep'] = addslashes(strip_tags(trim($_POST['cep'])));
        $_SESSION['cidade'] = addslashes(strip_tags(trim($_POST['cidade'])));
        $_SESSION['endereco'] = addslashes(strip_tags(trim($_POST['endereco'])));
        $_SESSION['numero'] = addslashes(strip_tags(trim($_POST['numero'])));
        $_SESSION['bairro'] = addslashes(strip_tags(trim($_POST['bairro'])));
        $_SESSION['complemento'] = addslashes(strip_tags(trim($_POST['complemento'])));
        $_SESSION['telefone'] = addslashes(strip_tags(trim($_POST['telefone'])));
        $estado_escolhido = addslashes(strip_tags(trim($_POST['estado'])));

        // Verificando se o metodo-pagamento foi enviado, se sim ele será armazenado numa sessão, se não, nada será armazenado na sessão.
        $_SESSION['metodo_pagamento'] = isset($_POST['metodo-pagamento']) ? $_POST['metodo-pagamento'] : "";

        // Verificando se o metodo-entrega foi enviado, se sim ele será armazenado numa sessão, se não, nada será armazenado na sessão.
        $_SESSION['metodo_entrega'] = isset($_POST['metodo-entrega']) ? $_POST['metodo-entrega'] : "";

        // Armazenando o nome do usuário logado para uma sessão.
        $_SESSION['nome_usuario'] = $info['nome'];

        // Verificando se cada campo está vazio
        if(strlen($_SESSION['cep']) == 0){
            $mensagem_erro[] = "Preencha o campo CEP.";
        }

        if(strlen($_SESSION['cidade']) == 0){
            $mensagem_erro[] = "Preencha o campo Cidade.";
        }

        if(strlen($_SESSION['endereco']) == 0){
            $mensagem_erro[] = "Preencha o campo Endereço.";
        }

        if(strlen($_SESSION['numero']) == 0){
            $mensagem_erro[] = "Preencha o campo Número.";
        }

        if(strlen($_SESSION['bairro']) == 0){
            $mensagem_erro[] = "Preencha o campo Bairro.";
        }

        if(strlen($_SESSION['complemento']) == 0){
            $mensagem_erro[] = "Preencha o campo Complemento.";
        }

        if(strlen($_SESSION['telefone']) == 0){
            $mensagem_erro[] = "Preencha o campo Telefone.";
        }

        //  Verificando se o campo não tem números digitados
        if(preg_match("/[^0-9]/", $_SESSION['telefone'])){
            $mensagem_erro[] = "Digite apenas números no campo Telefone";
        }

        // Separando os arrays da variável 'estados' que tem 3 arrays, separando para apenas 1($todos_estados).
        $todos_estados = [];
        foreach($estados as $e){
            for($c = 0; $c < count($e); $c++){
                $todos_estados[] = $e[$c]['nome'];
            }
        }
        
        // Verificando se o usuário não escolheu um estado.
        if(!in_array($estado_escolhido, $todos_estados)){
            $mensagem_erro[] = "Escolha o seu estado.";
        }

        // Verificando se a sessão 'metodo_pagamento' está vazia.
        if(strlen($_SESSION['metodo_pagamento']) == 0){
            $mensagem_erro[] = "Escolha o método de pagamento.";
        }

        // Verificando se a sessão 'metodo_entrega' está vazia.
        if(strlen($_SESSION['metodo_entrega']) == 0){
            $mensagem_erro[] = "Escolha o método de entrega.";
        }

        // Se não houver erros, os dados digitados serão armazenados na conta do usuário, será criada uma sessão para armazenar o nome do jogo comprado, e contudo isso o usuário será redirecionado para a página de 'compra-finalizada'.
        if(count($mensagem_erro) == 0){
            $dados->FinalizarCompraDadosUsuario($info['id'], $_SESSION['cep'], $_SESSION['cidade'], $_SESSION['endereco'], $_SESSION['numero'], $_SESSION['bairro'], $_SESSION['complemento'], $estado_escolhido, $_SESSION['telefone']);

            if(isset($_SESSION['nome_game_carrinho'])){
                $_SESSION['identificacao_compra_finalizada_carrinho'] = $_SESSION['nome_game_carrinho'];
            }else{
                $_SESSION['identificacao_compra_finalizada'] = $_SESSION['nome_game'];
            }

            header('location: compra-finalizada.php');
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

    <title>Finalizar Compra - SatuctGames</title>
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

                            <?php // Verificando se não tem ninguém logado.
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
                                <?php // Se a sessão atual for um administrador ou moderador, essa opção do menu será mostrada.
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

    <!-- FINALIZAR COMPRA -->
    <div class="container-fluid mt-5 py-5">
        <div class="row">
            <div class="col-12">
                <div class="row mb-5 py-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                    <div class="col-12">
                        <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">FINALIZAR COMPRA</span>
                    </div>
                </div>

                <form class="row ml-2" method="POST" autocomplete="off">
                    <div class="col-12 col-md-7 col-xl-6 mb-5">
                        <h5 class="mb-4">Dados <small class="text-cinza-2">(Todos os dados abaixo são obrigatórios)</small></h5>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="cep" class="text-cinza-2"><i class="fas fa-hotel pr-2"></i>CEP</label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: 00000-000" id="cep" name="cep" <?php
                                // Se existe uma sessão de login e se o cep do usuário for diferente de 0, será adicionado um value com o cep do usuário.
                                if(isset($info['cep']) && $info['cep'] != 0){
                                    echo "value='$info[cep]'";
                                }
                                
                                // Se existe a sessão 'cep' e o cep do banco de dados for igual a 0, será adicionado um value com o valor da sessão cep.
                                if(isset($_SESSION['cep']) && $info['cep'] == 0){
                                    echo "value='$_SESSION[cep]'";
                                }
                                ?>>
                                <small class="form-text text-danger d-none" id="aviso-erro-cep">Esse cep não existe</small>
                            </div>
                            <div class="form-group col-6">
                                <label for="cidade" class="text-cinza-2"><i class="fas fa-city pr-2"></i>Cidade</label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: São Paulo" id="cidade" name="cidade" <?php
                                // Se existe uma sessão de login e o usuário tiver uma cidade cadastrada, será adicionado um value com essa cidade.
                                if(isset($info['cidade']) && !empty($info['cidade'])){
                                    echo "value='$info[cidade]'";
                                }
                                
                                // Se existe uma sessão 'cidade' e o usuário não tiver uma cidade cadastrada, será adicionado um value com o valor da sessão 'cidade'.
                                if(isset($_SESSION['cidade']) && empty($info['cidade'])){
                                    echo "value='$_SESSION[cidade]'";
                                }
                                ?>>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="endereco" class="text-cinza-2"><i class="fas fa-map-marked-alt pr-2"></i>Endereço</label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: Rua dos Rouxinóis Amarelos" id="endereco" name="endereco" <?php
                                // Se existe uma sessão de login e o usuário tiver um endereço cadastrado, será adicionado um value com esse endereço.
                                if(isset($info['endereco']) && !empty($info['endereco'])){
                                    echo "value='$info[endereco]'";
                                }

                                // Se existe uma sessão 'endereco' e o usuário não tiver um endereço cadastrado, será adicionado um value com o valor da sessão 'endereco'.
                                if(isset($_SESSION['endereco']) && empty($info['endereco'])){
                                    echo "value='$_SESSION[endereco]'";
                                }
                                ?>>
                            </div>
                            <div class="form-group col-6">
                                <label for="numero" class="text-cinza-2"><i class="fas fa-home pr-2"></i>Número<small class="text-cinza-2"> (número da casa)</small></label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: 100" id="numero" name="numero" <?php
                                // Se existe uma sessão de login e o usuário tiver o número da sua casa cadastrado com valor diferente de 0, será adicionado um value com esse número.
                                if(isset($info['numero']) && $info['numero'] != 0){
                                    echo "value='$info[numero]'";
                                }
                                
                                // Se existe uma sessão 'numero' e o número da sua casa for igual a 0, será adicionado um value com o valor da sessão 'numero'.
                                if(isset($_SESSION['numero']) && $info['numero'] == 0){
                                    echo "value='$_SESSION[numero]'";
                                }
                                ?>>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="bairro" class="text-cinza-2"><i class="fas fa-users pr-2"></i>Bairro</label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: Vila Ayrosa" id="bairro" name="bairro" <?php
                                // Se existe uma sessão de login e o usuário tiver o bairro cadastrado, será adicionado um value com esse bairro.
                                if(isset($info['bairro']) && !empty($info['bairro'])){
                                    echo "value='$info[bairro]'";
                                }

                                // Se existe uma sessão 'bairro' e o bairro no banco de dados estiver vázio, será adicionado um value com o valor da sessão 'bairro'.
                                if(isset($_SESSION['bairro']) && empty($info['bairro'])){
                                    echo "value='$_SESSION[bairro]'";
                                }
                                ?>>
                            </div>
                            <div class="form-group col-6">
                                <label for="complemento" class="text-cinza-2"><i class="fas fa-building pr-2"></i>Complemento</label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: Casa, Apartamento, Condomínio..." id="complemento" name="complemento" <?php
                                // Se existe uma sessão de login e o usuário tiver o complemento cadastrado, será adicionado um value com esse complemento.
                                if(isset($info['complemento']) && !empty($info['complemento'])){
                                    echo "value='$info[complemento]'";
                                }

                                // Se existe uma sessão 'complemento' e o complemento no banco de dados estiver vázio, será adicionado um value com o valor da sessão 'complemento'.
                                if(isset($_SESSION['complemento']) && empty($info['complemento'])){
                                    echo "value='$_SESSION[complemento]'";
                                }
                                ?>>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="estado" class="text-cinza-2"><i class="fas fa-flag pr-2"></i>Estado</label>
                                <select class="form-control form-control-alt-2" id="estado" name="estado">
                                    <?php
                                        // Se existe uma sessão de login e o usuário tiver o estado cadastrado, será adicionado um option principal com esse estado. Se não, será mostrado um 'Escolha o seu estado' como option principal, com valor 0.
                                        if(isset($info['estado']) && !empty($info['estado'])){ ?>
                                            <option value="<?= $info['estado'] ?>" selected><?= $info['estado'] ?></option>
                                  <?php }else{ ?>
                                            <option value="0" selected>Escolha o seu estado</option>
                                  <?php }
                                    ?>
                                    <?php
                                        // Separando todos nomes de estados para mostrar no select
                                        foreach($estados as $estado){
                                            for($id_estado = 0; $id_estado < count($estado); $id_estado++){
                                                $mostrar_estado = $estado[$id_estado];
                                                if(isset($info['estado']) && !empty($info['estado'])){
                                                    // Se o usuário já tiver um 'estado' cadastrado, esse estado não será mostrado na lista de estados;
                                                    if($info['estado'] == $mostrar_estado['nome']){
                                                        continue;
                                                    }
                                                }
                                                ?>
                                                <option value="<?= $mostrar_estado['nome'] ?>"> <?= $mostrar_estado['nome'] ?> </option>"; <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="telefone" class="text-cinza-2"><i class="fas fa-phone pr-2"></i>Telefone</label>
                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: 81900000000" id="telefone" name="telefone" <?php
                                // Se existe uma sessão de login e o usuário tiver o telefone cadastrado com valor diferente de 0, será adicionado um value com esse telefone.
                                if(isset($info['telefone']) && $info['telefone'] != 0){
                                    echo "value='$info[telefone]'";
                                }

                                // Se existe uma sessão 'telefone' e o telefone do usuário for igual a 0, será adicionado um value com o valor da sessão 'telefone'.
                                if(isset($_SESSION['telefone']) && $info['telefone'] == 0){
                                    echo "value='$_SESSION[telefone]'";
                                }
                                ?>>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-5 col-xl-6 mb-5">
                        <h5 class="text-preto mb-4">Métodos de pagamento</h5>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metodo-pagamento" id="pagamento-teste" value="Pagamento-teste" <?php
                            // Se existir a sessão metodo_pagamento e essa sessão ter o método de pagamento 'Pagamento-teste', ele ficará marcado.
                            if(isset($_SESSION['metodo_pagamento']) && $_SESSION['metodo_pagamento'] == 'Pagamento-teste'){
                                echo "checked";
                            }
                            ?>>
                            <label class="form-check-label" for="pagamento-teste">
                                Pagamento teste
                            </label>
                        </div>

                        <div class="form-check disabled">
                            <input class="form-check-input" type="radio" name="metodo-pagamento" id="boleto-pagamento" value="Boleto" disabled <?php
                            // Se existir a sessão metodo_pagamento e essa sessão ter o método de pagamento 'Boleto', ele ficará marcado.
                            if(isset($_SESSION['metodo_pagamento']) && $_SESSION['metodo_pagamento'] == 'Boleto'){
                                echo "checked";
                            }
                            ?>>
                            <label class="form-check-label" for="boleto-pagamento">
                                Boleto bancário
                            </label>
                            <small class="ml-2 text-danger">Em breve</small>
                        </div>

                        <div class="form-check disabled">
                            <input class="form-check-input" type="radio" name="metodo-pagamento" id="pagseguro-pagamento" value="Pagseguro" disabled <?php
                            // Se existir a sessão metodo_pagamento e essa sessão ter o método de pagamento 'Pagseguro', ele ficará marcado.
                            if(isset($_SESSION['metodo_pagamento']) && $_SESSION['metodo_pagamento'] == 'Pagseguro'){
                                echo "checked";
                            }
                            ?>>
                            <label class="form-check-label" for="pagseguro-pagamento">
                                Pagseguro
                            </label>
                            <small class="ml-2 text-danger">Em breve</small>
                        </div>

                        <div class="form-check mb-3 disabled">
                            <input class="form-check-input" type="radio" name="metodo-pagamento" id="paypal-pagamento" value="Paypal" disabled <?php
                            // Se existir a sessão metodo_pagamento e essa sessão ter o método de pagamento 'Paypal', ele ficará marcado.
                            if(isset($_SESSION['metodo_pagamento']) && $_SESSION['metodo_pagamento'] == 'Paypal'){
                                echo "checked";
                            }
                            ?>>
                            <label class="form-check-label" for="paypal-pagamento">
                                Paypal
                            </label>
                            <small class="ml-2 text-danger">Em breve</small>
                        </div>

                        <h5 class="text-preto mb-3 mt-3">Métodos de entrega</h5>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metodo-entrega" id="sedex-entrega" value="Sedex" <?php
                            // Se existe uma sessão de login e se o cep do usuário for diferente de 0, nada será adicionado, se não, um disabled será adicionado.
                            if(isset($info['cep']) && $info['cep'] != 0){
                                echo '';
                            }else{
                                echo 'disabled';
                            }

                            // Se existir a sessão metodo_entrega e essa sessão ter o método de entrega 'Sedex', ele ficará marcado.
                            if(isset($_SESSION['metodo_entrega']) && $_SESSION['metodo_entrega'] == 'Sedex'){
                                echo "checked";
                            }
                            ?>>
                            <label class="form-check-label" for="sedex-entrega">
                                SEDEX
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="metodo-entrega" id="pac-entrega" value="PAC" <?php
                            // Se existe uma sessão de login e se o cep do usuário for diferente de 0, nada será adicionado, se não, um disabled será adicionado.
                            if(isset($info['cep']) && $info['cep'] != 0){
                                echo '';
                            }else{
                                echo 'disabled';
                            }

                            // Se existir a sessão metodo_entrega e essa sessão ter o método de entrega 'PAC', ele ficará marcado.
                            if(isset($_SESSION['metodo_entrega']) && $_SESSION['metodo_entrega'] == 'PAC'){
                                echo "checked";
                            }
                            ?>>
                            <label class="form-check-label" for="pac-entrega">
                                PAC
                            </label>
                        </div>

                        <div class="row d-flex flex-row">
                            <h6 class="ml-3 mr-2 mt-1">Frete:</h6>
                            <h6 class="text-cinza-2 mr-2 mt-1" id="frete-valor">R$0</h6>
                        </div>

                        <div class="row d-flex flex-row">
                            <h6 class="ml-3 mr-2 mt-1">Prazo de entrega:</h6>
                            <h6 class="text-cinza-2 mr-2 mt-1" id="prazo-entrega"></h6>
                        </div>

                        <div class="row d-flex flex-row mt-4">
                            <h6 class="ml-3 mr-2">Total carrinho:</h6>
                            <h6 class="text-cinza-2 mr-2" id="total-carrinho">R$<?php
                            // Se existir a sessão total_compra, será mostrado seu valor, se não, será mostrado o valor 0.
                            if(isset($_SESSION['total_compra'])){
                                echo $_SESSION['total_compra'];
                            }else{
                                echo "0";
                            }
                            ?></h6>
                        </div>

                        <div class="row d-flex flex-row mt-1">
                            <h6 class="ml-3 mr-2">Total a pagar:</h6>
                            <h6 class="text-cinza-2 mr-2" id="total-compra">R$<?php
                            // Se existir a sessão total_compra, será mostrado seu valor, se não, será mostrado o valor 0.
                            if(isset($_SESSION['total_compra'])){
                                echo $_SESSION['total_compra'];
                            }else{
                                echo "0";
                            }
                            ?></h6>
                        </div>
                    </div>

                    <div class="col-12 text-right mt-2 mb-5">
                        <button type="submit" class="btn btn-cinza-form btn-sm py-2 px-2" name="finalizar-compra"><i class="fas fa-wallet pl-1 pr-2"></i>Finalizar compra</button>
                    </div>
                </form>

                <div class="row">
                    <img src="imagens/loader.gif" alt="GIF de carregamento" id="carregamento-dados" class="my-3 mx-auto d-none">
                </div>

                <div claass="row">
                    <div class="col-12 col-md-7 col-lg-6 col-xl-5 px-0">
                        <?php 
                        // Se houver mensagens de erro, elas serão mostradas na página
                        if(count($mensagem_erro) > 0){ ?>
                            <div class="alert alert-danger d-flex text-break mb-5" role="alert">
                                <span class="mr-3"><?php foreach($mensagem_erro as $erro){ echo "$erro <br>"; } ?></span>
                                <button type="button" class="close ml-auto h-100" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FINALIZAR COMPRA -->

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

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>