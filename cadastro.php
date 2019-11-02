<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se alguém estiver logado, será redirecionado.
    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador']) || isset($_SESSION['id_usuario'])){
        header('location: index.php');
    }
    
    // Script com métodos para várias coisas.
    require_once 'scripts/dados.php'; 

    $cadastro = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
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

    // Se o botão cadastrar for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])){
        // Dados recebidos serão armazenados em sessões e variáveis
        $_SESSION['nome'] = addslashes(strip_tags(trim($_POST['nome'])));
        $_SESSION['sobrenome'] = addslashes(strip_tags(trim($_POST['sobrenome'])));
        $_SESSION['email'] = addslashes(strip_tags(trim($_POST['email'])));
        $senha = addslashes(strip_tags(trim($_POST['senha'])));
        $repita_senha = addslashes(strip_tags(trim($_POST['repita-senha'])));

        // Um array com os dados do usuário.
        $dados = array($_SESSION['nome'], $_SESSION['sobrenome'], $_SESSION['email'], $senha);

        // Chamando o script com a key e iv para criptografia, criptografando os dados e codificando em json.
        include 'scripts/utilitarios.php';
        $dados_criptografado = openssl_encrypt(json_encode($dados), 'aes-256-ctr', $key_ce, 0, $iv_ce);

        // Codificando em formato url os dados criptografados.
        $dados_url = urlencode($dados_criptografado);
        
        // Verificando se cada campo está vazio.
        if(strlen($_SESSION['nome']) == 0){
            $mensagem_erro[] = "Preencha o campo Nome.";
        }

        if(strlen($_SESSION['sobrenome']) == 0){
            $mensagem_erro[] = "Preencha o campo Sobrenome.";
        }

        if(strlen($_SESSION['email']) == 0){
            $mensagem_erro[] = "Preencha o campo E-mail.";
        }

        if(strlen($senha) == 0){
            $mensagem_erro[] = "Preencha o campo Senha.";
        }

        if(strlen($repita_senha) == 0){
            $mensagem_erro[] = "Preencha o campo Repita Senha.";
        }

        // Verificando se o email é válido.
        if(!filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)){
            $mensagem_erro[] = "Digite um email válido.";
        }

        // Verificando se a senha está entre 8 e 24 caracteres.
        if(strlen($senha) < 8 || strlen($senha) > 24){
            $mensagem_erro[] = "A senha deve conter entre 8 e 24 caracteres";
        }

        // Verificando se a senha tem pelo menos uma letra maiúscula.
        if(!preg_match("/[A-Z]/", $senha)){
            $mensagem_erro[] = "A senha deve conter pelo menos 1 letra maiúscula.";
        }

        // Verificando se a senha tem pelo menos uma letra minúscula.
        if(!preg_match("/[a-z]/", $senha)){
            $mensagem_erro[] = "A senha deve conter pelo menos 1 letra minúscula.";
        }

        // Verificando se a senha tem pelo menos um caractere especial.
        if(!preg_match("/\W/", $senha)){
            $mensagem_erro[] = "A senha deve conter pelo menos 1 caractere especial, ex: +-*@.";
        }

        // Verificando se a senha não tem nenhum espaço em branco.
        if(preg_match("/\s/", $senha)){
            $mensagem_erro[] = "A senha não pode conter nenhum espaço em branco";
        }

        // Verificando se as senhas são iguais.
        if(strcmp($senha, $repita_senha) != 0){
            $mensagem_erro[] = "As senhas não correspondem.";
        }

        // Se não houver mensagens de erro.
        if(count($mensagem_erro) == 0){
            // Verificando se o email recebido já existe no banco de dados, se não o usuário receberá uma mensagem no seu email para confirmar o cadastro.
            if($cadastro->VerificarEmail($_SESSION['email'])){
                $mensagem_erro[] = "Já existe um usuário com este email";
            }else{
                // PHPMailer
                $mail->addAddress($_SESSION['email']);
                $mail->Subject = "Confirmar email";
                $mail->Body = "Olá $_SESSION[nome], confirme seu email para completar seu cadastro. <strong><a href='http://localhost/SG_Project/confirmar-email.php?$dados_url'>Confirmar</a></strong>";

                if(!$mail->Send()){
                    echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                }

                $mensagem_sucesso[] = "Cadastro quase completo, confirme seu email para completar";
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

    <title>Cadastro - SatuctGames</title>
</head>
<body class="bg-cinza">

    <!-- CADASTRO -->
    <div class="container-fluid mt-3 pt-4">
        <div class="row">
            <div class="col-12 text-center mb-1">
                <a href="index.php">
                    <img src="imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo">
                </a>
            </div>

            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-9 col-11 mx-auto">
                <form method="POST" role="form" autocomplete="off">
                    <div class="form-row mt-5">
                        <div class="form-group col-6">
                            <label for="nome" class="text-cinza-2"><i class="fas fa-user pr-2"></i>Nome</label>
                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: Gleydson" id="nome" name="nome" maxlength="30" value="<?php 
                            // Verificando se a sessão nome existe, se sim ela será mostrada.
                            if(isset($_SESSION['nome'])){ echo $_SESSION['nome']; } ?>">
                        </div>
                        <div class="form-group col-6">
                            <label for="sobrenome" class="text-cinza-2"><i class="far fa-user pr-2"></i>Sobrenome</label>
                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: José" id="sobrenome" name="sobrenome" maxlength="30" value="<?php 
                            // Verificando se a sessão sobrenome existe, se sim ela será mostrada.
                            if(isset($_SESSION['sobrenome'])){ echo $_SESSION['sobrenome']; } ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="text-cinza-2"><i class="fas fa-envelope pr-2"></i>E-mail</label>
                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: satuctgames@hotmail.com" id="email" name="email" maxlength="70" value="<?php 
                        // Verificando se a sessão email existe, se sim ela será mostrada.
                        if(isset($_SESSION['email'])){ echo $_SESSION['email']; } ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="senha" class="text-cinza-2"><i class="fas fa-lock pr-2"></i>Senha</label>
                            <input type="password" class="form-control form-control-alt-2" id="senha" name="senha" maxlength="24">
                            <small class="form-text text-cinza-2">A senha deve conter entre 8 e 24 caracteres</small>
                        </div>
                        <div class="form-group col-6">
                            <label for="repita-senha" class="text-cinza-2"><i class="fas fa-key pr-2"></i>Repita a senha</label>
                            <input type="password" class="form-control form-control-alt-2" id="repita-senha" name="repita-senha" maxlength="24">
                        </div>
                    </div>

                    <div class="btn-group w-100 mt-4">
                        <a href="login.php" class="btn btn-branco-form w-25 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                        <button type="submit" class="btn btn-cinza-form w-75 py-2 ml-2" name="cadastrar"><i class="fas fa-file-signature pr-2"></i>Cadastrar</button>
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
    <!-- // CADASTRO -->
    
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->
</body>
</html>