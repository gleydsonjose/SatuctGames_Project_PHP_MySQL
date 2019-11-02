<?php
    // Esse script contém sessões de login, ex: adm, moderador, usuário.
    include 'sessao_login.php';

    // Script com métodos para várias coisas.
    require_once 'dados.php';
    $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Verificando se é um administrador ou moderador logado
        if(isset($_SESSION['id_administrador']) || isset($_SESSION['id_moderador'])){

            // Excluindo a avaliação pelo id da avaliação recebido
            if($dados->ExcluirAvaliacao($_POST['id_avaliacao'])){
                header('location: ../games/'.$_POST['nome_game'].'.php');
            }
        }
    }
?>