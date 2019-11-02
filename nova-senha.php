<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se alguém estiver logado, será redirecionado
    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador']) || isset($_SESSION['id_usuario'])){
        header('location: index.php');
    }

    // Verificando se existe algum email sendo passado pela url, se tiver, ele será descriptografado, decodificado da forma json, e assim verificando se esse email existe no banco de dados, se não existe o usuário será redirecionado para a página de 'esqueci a senha', caso não existir nenhum email na url, acontecerá o mesmo.
    if(!empty($_GET)){
        // Script com key e iv para criptografia e descriptografia openssl.
        include 'scripts/utilitarios.php';

        foreach($_GET as $chave=>$valor){
            $email_descriptografado = openssl_decrypt($chave, 'aes-256-ctr', $key_ns, 0, $iv_ns);
        }
        $email_decodificado = json_decode($email_descriptografado);

        // Se o código decodificado(JSON) retornar nulo, for diferente de 1 ou estiver vazio, o usuário será redirecionado para a página 'esqueci a senha', isso vai evitar alguém tentar alterar o código no link e tentar afetar os dados recebidos.
        if(empty($email_decodificado) || count($email_decodificado) != 1 || $email_decodificado == null){
            header('location: esqueci-a-senha.php');
        }

        // Script com métodos para várias coisas.
        require_once 'scripts/dados.php';
        $novasenha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

        // Verificando se o email recebido não existe no banco de dados, se for o caso, o usuário será redirecionado para a página 'esqueci a senha'.
        if(!$novasenha->VerificarEmail($email_decodificado[0])){
            header('location: esqueci-a-senha.php');
        }

        // Se a permissão para alterar a senha estiver desativada, o usuário será redirecionado para a página 'esqueci-a-senha.php'.
        if($novasenha->VerPermissaoNovaSenha($email_decodificado[0])[0] != 1){
            header('location: esqueci-a-senha.php');
        }
    }else{
        header('location: esqueci-a-senha.php');
    }

    // Script com métodos para várias coisas.
    $mudar_senha = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

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

    // Se o botão 'mudar-senha' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mudar-senha'])){

        // Armazenando dados recebidos para variáveis.
        $email = addslashes(strip_tags(trim($email_decodificado[0])));
        $nova_senha = addslashes(strip_tags(trim($_POST['nova-senha'])));
        $repita_nova_senha = addslashes(strip_tags(trim($_POST['repita-nova-senha'])));
        
        // Verificando se cada campo está vazio.
        if(strlen($nova_senha) == 0){
            $mensagem_erro[] = "Preencha o campo Nova senha.";
        }

        if(strlen($repita_nova_senha) == 0){
            $mensagem_erro[] = "Preencha o campo Repita nova senha.";
        }

        // Verificando se a senha está entre 8 e 24 caracteres.
        if(strlen($nova_senha) < 8 || strlen($nova_senha) > 24){
            $mensagem_erro[] = "Preencha o campo da senha corretamente.";
        }

        // Verificando se a senha tem pelo menos uma letra maiúscula.
        if(!preg_match("/[A-Z]/", $nova_senha)){
            $mensagem_erro[] = "A senha deve conter pelo menos 1 letra maiúscula.";
        }

        // Verificando se a senha tem pelo menos uma letra minúscula.
        if(!preg_match("/[a-z]/", $nova_senha)){
            $mensagem_erro[] = "A senha deve conter pelo menos 1 letra minúscula.";
        }

        // Verificando se a senha tem pelo menos um caractere especial.
        if(!preg_match("/\W/", $nova_senha)){
            $mensagem_erro[] = "A senha deve conter pelo menos 1 caractere especial, ex: +-*@.";
        }

        // Verificando se a senha não tem nenhum espaço em branco.
        if(preg_match("/\s/", $nova_senha)){
            $mensagem_erro[] = "A senha não pode conter nenhum espaço em branco";
        }

        // Verificando se as senhas são iguais.
        if(strcmp($nova_senha, $repita_nova_senha) != 0){
            $mensagem_erro[] = "As senhas não correspondem.";
        }

        // Se não houver mensagens de erro.
        if(count($mensagem_erro) == 0){
            // Mudando a senha do usuário a partir do email recebido, enviando uma mensagem para o email do usuário, removendo sessões key e iv, redirecionando o usuário para a tela de login.
            if($mudar_senha->MudarSenhaPorEmail($email, $nova_senha)){
                // PHPMailer
                $mail->addAddress($email);
                $mail->Subject = "Senha alterada";
                $mail->Body = "Sua senha foi alterada com sucesso";

                if(!$mail->Send()){
                    echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                }

                $mensagem_sucesso[] = "Senha alterada com sucesso";

                // Depois de alterar a senha, a permissão de alterar novamente foi desativada, para evitar o usuário voltar pelo mesmo link e alterar a senha quando quiser por esse link
                $novasenha->AlterandoPermissaoNovaSenha($email, 0);

                echo "<meta HTTP-EQUIV='refresh' CONTENT='4;URL=login.php'>";
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

    <title>Nova senha - SatuctGames</title>
</head>
<body class="bg-cinza">

    <!-- Nova senha -->
    <div class="container-fluid mt-3 pt-4">
        <div class="row">
            <div class="col-12 text-center mb-1">
                <a href="index.php">
                    <img src="imagens/satuctgames-logo.png?<?= time() ?>" alt="Logotipo do site SatuctGames" class="img-fluid logotipo">
                </a>
            </div>

            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-9 col-11 mx-auto">
                <form method="POST">
                    <div class="form-group mt-5 text-left">
                        <label for="nova-senha" class="text-cinza-2"><i class="fas fa-lock pr-2"></i>Nova senha</label>
                        <input type="password" class="form-control form-control-alt-2" id="nova-senha" name="nova-senha" maxlength="24">
                        <small class="form-text text-cinza-2">A senha deve conter entre 8 e 24 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="repita-nova-senha" class="text-cinza-2"><i class="fas fa-key pr-2"></i>Repita a nova senha</label>
                        <input type="password" class="form-control form-control-alt-2" id="repita-nova-senha" name="repita-nova-senha" maxlength="24">
                    </div>

                    <div class="btn-group w-100 mt-3">
                        <button type="submit" class="btn btn-cinza-form w-100 py-2" name="mudar-senha"><i class="fas fa-unlock-alt pr-2"></i>Mudar senha</button>
                    </div>
                </form>
            </div>
        </div>

        <div claass="row">
            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-9 col-11 mx-auto mt-5">
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
    <!-- // Nova senha -->
    
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->
</body>
</html>