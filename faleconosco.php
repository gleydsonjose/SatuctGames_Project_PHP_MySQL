<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Arrays para armazenar mensagens.
    $mensagem_erro = [];
    $mensagem_sucesso = [];

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

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar'])){
        // Dados recebidos serão armazenados em variáveis.
        $nome = addslashes(strip_tags(trim($_POST['nome'])));
        $email = addslashes(strip_tags(trim($_POST['email'])));
        $assunto = addslashes(strip_tags(trim($_POST['assunto'])));
        $mensagem = addslashes(strip_tags(trim($_POST['mensagem'])));

        // Verificando se cada campo está vazio
        if(strlen($nome) == 0){
            $mensagem_erro[] = "Preencha o campo Nome.";
        }

        if(strlen($email) == 0){
            $mensagem_erro[] = "Preencha o campo E-mail.";
        }

        if(strlen($assunto) == 0){
            $mensagem_erro[] = "Preencha o campo Assunto.";
        }

        if(strlen($mensagem) == 0){
            $mensagem_erro[] = "Preencha o campo Mensagem.";
        }

        // Verificando se o email é válido
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mensagem_erro[] = "Digite um email válido.";
        }

        // Se não houver nenhum erro, a mensagem será enviada e será mostrado um aviso de sucesso para o usuário.
        if(count($mensagem_erro) == 0){
            // PHPMailer
            $mail->addAddress('Um email que vai receber as mensagens, pode ser qualquer um');
            $mail->Subject = "$_POST[assunto]";
            $mail->Body = "<h4>Nome:</h4> <p>$_POST[nome]</p>
            <h4>Email:</h4> <p>$_POST[email]</p>
            <h4>Mensagem:</h4>
            <p>$_POST[mensagem]</p>";

            if(!$mail->Send()){
                echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
            }

            $mensagem_sucesso[] = "Mensagem enviada com sucesso";

            // Atualizando a página em 4 segundos.
            echo "<meta HTTP-EQUIV='refresh' CONTENT='4;URL=faleconosco.php'>";
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

    <title>Fale conosco - SatuctGames</title>
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

    <!-- FALE CONOSCO -->
    <div class="container-fluid mt-5 py-5">
        <div class="row">
            <div class="col-12">
                <div class="row mb-4 py-3" style="-ms-flex-wrap: nowrap !important; flex-wrap: nowrap !important;">
                    <div class="col-12">
                        <span class="text-preto ml-2 pl-2 border-left-titulo-preto" style="font-size: 15pt;">FALE CONOSCO</span>
                    </div>
                </div>

                <div class="container-fluid pt-4">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8 col-sm-9 col-11 mx-auto">
                            <form method="POST" autocomplete="off">
                                <div class="form-group">
                                    <label for="nome-faleconosco" class="text-cinza-2"><i class="fas fa-user pr-2"></i>Nome</label>
                                    <input type="text" class="form-control form-control-alt-2" maxlength="100" placeholder="ex: Roberto Silva" id="nome-faleconosco" name="nome">
                                </div>

                                <div class="form-group">
                                    <label for="email-faleconosco" class="text-cinza-2"><i class="fas fa-envelope pr-2"></i>E-mail</label>
                                    <input type="text" class="form-control form-control-alt-2" maxlength="100" placeholder="ex: satuctgames@hotmail.com" id="email-faleconosco" name="email">
                                </div>

                                <div class="form-group">
                                    <label for="assunto-faleconosco" class="text-cinza-2"><i class="fas fa-pen pr-2"></i>Assunto</label>
                                    <input type="text" class="form-control form-control-alt-2" maxlength="100" placeholder="Digite o assunto da mensagem" id="assunto-faleconosco" name="assunto">
                                </div>

                                <div class="form-group">
                                    <label for="mensagem-falenocosco" class="text-cinza-2"><i class="fas fa-file-alt pr-2"></i>Mensagem</label>
                                    <textarea class="form-control form-control-alt-2" id="mensagem-faleconosco" maxlength="400" rows="5" name="mensagem" placeholder="Digite sua mensagem"></textarea>
                                </div>

                                <div class="btn-group w-100 mt-4">
                                    <button type="submit" class="btn btn-cinza-form w-100 py-2" name="enviar"><i class="fas fa-arrow-alt-circle-up pr-2"></i>Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
            
                    <div claass="row">
                        <div class="col-xl-6 col-lg-7 col-md-8 col-sm-9 col-11 mx-auto mt-5">
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
    <!-- // FALE CONOSCO -->

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