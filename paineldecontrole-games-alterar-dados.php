<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }

    // Buscando dados de um game pelo seu id.
    $info_game = $dados->BuscarDadosGames($_SESSION['game_id']);

    // Arrays para armazenar mensagens de erro e sucesso.
    $mensagem_erro = [];
    $mensagem_sucesso = [];

    // Se o botão 'mudar-imagem' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mudar-imagem'])){
        // URL da imagem vai ser armazenado numa variável.
        $imagem_game = addslashes(strip_tags(trim('imagens_games/'.$_FILES['imagem-game']['name'])));

        // Se nenhuma imagem for escolhida, será mostrado uma mensagem de erro.
        if(!$_FILES['imagem-game']['name']){
            $mensagem_erro[] = "Escolha uma imagem";
        }else{
            // Se uma imagem for escolhida, tudo abaixo será executado.
            // Passando as dimensões da imagem para uma variável.
            // [0] = Largura da imagem.
            // [1] = Altura da imagem.
            $dimensoes_imagem = getimagesize($_FILES['imagem-game']['tmp_name']);
            $largura_imagem = $dimensoes_imagem[0];
            $altura_imagem = $dimensoes_imagem[1];
            
            // Se a imagem for menor que 300x300(LxA), será mostrado uma mensagem de erro.
            if($largura_imagem < 300 && $altura_imagem < 300){
                $mensagem_erro[] = "A imagem precisa ser maior que 300x300";
            }
        }

        // Passando o tipo da imagem para uma variável.
        $tipo_imagem = pathinfo($imagem_game, PATHINFO_EXTENSION);

        // Se o tipo da imagem não for jpg, jpeg ou png, será mostrado uma mensagem de erro.
        if(!in_array($tipo_imagem, array('jpg', 'jpeg', 'png'))){
            $mensagem_erro[] = "O tipo da imagem precisa ser JPG, JPEG ou PNG.";
        }

        // Se o tamanho da imagem for igual a 0 ou maior que 500Kb, será mostrado uma mensagem de erro.
        if($_FILES['imagem-game']['size'] == 0 || $_FILES['imagem-game']['size'] > 500000){
            $mensagem_erro[] = "O tamanho da imagem precisa ser menor que 500Kb";
        }

        // Se não houver nenhum erro, tudo abaixo será executado.
        if(count($mensagem_erro) == 0){
            // Removendo a imagem anterior do game.
            unlink($info_game['imagem']);

            // Armazenando a imagem numa pasta.
            copy($_FILES['imagem-game']['tmp_name'], $imagem_game);

            // Mudando a imagem do game pelo seu id.
            $dados->MudarImagemGame($_SESSION['game_id'], $imagem_game);

            // Atualizando a página.
            header('refresh: 0');
        }
    }

    // Se o botão 'alterar-dados' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar-dados'])){
        // Dados recebidos serão armazenados em variáveis.
        $nome_game = addslashes(strip_tags(trim($_POST['nome-game'])));
        $titulo_pagina = addslashes(strip_tags(trim($_POST['titulo-pagina'])));
        $categoria = addslashes(strip_tags(trim($_POST['categoria'])));
        $informacoes = trim($_POST['informacoes']);
        $disponibilidade = addslashes(strip_tags(trim($_POST['disponibilidade'])));
        $produto_promocional = addslashes(strip_tags(trim($_POST['produto-promocional'])));
        $lancamento = addslashes(strip_tags(trim($_POST['lancamento'])));
        $preco_antigo = addslashes(strip_tags(trim($_POST['preco-antigo'])));
        $quantidade = addslashes(strip_tags(trim($_POST['quantidade'])));
        $preco_atual = addslashes(strip_tags(trim($_POST['preco-atual'])));

        // Verificando se cada campo está vazio.
        if(strlen($nome_game) == 0){
            $mensagem_erro[] = "Preencha o campo Nome do game.";
        }

        if(strlen($titulo_pagina) == 0){
            $mensagem_erro[] = "Preencha o campo Título da página.";
        }

        if(strlen($informacoes) == 0){
            $mensagem_erro[] = "Preencha o campo Informações.";
        }

        if($quantidade == ""){
            $mensagem_erro[] = 'Preencha o campo Quantidade';
        }

        if(strlen($preco_atual) == 0){
            $mensagem_erro[] = "Preencha o campo Preço atual.";
        }

        // Se a opção de produto promocional escolhida foi '1', será verificado se o campo de preço antigo está vazio, se estiver, um erro vai aparecer.
        if($produto_promocional == 1){
            if(strlen($preco_antigo) == 0){
                $mensagem_erro[] = "Preencha o campo Preço antigo.";
            }
        }

        // Se a opção de produto disponível for '1(Sim)' e a quantidade for igual a 0, uma mensagem de erro será mostrada.
        if($disponibilidade == 1 && $quantidade == 0){
            $mensagem_erro[] = "Um produto disponível precisa ter quantidade diferente de 0.";
        }

        // Se a opção de produto disponível for '0(Não)' e a quantidade for diferente de 0, uma mensagem de erro será mostrada.
        if($disponibilidade == 0 && $quantidade != 0){
            $mensagem_erro[] = "Um produto indisponível precisa ter 0 quantidade.";
        }

        // Se a categoria for igual a 'Escolha a categoria', que não é uma categoria válida, será mostrado uma mensagem de erro.
        if($categoria == 'Escolha a categoria'){
            $mensagem_erro[] = "Escolha uma categoria.";
        }

        // Se não houver nenhum erro, tudo abaixo será executado.
        if(count($mensagem_erro) == 0){
            // Se o produto não for promocional, ele será 0, então se for igual a 0, o preço antigo vai receber 0.
            if($produto_promocional == 0){
                $preco_antigo = 0;
            }
            
            // Alterando os dados do game de sua página e banco de dados a partir de seu id.
            $dados->AlterarDadosGame($_SESSION['game_id'], $nome_game, $titulo_pagina, $categoria, $informacoes, $disponibilidade, $produto_promocional, $lancamento, $quantidade, $preco_atual, $preco_antigo);

            // Logo após tudo acima ser feito, uma mensagem de sucesso será mostrada.
            $mensagem_sucesso[] = "Dados do game foram alterados com sucesso.";

            // Atualizando a página em 5 segundos.
            echo "<meta HTTP-EQUIV='refresh' CONTENT='5;URL=paineldecontrole-games-alterar-dados.php'>";
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

    <title>Painel de Controle Games - SatuctGames</title>
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
            <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                <h4 class="text-preto">Painel de Controle</h4>
                <h5 class="text-preto">Games</h5>
                <h6 style="color: #227cb9;"><?= $tag ?></h6>
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
                                <a href="index.php" class="nav-link btn-menu-cinza">Voltar para loja</a>
                            </li>

                            <li class="nav-item">
                                <a href="paineldecontrole-usuarios.php" class="nav-link btn-menu-cinza">Usuários</a>
                            </li>

                            <li class="nav-item">
                                <a href="paineldecontrole-games.php" class="nav-link btn-menu-cinza">Games</a>
                            </li>

                            <li class="nav-item">
                                <a href="paineldecontrole-cupons.php" class="nav-link btn-menu-cinza">Cupons</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto">
                        <?php // Se tem alguém logado
                            if(isset($info)){ ?>
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

    <!-- PAINEL DE CONTROLE GAMES - ALTERAR DADOS -->
    <div class="container-fluid mt-5 pt-5">
            <div class="row">
                <div class="col-12">
                    <div class="row mb-3 py-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                        <div class="col-12">
                            <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">ALTERAR DADOS</span>
                        </div>
                    </div>

                    <div class="container-fluid mt-5 pt-4">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8 col-sm-9 col-11 mx-auto">
                                <form method="POST" enctype="multipart/form-data" autocomplete="off">
                                    <div class="form-row mt-3">
                                        <div class="form-group col-5 col-md-4">
                                            <h6>Imagem atual</h6>
                                            <img src="<?= $info_game['imagem'] ?>?<?= time() ?>" alt="Imagem do <?= $info_game['nome_game'] ?>" class="img-fluid" style="width: 150px; height: 150px; filter: grayscale(20%);">
                                        </div>

                                        <div class="form-group col-7 col-md-8">
                                            <label for="imagem-game-alterar-dados" class="text-cinza-2"><i class="fas fa-image pr-2"></i>Alterar imagem</label>
                                            <input type="file" class="form-control-file" id="imagem-game-alterar-dados" name="imagem-game" accept="image/*">
                                            <button type="submit" class="btn btn-cinza-form w-100 py-2 mt-2" name="mudar-imagem"><i class="far fa-edit pr-2"></i>Mudar imagem</button>
                                            <span class="form-text text-cinza-2">(A imagem precisa ser maior que 300x300)</span>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                        <label for="nome-game-alterar-dados" class="text-cinza-2"><i class="fas fa-gamepad pr-2"></i>Nome do game</label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: Batman Arkham Origins" id="nome-game-alterar-dados" name="nome-game" <?php
                                        // Se existir o array 'info_game', será mostrado os dados da key nome_game.
                                        if(isset($info_game)){ echo "value='".$info_game['nome_game']."'"; }
                                        ?>>
                                    </div>

                                    <div class="form-group">
                                        <label for="titulo-pagina-alterar-dados" class="text-cinza-2"><i class="fas fa-pencil-alt pr-2"></i>Título da página</label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: Batman Arkham Origins" id="titulo-pagina-alterar-dados" name="titulo-pagina" <?php
                                        // Se existir o array 'info_game', será mostrado os dados da key titulo_pagina.
                                        if(isset($info_game)){ echo "value='".$info_game['titulo_pagina']."'"; }
                                        ?>>
                                        <span class="text-danger">Não remova o '- SatuctGames'</span>
                                    </div>

                                    <label for="categoria-alterar-dados" class="text-cinza-2"><i class="fas fa-list-alt pr-2"></i>Categoria</label>
                                    <select class="form-control form-control-alt-2" id="categoria-alterar-dados" name="categoria">
                                    <option value="Escolha a categoria" <?php
                                        // Se existir o array 'info_game' e o valor da key 'categoria' for igual a 'Escolha a categoria', esse option será selecionado.
                                        if(isset($info_game) && $info_game['categoria'] == 'Escolha a categoria'){ 
                                            echo "selected";
                                        } 
                                        ?>>Escolha a categoria</option>
                                        <option value="Ação" <?php
                                        // Se existir o array 'info_game' e o valor da key 'categoria' for igual a 'Ação', esse option será selecionado.
                                        if(isset($info_game) && $info_game['categoria'] == 'Ação'){ 
                                            echo "selected";
                                        } 
                                        ?>>Ação</option>
                                        <option value="Aventura" <?php
                                        // Se existir o array 'info_game' e o valor da key 'categoria' for igual a 'Aventura', esse option será selecionado.
                                        if(isset($info_game) && $info_game['categoria'] == 'Aventura'){ 
                                            echo "selected";
                                        } 
                                        ?>>Aventura</option>
                                        <option value="Corrida" <?php
                                        // Se existir o array 'info_game' e o valor da key 'categoria' for igual a 'Corrida', esse option será selecionado.
                                        if(isset($info_game) && $info_game['categoria'] == 'Corrida'){ 
                                            echo "selected";
                                        } 
                                        ?>>Corrida</option>
                                        <option value="Luta" <?php
                                        // Se existir o array 'info_game' e o valor da key 'categoria' for igual a 'Luta', esse option será selecionado.
                                        if(isset($info_game['categoria']) && $info_game['categoria'] == 'Luta'){ 
                                            echo "selected";
                                        } 
                                        ?>>Luta</option>
                                    </select>

                                    <div class="form-group mt-3">
                                        <label for="informacoes-alterar-dados" class="text-cinza-2"><i class="fas fa-sticky-note pr-2"></i>Informações</label>
                                        <textarea class="form-control form-control-alt-2" id="informacoes-alterar-dados" name="informacoes" rows="7" placeholder="ex: Sobre o jogo, Conteúdo adicional..."><?php
                                        // Se existir o array 'info_game', será mostrado os dados da key informacoes.
                                        if(isset($info_game)){
                                             echo $info_game['informacoes'];
                                        }
                                        ?></textarea>
                                        <span class="form-text text-cinza-2">Ex de título para informação: <span><</span>h4<span>></span>Título<span><</span>/h4<span>></span> </span>
                                        <span class="form-text text-cinza-2">Ex de informação: <span><</span>p class="text-break text-muted mb-5"<span>></span>Informação<span><</span>/p<span>></span></span>
                                        <span class="form-text text-cinza-2">É obrigatório usar o título e informação com as tags acima.</span>
                                    </div>

                                    <div class="form-group">
                                        <h5 class="text-preto mb-2">Produto disponível?</h5>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="disponibilidade" id="produto-disponivel-sim" value="1" <?php
                                            // Se existir o array 'info_game' e o valor da key 'disponibilidade' for igual a 1, esse radio será marcado.
                                            if(isset($info_game) && $info_game['disponibilidade'] == 1){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="produto-disponivel-sim">
                                                Sim
                                            </label>
                                        </div>
                
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="disponibilidade" id="produto-disponivel-nao" value="0" <?php
                                            // Se existir o array 'info_game' e o valor da key 'disponibilidade' for igual a 0, esse radio será marcado.
                                            if(isset($info_game) && $info_game['disponibilidade'] == 0){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="produto-disponivel-nao">
                                                Não
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <h5 class="text-preto mb-2">Produto promocional?</h5>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produto-promocional" id="produto-promocional-sim" value="1" <?php
                                            // Se existir o array 'info_game' e o valor da key 'promocao' for igual a 1, esse radio será marcado.
                                            if(isset($info_game) && $info_game['promocao'] == 1){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="produto-promocional-sim">
                                                Sim
                                            </label>
                                        </div>
                
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produto-promocional" id="produto-promocional-nao" value="0" <?php
                                            // Se existir o array 'info_game' e o valor da key 'promocao' for igual a 0, esse radio será marcado.
                                            if(isset($info_game) && $info_game['promocao'] == 0){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="produto-promocional-nao">
                                                Não
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <h5 class="text-preto mb-2">Lançamento?</h5>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lancamento" id="lancamento-sim" value="1" <?php
                                            // Se existir o array 'info_game' e o valor da key 'lancamento' for igual a 1, esse radio será marcado.
                                            if(isset($info_game) && $info_game['lancamento'] == 1){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="lancamento-sim">
                                                Sim
                                            </label>
                                        </div>
                
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lancamento" id="lancamento-nao" value="0" <?php
                                            // Se existir o array 'info_game' e o valor da key 'lancamento' for igual a 0, esse radio será marcado.
                                            if(isset($info_game) && $info_game['lancamento'] == 0){
                                                echo "checked";
                                            }
                                            ?>>
                                            <label class="form-check-label" for="lancamento-nao">
                                                Não
                                            </label>
                                        </div>
                                    </div>

                                    <div <?php
                                        // Se existir o array 'info_game' e o valor da key 'promocao' for igual a 1, preço antigo será mostrado, se não for o caso, o preço antigo vai ficar escondido.
                                        if(isset($info_game) && $info_game['promocao'] == 1){
                                            echo "class='form-group preco-antigo'";
                                        }else{
                                            echo "class='form-group preco-antigo d-none'";
                                        }
                                    ?>>
                                        <label for="preco-antigo-alterar-dados" class="text-cinza-2"><i class="fas fa-dollar-sign pr-2"></i>Preço antigo</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text" id="preco-antigo-alterar-dados">R$</span>
                                            </div>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: 69,99" id="preco-antigo-alterar-dados" name="preco-antigo" <?php
                                            // Se existir o array 'info_game', será mostrado os dados da key preco_antigo.
                                            if(isset($info_game)){
                                                echo  "value='".$info_game['preco_antigo']."'";;
                                            }
                                            ?>>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <label for="quantidade-alterar-dados" class="text-cinza-2"><i class="fas fa-cubes pr-2"></i>Quantidade</label>
                                            <input type="number" class="form-control form-control-alt-2" name="quantidade" id="quantidade-alterar-dados" min="0" <?php
                                            // Se existir o array 'info_game', será mostrado os dados da key quantidade.
                                            if(isset($info_game)){
                                                echo "value='$info_game[quantidade]'";
                                            }
                                            ?>>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="preco-atual-alterar-dados" class="text-cinza-2"><i class="fas fa-dollar-sign pr-2"></i>Preço atual</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text" id="preco-atual-alterar-dados">R$</span>
                                                </div>
                                                <input type="text" class="form-control form-control-alt-2" placeholder="ex: 99,99" id="preco-atual-alterar-dados" name="preco-atual" <?php
                                                // Se existir o array 'info_game', será mostrado os dados da key preco_atual.
                                                if(isset($info_game)){
                                                    echo  "value='".$info_game['preco_atual']."'";;
                                                }
                                                ?>>
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="btn-group w-100 mt-4">
                                        <a href="paineldecontrole-games.php" class="btn btn-azul-form w-25 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                                        <button type="submit" class="btn btn-cinza-form w-75 py-2 ml-2" name="alterar-dados"><i class="fas fa-edit pr-2"></i>Alterar dados</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                
                        <div claass="row">
                            <div class="col-xl-5 col-md-7 col-11 mx-auto mt-5">
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
                            <?php
                            // Se houver a mensagem de sucesso, ela será mostrada na página
                            if(count($mensagem_sucesso) == 1){?>
                                <div class="alert alert-success d-flex text-break mb-5" role="alert">
                                    <span class="mr-3"><?php foreach($mensagem_sucesso as $sucesso){ echo "$sucesso <br>"; } ?></span>
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
        </div>
    </div>
    <!-- // PAINEL DE CONTROLE GAMES - ALTERAR DADOS -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>