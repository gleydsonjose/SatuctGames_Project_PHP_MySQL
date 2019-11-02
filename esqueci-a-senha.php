<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se alguém estiver logado, será redirecionado.
    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador']) || isset($_SESSION['id_usuario'])){
        header('location: index.php');
    }

    // Script com métodos para várias coisas.
    require_once 'scripts/dados.php';
    $esqueci_a_senha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Arrays para armazenar mensagens de erro e sucesso.
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

    // Se o botão 'enviar' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar'])){
        // O email recebido será armazenado em uma variável
        $email = addslashes(strip_tags(trim($_POST['email'])));
        
        // Verificando se o campo email está vazio
        if(strlen($email) == 0){
            $mensagem_erro[] = "Preencha o campo E-mail.";
        }

        // Se não houver mensagens de erro
        if(count($mensagem_erro) == 0){
            // Verificando se o email recebido já existe no banco de dados, se não o usuário vai receber uma mensagem de erro
            if($esqueci_a_senha->VerificarEmail($email)){
                // Um array com o email do usuário
                $email_array = array($email);

                // Chamando o script com a key e iv para criptografia, criptografando os dados e codificando em json
                include 'scripts/utilitarios.php';
                $email_criptografado = openssl_encrypt(json_encode($email_array), 'aes-256-ctr', $key_ns, 0, $iv_ns);
                $email_url = urlencode($email_criptografado);

                // PHPMailer
                $mail->addAddress($email);
                $mail->Subject = "Alterar senha";
                $mail->Body = "Clique no link para você criar uma nova senha. <strong><a href='http://localhost/SG_Project/nova-senha.php?$email_url'>Alterar senha</a></strong>";

                if(!$mail->Send()){
                    echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                }

                // Mnesagem de sucesso para mostrar ao usuário um aviso.
                $mensagem_sucesso[] = "Foi enviado um link para seu email, clique nele para mudar sua senha";

                // Depois de verificar se o email existe, a permissão de alterar novamente será ativada
                $esqueci_a_senha->AlterandoPermissaoNovaSenha($email, 1);
            }else{
                $mensagem_erro[] = "Este email não está cadastrado";
            }
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
    <link rel="stylesheet" href="css/estilo.css?<?= time() ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- // BOOTSTRAP CSS -->

    <title>Esqueci a senha - SatuctGames</title>
</head>
<body class="bg-cinza">

    <!-- Esqueci a senha -->
    <div class="container-fluid mt-3 pt-4">
        <div class="row">
            <div class="col-12 text-center mb-1">
                <a href="index.php">
                    <img src="imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo">
                </a>
            </div>

            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-9 col-11 mx-auto">
                <form method="POST" autocomplete="off">
                    <div class="form-group mt-5 text-left">
                        <label for="email-login" class="text-cinza-2"><i class="fas fa-envelope pr-2"></i>E-mail</label>
                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: satuctgames@hotmail.com" id="email-login" name="email" maxlength="70">
                        <small class="form-text text-cinza-2">Escreva seu email para receber um link  para alterar sua senha</small>
                    </div>

                    <div class="btn-group w-100 mt-3">
                        <a href="login.php" class="btn btn-branco-form w-25 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                        <button type="submit" class="btn btn-cinza-form w-75 py-2 ml-2" name="enviar"><i class="fas fa-arrow-alt-circle-up pr-2"></i>Enviar</button>
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
    <!-- // Esqueci a senha -->
    
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->
</body>
</html>