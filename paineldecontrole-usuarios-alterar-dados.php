<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'scripts/sessao_login.php';

    // Se a sessão atual não for de um administrador ou moderador, o usuário será redirecionado para a página inicial.
    if(!isset($_SESSION['id_administrador']) && !isset($_SESSION['id_moderador'])){
        header('location: index.php');
    }

    // Classe com métodos para várias coisas.
    $alterar_dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Arrays para armazenar mensagens de erro e sucesso.
    $mensagem_erro = [];
    $mensagem_sucesso = [];

    // Pegando o arquivo json e decodificando ele em array associativo.
    $estados = json_decode(file_get_contents('scripts/estados.json'), true);

    // Se existir a sessão id_usuario.
    if(isset($_SESSION['usuario_id'])){
        // Buscando os dados do usuário pelo seu id_usuario.
        $usuario = $dados->BuscarDadosUsuarios($_SESSION['usuario_id']);

        // Se o botão 'alterar-dados' for clicado, tudo abaixo será executado.
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar-dados'])){
            // Dados recebidos serão armazenados em variáveis e sessões.
            $nome = addslashes(strip_tags(trim($_POST['nome'])));
            $sobrenome = addslashes(strip_tags(trim($_POST['sobrenome'])));
            $email = addslashes(strip_tags(trim($_POST['email'])));
            $_SESSION['cep'] = addslashes(strip_tags(trim($_POST['cep'])));
            $_SESSION['cidade'] = addslashes(strip_tags(trim($_POST['cidade'])));
            $_SESSION['endereco'] = addslashes(strip_tags(trim($_POST['endereco'])));
            $_SESSION['numero'] = addslashes(strip_tags(trim($_POST['numero'])));
            $_SESSION['bairro'] = addslashes(strip_tags(trim($_POST['bairro'])));
            $_SESSION['complemento'] = addslashes(strip_tags(trim($_POST['complemento'])));
            $_SESSION['telefone'] = addslashes(strip_tags(trim($_POST['telefone'])));
            $estado_escolhido = addslashes(strip_tags(trim($_POST['estado'])));
            
            // Verificando se cada campo está vazio.
            if(strlen($nome) == 0){
                $mensagem_erro[] = "Preencha o campo Nome.";
            }

            if(strlen($sobrenome) == 0){
                $mensagem_erro[] = "Preencha o campo Sobrenome.";
            }

            if(strlen($email) == 0){
                $mensagem_erro[] = "Preencha o campo E-mail.";
            }

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

            //  Verificando se o campo não tem números digitados.
            if(preg_match("/[^0-9]/", $_SESSION['telefone'])){
                $mensagem_erro[] = "Digite apenas números no campo Telefone";
            }

            // Separando os arrays da variável estados que tem 3 arrays, separando para 1 apenas($todos_estados).
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

            // Verificando se o email é válido.
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mensagem_erro[] = "Digite um email válido.";
            }

            // Se não houver nenhum erro, os dados serão alterados, uma mensagem de sucesso será mostrada e as sessões abertas para armazenar dados recebidos serão removidas.
            if(count($mensagem_erro) == 0){
                if($alterar_dados->AlterarDados($usuario['id'], $nome, $sobrenome, $email, $_SESSION['cep'], $_SESSION['cidade'], $_SESSION['endereco'], $_SESSION['numero'], $_SESSION['bairro'], $_SESSION['complemento'], $estado_escolhido, $_SESSION['telefone'])){

                    unset($_SESSION['cep'], $_SESSION['cidade'], $_SESSION['endereco'], $_SESSION['numero'], $_SESSION['bairro'], $_SESSION['complemento'], $_SESSION['telefone'], $_SESSION['usuario_id']);

                    $mensagem_sucesso[] = "Dados alterados com sucesso";

                    // Redirecionando
                    echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=paineldecontrole-usuarios-alterar-dados.php'>";
                }else{
                    $mensagem_erro[] = "Já existe um usuário com este email";
                }
            }
        }
    }else{
        // Redirecionando
        header('location: paineldecontrole-usuarios.php');
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

    <title>Painel de Controle Usuários - SatuctGames</title>
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
                <h5 class="text-preto">Usuários</h5>
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

    <!-- PAINEL DE CONTROLE USUÁRIOS - ALTERAR DADOS -->
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
                                <form method="POST" autocomplete="off">
                                    <div class="form-row mt-3">
                                        <div class="form-group col-6">
                                            <label for="nome-alterar-dados" class="text-cinza-2"><i class="fas fa-user pr-2"></i>Nome</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: Gleydson" id="nome-alterar-dados" name="nome" <?php
                                            // Se existe o nome do usuário no banco de dados, será adicionado um value com esse nome.
                                            if(isset($usuario['nome'])){ echo "value='$usuario[nome]'"; } ?>>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="sobrenome-alterar-dados" class="text-cinza-2"><i class="far fa-user pr-2"></i>Sobrenome</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: José" id="sobrenome-alterar-dados" name="sobrenome" <?php
                                            // Se existe o sobrenome do usuário no banco de dados, será adicionado um value com esse sobrenome.
                                            if(isset($usuario['sobrenome'])){ echo "value='$usuario[sobrenome]'"; } ?>>
                                        </div>
                                    </div>
                
                                    <div class="form-group">
                                        <label for="email-alterar-dados" class="text-cinza-2"><i class="fas fa-envelope pr-2"></i>E-mail</label>
                                        <input type="text" class="form-control form-control-alt-2" placeholder="ex: satuctgames@hotmail.com" id="email-alterar-dados" name="email" <?php
                                        // Se existe o email do usuário no banco de dados, será adicionado um value com esse email.
                                        if(isset($usuario['email'])){ echo "value='$usuario[email]'"; } ?>>
                                    </div>
                
                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <label for="cep-alterar-dados" class="text-cinza-2"><i class="fas fa-hotel pr-2"></i>CEP</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: 00000-000" id="cep-alterar-dados" name="cep" <?php
                                            // Se existe o cep do usuário no banco de dados e esse cep for diferente de 0, será adicionado um value com o cep do usuário.
                                            if(isset($usuario['cep']) && $usuario['cep'] != 0){
                                                echo "value='$usuario[cep]'";
                                            }
                                            
                                            // Se existe a sessão 'cep' e o cep do banco de dados for igual a 0, será adicionado um value com o valor da sessão cep.
                                            if(isset($_SESSION['cep']) && $usuario['cep'] == 0){
                                                echo "value='$_SESSION[cep]'";
                                            }
                                            ?>>
                                            <small class="form-text text-danger d-none" id="aviso-erro-cep">Esse cep não existe</small>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="cidade-alterar-dados" class="text-cinza-2"><i class="fas fa-city pr-2"></i>Cidade</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: São Paulo" id="cidade-alterar-dados" name="cidade" <?php
                                            // Se existe a cidade do usuário no banco de dados e essa cidade cadastrada não for vazia, será adicionado um value com essa cidade.
                                            if(isset($usuario['cidade']) && !empty($usuario['cidade'])){
                                                echo "value='$usuario[cidade]'";
                                            }
                                            
                                            // Se existe uma sessão 'cidade' e o usuário não tiver uma cidade cadastrada, será adicionado um value com o valor da sessão 'cidade'.
                                            if(isset($_SESSION['cidade']) && empty($usuario['cidade'])){
                                                echo "value='$_SESSION[cidade]'";
                                            }
                                            ?>>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <label for="endereco-alterar-dados" class="text-cinza-2"><i class="fas fa-map-marked-alt pr-2"></i>Endereço</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: Rua dos Rouxinóis Amarelos" id="endereco-alterar-dados" name="endereco" <?php
                                            // Se existe o endereço do usuário no banco de dados e esse endereço cadastrado não for vazio, será adicionado um value com esse endereço.
                                            if(isset($usuario['endereco']) && !empty($usuario['endereco'])){
                                                echo "value='$usuario[endereco]'";
                                            }

                                            // Se existe uma sessão 'endereco' e o usuário não tiver um endereço cadastrado, será adicionado um value com o valor da sessão 'endereco'.
                                            if(isset($_SESSION['endereco']) && empty($usuario['endereco'])){
                                                echo "value='$_SESSION[endereco]'";
                                            }
                                            ?>>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="numero-alterar-dados" class="text-cinza-2"><i class="fas fa-home pr-2"></i>Número<small class="text-cinza-2"> (número da casa)</small></label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: 100" id="numero-alterar-dados" name="numero" <?php
                                            // Se existe o número da casa do usuário no banco de dados e esse número for diferente de 0, será adicionado um value com o número do usuário.
                                            if(isset($usuario['numero']) && $usuario['numero'] != 0){
                                                echo "value='$usuario[numero]'";
                                            }
                                            
                                            // Se existe uma sessão 'numero' e o número da sua casa for igual a 0, será adicionado um value com o valor da sessão 'numero'.
                                            if(isset($_SESSION['numero']) && $usuario['numero'] == 0){
                                                echo "value='$_SESSION[numero]'";
                                            }
                                            ?>>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <label for="bairro-alterar-dados" class="text-cinza-2"><i class="fas fa-users pr-2"></i>Bairro</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: Vila Ayrosa" id="bairro-alterar-dados" name="bairro" <?php
                                            // Se existe o bairro do usuário no banco de dados e esse bairro cadastrado não for vazio, será adicionado um value com esse bairro.
                                            if(isset($usuario['bairro']) && !empty($usuario['bairro'])){
                                                echo "value='$usuario[bairro]'";
                                            }

                                            // Se existe uma sessão 'bairro' e o bairro no banco de dados estiver vázio, será adicionado um value com o valor da sessão 'bairro'.
                                            if(isset($_SESSION['bairro']) && empty($usuario['bairro'])){
                                                echo "value='$_SESSION[bairro]'";
                                            }
                                            ?>>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="complemento-alterar-dados" class="text-cinza-2"><i class="fas fa-building pr-2"></i>Complemento</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: Casa, Apartamento, Condomínio..." id="complemento-alterar-dados" name="complemento" <?php 
                                            // Se existe o complemento do usuário no banco de dados e esse complemento cadastrado não for vazio, será adicionado um value com esse complemento.
                                            if(isset($usuario['complemento']) && !empty($usuario['complemento'])){
                                                echo "value='$usuario[complemento]'";
                                            }
                                            
                                            // Se existe uma sessão 'complemento' e o complemento no banco de dados estiver vázio, será adicionado um value com o valor da sessão 'complemento'.
                                            if(isset($_SESSION['complemento']) && empty($usuario['complemento'])){
                                                echo "value='$_SESSION[complemento]'";
                                            }
                                            ?>>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-6">
                                            <label for="estado-alterar-dados" class="text-cinza-2"><i class="fas fa-flag pr-2"></i>Estado</label>
                                            <select class="form-control form-control-alt-2" id="estado-alterar-dados" name="estado">
                                                <?php
                                                    // Se existe o estado do usuário no banco de dados, será adicionado um option principal com esse estado. Se não, será mostrado um 'Escolha o seu estado' como option principal, com valor 0.
                                                    if(isset($usuario['estado']) && !empty($usuario['estado'])){ ?>
                                                        <option value="<?= $usuario['estado'] ?>" selected><?= $usuario['estado'] ?></option>
                                              <?php }else{ ?>
                                                        <option value="0" selected>Escolha o seu estado</option>
                                              <?php }
                                                ?>
                                                <?php
                                                    // Separando todos nomes de estados para mostrar no select
                                                    foreach($estados as $estado){
                                                        for($id_estado = 0; $id_estado < count($estado); $id_estado++){
                                                            $mostrar_estado = $estado[$id_estado];
                                                            if(isset($usuario['estado']) && !empty($usuario['estado'])){
                                                                // Se o usuário já tiver um 'estado' cadastrado, esse estado não será mostrado na lista de estados;
                                                                if($usuario['estado'] == $mostrar_estado['nome']){
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
                                            <label for="telefone-alterar-dados" class="text-cinza-2"><i class="fas fa-phone pr-2"></i>Telefone</label>
                                            <input type="text" class="form-control form-control-alt-2" placeholder="ex: 81900000000" id="telefone-alterar-dados" name="telefone" <?php
                                            // Se existe o telefone do usuário no banco de dados e esse telefone for diferente de 0, será adicionado um value com o telefone do usuário.
                                            if(isset($usuario['telefone']) && $usuario['telefone'] != 0){
                                                echo "value='$usuario[telefone]'";
                                            }

                                            // Se existe uma sessão 'telefone' e o telefone do usuário for igual a 0, será adicionado um value com o valor da sessão 'telefone'.
                                            if(isset($_SESSION['telefone']) && $usuario['telefone'] == 0){
                                                echo "value='$_SESSION[telefone]'";
                                            }
                                            ?>>
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <img src="imagens/loader.gif" alt="GIF de carregamento" id="carregamento-dados" class="mt-3 mx-auto d-none">
                                    </div>

                                    <div class="btn-group w-100 mt-4">
                                        <a href="paineldecontrole-usuarios.php" class="btn btn-azul-form w-25 py-2"><i class="fas fa-caret-square-left pr-2"></i>Voltar</a>
                                        <button type="submit" class="btn btn-cinza-form w-75 py-2 ml-2" name="alterar-dados" id="alterar-dados"><i class="fas fa-edit pr-2"></i>Alterar dados</button>
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
    </div>
    <!-- // PAINEL DE CONTROLE USUÁRIOS - ALTERAR DADOS -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- // BOOTSTRAP SCRIPTS -->

    <script src="js/script.js?<?= time() ?>"></script>
</body>
</html>