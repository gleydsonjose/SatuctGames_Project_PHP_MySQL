<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador, moderador ou usuário, a página será redirecionada para a página inicial.
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador']) && !isset($_SESSION['id_usuario'])){
        header('location: index.php');
    }

    // Se a sessão 'identificacao_compra_finalizada' existir, será verificado se ela é diferente do nome do game escolhido antes, se sim, o usuário será redirecionado para a página inicial.
    if(isset($_SESSION['identificacao_compra_finalizada'])){
        if($_SESSION['identificacao_compra_finalizada'] != $_SESSION['nome_game']){
            header('location: index.php');
        }

    }else if(isset($_SESSION['identificacao_compra_finalizada_carrinho'])){
        // Se a sessão 'identificacao_compra_finalizada_carrinho' existir, será verificado se ela é diferente do nome do game no carrinho escolhido antes, se sim, o usuário será redirecionado para a página inicial.
        if($_SESSION['identificacao_compra_finalizada_carrinho'] != $_SESSION['nome_game_carrinho']){
            header('location: index.php');
        }

    }else{
        // Se nenhuma dessas sessões existirem, o usuário será redirecionado para a página inicial.
        header('location: index.php');
    }
    // OBS: Essa verificação é para evitar que o usuário escolha outro game e tente entrar nessa página sem ter finalizado a compra, a sessão pedida só é criada quando ele finaliza a compra do jogo que ele realmente quer.

    // Script com métodos para várias coisas.
    require_once 'scripts/dados.php';
    $compra_finalizada = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // PHPMailer
    require_once 'PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer();
    $mail->Port = '465';
    $mail->Host = 'smtp.gmail.com';
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Mailer = 'smtp';
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = 'true';
    $mail->Username = 'Conta gmail liberada para enviar email para outlook, hotmail...';
    $mail->Password = 'Senha do email gmail';
    $mail->SingleTo = 'true';
    $mail->From = 'Conta gmail liberada para enviar email para outlook, hotmail...';
    $mail->FromName = 'Seu nome ou da empresa';
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

    <!-- COMPRA FINALIZADA -->
    <div class="container-fluid mt-5 pt-5">
        <div class="row">
            <div class="col-12 text-center my-5 py-5">
                <?php
                    // Se a versão do game for fisica, tudo abaixo será executado.
                    if(isset($_SESSION['versao_game']) && $_SESSION['versao_game'] == 'Física'){
                        // Inserindo os dados da transação em uma tabela no banco de dados.
                        $compra_finalizada->InserirTransacao($info['id'], $_SESSION['nome_usuario'], $_SESSION['quantidade'], $_SESSION['nome_game'], $_SESSION['versao_game'], $_SESSION['total_a_pagar'], $_SESSION['metodo_pagamento'], $_SESSION['metodo_entrega'], $_SESSION['frete'], $_SESSION['prazo_entrega'], $_SESSION['idgame'], '0');

                        // PHPMailer
                        $mail->addAddress($info['email']);
                        $mail->Subject = "Compra realizada";
                        $mail->Body = "<h2><strong>Olá $info[nome], você acabou de realizar a compra de:</strong></h2>
                                    <p><strong>$_SESSION[quantidade]x $_SESSION[nome_game] Versão $_SESSION[versao_game].</strong></p>
                                    <p><strong>Método de pagamento escolhido:</strong> $_SESSION[metodo_pagamento]</p>
                                    <p><strong>Total a pagar:</strong> R$$_SESSION[total_a_pagar]</p>
                                    <p><strong>Método de entrega escolhido:</strong> $_SESSION[metodo_entrega]</p>
                                    <p><strong>Prazo estimado de entrega:</strong> $_SESSION[prazo_entrega] dias úteis.</p>";

                        if(!$mail->Send()){
                            echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                        }

                        // Mensagem de sucesso para mostrar para o usuário
                        echo " <h4>Compra finalizada com sucesso - Obrigado por comprar conosco $_SESSION[nome_usuario].</h4>
                            <h5 class='my-3'>Total a pagar: R$$_SESSION[total_a_pagar]</h5>
                            <h6>Você receberá um email com mais detalhes</h6>
                            <a href='index.php' class='btn btn-cinza-form btn-sm py-2 px-2 mt-2'><i class='fas fa-home pr-2'></i>Voltar para o inicio</a>";


                        // Subtraindo a quantidade total do game pela quantidade escolhida pelo comprador, e logo após a isso será passado o resultado para uma variável.
                        $subtracao_quantidade = $compra_finalizada->BuscarDadosGames($_SESSION['idgame'])['quantidade'] - $_SESSION['quantidade'];

                        // Se o resultado da 'subtracao_quantidade' for igual a 0, a disponibilidade vai para 0, se não, ela vai para 1.
                        if($subtracao_quantidade == 0){
                            $disponibilidade = 0;
                        }else{
                            $disponibilidade = 1;
                        }

                        // Alterando a disponibilidade e a quantidade do game pelo seu id.
                        $compra_finalizada->AlterarDisponibilidadeQuantidadeGame($_SESSION['idgame'], $disponibilidade, $subtracao_quantidade);

                        // Removendo todas sessões criada para a transação e compra ser realizada, apenas a sessão do login que vai permanecer ativa.
                        foreach($_SESSION as $chave=>$valor){
                            $buscar_sessao = strpos($chave, 'id_');
                            if($buscar_sessao === false){
                                unset($_SESSION[$chave]);
                            }
                        }

                    } else if(isset($_SESSION['versao_game']) && $_SESSION['versao_game'] == 'Digital'){ // Se a versão do game for digital, tudo abaixo será executado.
                        // Inserindo os dados da transação em uma tabela no banco de dados.
                        $compra_finalizada->InserirTransacao($info['id'], $_SESSION['nome_usuario'], $_SESSION['quantidade'], $_SESSION['nome_game'], $_SESSION['versao_game'], $_SESSION['total_a_pagar'], $_SESSION['metodo_pagamento'], '', '0', '0', $_SESSION['idgame'], '0');

                        // PHPMailer
                        $mail->addAddress($info['email']);
                        $mail->Subject = "Compra realizada";
                        $mail->Body = "<h2><strong>Olá $info[nome], você acabou de realizar a compra de:</strong></h2>
                                    <p><strong>$_SESSION[quantidade]x $_SESSION[nome_game] Versão $_SESSION[versao_game].</strong></p>
                                    <p><strong>Método de pagamento escolhido:</strong> $_SESSION[metodo_pagamento]</p>
                                    <p><strong>Total a pagar:</strong> R$$_SESSION[total_a_pagar]</p>
                                    <p>Dentro de 24 horas você vai receber sua key.</p>";

                        if(!$mail->Send()){
                            echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                        }

                        // Mensagem de sucesso para mostrar para o usuário
                        echo " <h4>Compra finalizada com sucesso - Obrigado por comprar conosco $_SESSION[nome_usuario].</h4>
                            <h5 class='my-3'>Total a pagar: R$$_SESSION[total_a_pagar]</h5>
                            <h6>Você receberá um email com mais detalhes</h6>
                            <a href='index.php' class='btn btn-cinza-form btn-sm py-2 px-2 mt-2'><i class='fas fa-home pr-2'></i>Voltar para o inicio</a>";

                        // Subtraindo a quantidade total do game pela quantidade escolhida pelo comprador, e logo após a isso será passado o resultado para uma variável.
                        $subtracao_quantidade = $compra_finalizada->BuscarDadosGames($_SESSION['idgame'])['quantidade'] - $_SESSION['quantidade'];

                        // Se o resultado da 'subtracao_quantidade' for igual a 0, a disponibilidade vai para 0, se não, ela vai para 1.
                        if($subtracao_quantidade == 0){
                            $disponibilidade = 0;
                        }else{
                            $disponibilidade = 1;
                        }

                        // Alterando a disponibilidade e a quantidade do game pelo seu id.
                        $compra_finalizada->AlterarDisponibilidadeQuantidadeGame($_SESSION['idgame'], $disponibilidade, $subtracao_quantidade);

                        // Removendo todas sessões criada para a transação e compra ser realizada, apenas a sessão do login que vai permanecer ativa.
                        foreach($_SESSION as $chave=>$valor){
                            $buscar_sessao = strpos($chave, 'id_');
                            if($buscar_sessao === false){
                                unset($_SESSION[$chave]);
                            }
                        }

                    } else if(isset($_SESSION['idgame_carrinho']) && count($_SESSION['idgame_carrinho']) > 0){
                        
                        // Variável para guardar o maior id da transação no carrinho do usuário, vai começar recebendo 0.
                        $maior_id_transacao_carrinho = 0;

                        // Colocando todas ids de transação no carrinho do usuário numa variável
                        $QuantidadeTransacoes = $compra_finalizada->QuantidadeTransacoesCarrinhoUsuario($info['id']);

                        // Fazendo a filtragem dos ids para depois encontrar o maior id e colocar ele numa variável.
                        foreach($QuantidadeTransacoes as $valor_QT){
                            foreach($valor_QT as $id_transacao_carrinho){
                                if($id_transacao_carrinho > $maior_id_transacao_carrinho){
                                    $maior_id_transacao_carrinho = $id_transacao_carrinho;
                                }
                            }
                        }

                        // O novo id de transação vai ser o maior id encontrado acima + 1
                        $maior_id_transacao_carrinho++;
                        
                        // Se existir games no carrinho, será adicionado todos os dados do game e da compra na transação do usuário.
                        // Se a sessão 'quantidade_game' existir, será retornado o valor dela, se não, vai ser o valor da sessão 'quantidade_escolhida_carrinho'.
                        if(isset($_SESSION['idgame_carrinho'])){
                            foreach($_SESSION['idgame_carrinho'] as $idgame_carrinho){
                                $compra_finalizada->InserirTransacao($info['id'], $_SESSION['nome_usuario'], 

                                isset($_SESSION['quantidade_game'][$idgame_carrinho]) ? $_SESSION['quantidade_game'][$idgame_carrinho] : $_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho],
                                
                                $_SESSION['nome_game_carrinho'][$idgame_carrinho], $_SESSION['versao_game_carrinho'][$idgame_carrinho], $_SESSION['total_a_pagar'], $_SESSION['metodo_pagamento'], $_SESSION['metodo_entrega'], $_SESSION['frete'], $_SESSION['prazo_entrega'], $idgame_carrinho, $maior_id_transacao_carrinho);
                            }
                        }

                        // PHPMailer
                        $mail->addAddress($info['email']);
                        $mail->Subject = "Compra realizada";

                        $Body = "<h2><strong>Olá $info[nome], você acabou de realizar a compra de:</strong></h2>";
                        foreach($_SESSION['idgame_carrinho'] as $idgame_carrinho){
                            // Antes de fazer a subtração, será verificado se o usuário fez apenas a escolha da quantidade pela página do game e depois alterou a quantidade no carrinho
                            // Subtraindo a quantidade total do game pela quantidade escolhida pelo comprador, e logo após a isso será passado o resultado para uma variável.
                            if(isset($_SESSION['quantidade_game'][$idgame_carrinho])){
                                $subtracao_quantidade = $compra_finalizada->BuscarDadosGames($_SESSION['idgame_carrinho'][$idgame_carrinho])['quantidade'] - $_SESSION['quantidade_game'][$idgame_carrinho];
                            }else{
                                $subtracao_quantidade = $compra_finalizada->BuscarDadosGames($_SESSION['idgame_carrinho'][$idgame_carrinho])['quantidade'] - $_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho];
                            }

                            // Se o resultado da 'subtracao_quantidade' for igual a 0, a disponibilidade vai para 0, se não, ela vai para 1.
                            if($subtracao_quantidade == 0){
                                $disponibilidade = 0;
                            }else{
                                $disponibilidade = 1;
                            }

                            // Alterando a disponibilidade e a quantidade do game pelo seu id.
                            $compra_finalizada->AlterarDisponibilidadeQuantidadeGame($_SESSION['idgame_carrinho'][$idgame_carrinho], $disponibilidade, $subtracao_quantidade);

                            // Se a sessão 'quantidade_game' existir, será mostrado uma mensagem que vai usar o valor dessa sessão.
                            if(isset($_SESSION['quantidade_game'][$idgame_carrinho])){
                                $Body .= "<p><strong>".$_SESSION['quantidade_game'][$idgame_carrinho]."x ".$_SESSION['nome_game_carrinho'][$idgame_carrinho]." Versão ".$_SESSION['versao_game_carrinho'][$idgame_carrinho].".</strong></p>";
                            }else{
                                // Se não, será usado o valor da sessão 'quantidade_escolhida_carrinho' nessa mensagem.
                                $Body .= "<p><strong>".$_SESSION['quantidade_escolhida_carrinho'][$idgame_carrinho]."x ".$_SESSION['nome_game_carrinho'][$idgame_carrinho]." Versão ".$_SESSION['versao_game_carrinho'][$idgame_carrinho].".</strong></p>";
                            }
                        }
                        $Body .= "<p><strong>Método de pagamento escolhido:</strong> $_SESSION[metodo_pagamento]</p>
                        <p><strong>Total a pagar:</strong> R$$_SESSION[total_a_pagar]</p>
                        <p><strong>Método de entrega escolhido:</strong> $_SESSION[metodo_entrega]</p>
                        <p><strong>Prazo estimado de entrega:</strong> $_SESSION[prazo_entrega] dias úteis.</p>";

                        $mail->Body = $Body;

                        if(!$mail->Send()){
                            echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                        }

                        // Mensagem de sucesso para mostrar para o usuário
                        echo " <h4>Compra finalizada com sucesso - Obrigado por comprar conosco $_SESSION[nome_usuario].</h4>
                            <h5 class='my-3'>Total a pagar: R$$_SESSION[total_a_pagar]</h5>
                            <h6>Você receberá um email com mais detalhes</h6>
                            <a href='index.php' class='btn btn-cinza-form btn-sm py-2 px-2 mt-2'><i class='fas fa-home pr-2'></i>Voltar para o inicio</a>";

                        // Removendo todas sessões criada para a transação e compra ser realizada, apenas a sessão do login que vai permanecer ativa.
                        foreach($_SESSION as $chave=>$valor){
                            $buscar_sessao = strpos($chave, 'id_');
                            if($buscar_sessao === false){
                                unset($_SESSION[$chave]);
                            }
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    <!-- COMPRA FINALIZADA -->

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