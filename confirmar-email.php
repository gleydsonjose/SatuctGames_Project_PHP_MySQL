<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se alguém estiver logado, será redirecionado.
    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador']) || isset($_SESSION['id_usuario'])){
        header('location: index.php');
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
    <link rel="icon" href="imagens/satuctgames-favicon.png">
    <link rel="stylesheet" href="css/estilo.css?<?= time() ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- // BOOTSTRAP CSS -->

    <title>Confirmar Email - SatuctGames</title>
</head>
<body class="bg-preto text-center pt-5">
    <?php
        // Script com métodos para várias coisas.
        require_once 'scripts/dados.php';
        $cadastro = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

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

        // Verificando se existe alguns dados sendo passado pela url, se tiver, eles serão descriptografados, decodificados da forma json, para depois ser passado para o método de cadastro, assim um email será enviado para o usuário para confirmar o email para finalizar o cadastro. Além disso as sessões existentes serão removidas(nome, sobrenome, email), depois de tudo isso o usuário será redirecionado para a tela de login. Se não for o caso, uma mensagem de erro vai aparecer, e depois o usuário será redirecionado para a tela de cadastro.
        if(!empty($_GET)){
            include 'scripts/utilitarios.php';

            foreach($_GET as $chave=>$valor){
                $dados_descriptografado = openssl_decrypt($chave, 'aes-256-ctr', $key_ce, 0, $iv_ce);
            }

            $dados = json_decode($dados_descriptografado);

            // Se o código decodificado(JSON) retornar nulo, for diferente de 4 ou estiver vazio, o usuário será redirecionado para a loja, isso vai evitar alguém tentar alterar o código no link e tentar afetar os dados.
            if(empty($dados) || count($dados) != 4 || $dados == null){
                header('location: index.php');
            }

            // Verificando se o email recebido já existe no banco de dados, se sim o usuário será rediorecionado para a tela de login, se não, ele será cadastrado
            if($cadastro->VerificarEmail($dados[2])){
                echo "<span class='text-danger'>Este usuário já foi cadastrado.</span>";
                echo "<meta HTTP-EQUIV='refresh' CONTENT='4;URL=login.php'>";
            }else{
                if($cadastro->Cadastrar($dados[0], $dados[1], $dados[2], $dados[3])){
                    echo "<span class='text-success'>Bem vindo $dados[0], você foi cadastrado com sucesso.</span>";
                    
                    // PHPMailer
                    $mail->addAddress($dados[2]);
                    $mail->Subject = "Bem vindo $dados[0]";
                    $mail->Body = "Olá $dados[0], Seja bem vindo ao SatuctGames.";
        
                    if(!$mail->Send()){
                        echo 'Erro ao tentar enviar o email:' . $mail->ErrorInfo;
                    }
        
                    // Removendo sessões
                    unset($_SESSION['nome'], $_SESSION['sobrenome'], $_SESSION['email']);
        
                    echo "<meta HTTP-EQUIV='refresh' CONTENT='4;URL=login.php'>";
                }
            }
        }else{
            echo "<span class='text-danger'>Houve uma falha ao tentar confirmar o email, tente cadastrar novamente para re-enviar a confirmação de email.</span>";

            echo "<meta HTTP-EQUIV='refresh' CONTENT='5;URL=cadastro.php'>";
        }
    ?>

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->
</body>
</html>