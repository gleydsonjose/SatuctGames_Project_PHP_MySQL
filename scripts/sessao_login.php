<?php
    // Se não houver uma sessão iniciada, será iniciada uma nova
    if(!isset($_SESSION)){
        session_start();
    }
    
    // Script com métodos para várias coisas.
    require_once 'dados.php';

    // Verificando se a sessão atual é um usuário, moderador ou administrador
    if(isset($_SESSION['id_usuario'])){
        $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
        $info = $dados->BuscarDadosUsuarios($_SESSION['id_usuario']);
        $tag = 'Usuário';
    }elseif(isset($_SESSION['id_administrador'])){
        $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
        $info = $dados->BuscarDadosUsuarios($_SESSION['id_administrador']);
        $tag = 'Administrador';
    }elseif(isset($_SESSION['id_moderador'])){
        $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");
        $info = $dados->BuscarDadosUsuarios($_SESSION['id_moderador']);
        $tag = 'Moderador';
    }
?>