<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se alguém estiver logado, será redirecionado.
    if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador']) || isset($_SESSION['id_usuario'])){
        header('location: index.php');
    }
    
    // Script com métodos para várias coisas.
    require_once 'scripts/dados.php';
    $login = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Array para armazenar mensagens de erro.
    $mensagem_erro = [];

    // Se o botão 'enviar' for clicado, tudo abaixo será executado.
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['entrar'])){
        // Se não houver uma sessão iniciada, será iniciada uma nova.
        if(!isset($_SESSION)){
            session_start();
        }

        // Dados recebidos serão armazenados em variáveis.
        $email = addslashes(strip_tags(trim($_POST['email'])));
        $senha = addslashes(strip_tags(trim($_POST['senha'])));
        
        // Verificando se cada campo está vazio.
        if(strlen($email) == 0){
            $mensagem_erro[] = "Preencha o campo E-mail.";
        }

        if(strlen($senha) == 0){
            $mensagem_erro[] = "Preencha o campo Senha.";
        }

        // Se não houver mensagens de erro.
        if(count($mensagem_erro) == 0){
            // Verificando se o email recebido já existe no banco de dados, se existir e se o usuário tiver marcado a caixa de lembrar a senha, será criado um token e ele será armazenado no cookie do navegador do usuário, se por acaso o usuário não marcou a caixa, ou desmarcou e apertou em logar a qualquer momento, o cookie será removido, lembrando que a cada login que ele fizer com a caixa marcada um novo token será criado. Se o email recebido não existir, o usuário receberá uma mensagem de erro.
            if($login->Login($email, $senha)){
                if(isset($_POST['lembrar-senha'])){
                    $token = password_hash(random_int(1,1000), PASSWORD_ARGON2I);
                    $login->GuardarToken($email, $token);
                    setcookie('_sgpt', $token, time() + (86400*30), '/', '', '',  1);
                }else{
                    setcookie('_sgpt', null, time() + (86400*30), '/', '', '',  1);
                }

                // Redirecionando o usuário para a página inicial.
                echo "<script>location.href='index.php'</script>";
            }else{
                $mensagem_erro[] = "Email ou Senha está incorreto(a)";
            }
        }
    }

    // Se o cookie existir, 2 variavéis serão criadas e terão email e senha do usuário sendo armazenada para mostrar no campo de email e senha
    if(isset($_COOKIE['_sgpt'])){
        $email_user = $login->LembrarEmail($_COOKIE['_sgpt']);
        $senha_user = $login->LembrarSenha($_COOKIE['_sgpt']);
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

    <title>Login - SatuctGames</title>
</head>
<body class="bg-cinza">

    <!-- LOGIN -->
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
                        <input type="text" class="form-control form-control-alt-2" placeholder="Digite seu email" id="email-login" name="email" maxlength="70" value="<?php if(isset($_COOKIE['_sgpt'])){ echo $email_user; } ?>">
                    </div>

                    <div class="form-group text-left">
                        <label for="senha-login" class="text-cinza-2"><i class="fas fa-lock pr-2"></i>Senha</label>
                        <input type="password" class="form-control form-control-alt-2" placeholder="Digite sua senha" id="senha-login" name="senha" maxlength="24" value="<?php if(isset($_COOKIE['_sgpt'])){ echo $senha_user; } ?>">
                        <small class="form-text text-cinza-2">(É necessário ter os cookies ativo para lembrar a senha)</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 text-left">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="lembrar-senha" name="lembrar-senha" <?php if(isset($_COOKIE['_sgpt'])){ echo 'checked'; } ?>>
                                <label class="form-check-label text-preto" for="lembrar-senha">Lembrar senha</label>
                            </div>
                        </div>

                        <div class="form-group col-6 text-right">
                            <a href="esqueci-a-senha.php" class="btn-preto-esqueceu-senha" style="text-decoration: none;">Esqueci a senha</a>
                        </div>
                    </div>

                    <div class="btn-group w-100 mt-3">
                        <a href="cadastro.php" class="btn btn-branco-form w-50 py-2"><i class="fas fa-file-signature pr-2"></i>Criar conta</a>
                        <button type="submit" class="btn btn-cinza-form btn-block w-50 py-2 ml-2" name="entrar"><i class="fas fa-user pr-2"></i>Entrar</button>
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
            </div>
        </div>
    </div>
    <!-- // LOGIN -->
    
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->
</body>
</html>