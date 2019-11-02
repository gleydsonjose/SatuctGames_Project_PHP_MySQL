<?php
    // Se não houver uma sessão iniciada, será iniciada uma nova.
    if(!isset($_SESSION)){
        session_start();
    }

    // Removendo sessões de login.
    unset($_SESSION['id_administrador']);
    unset($_SESSION['id_moderador']);
    unset($_SESSION['id_usuario']);

    // Redirecionado para a página inicial.
    echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=index.php'>";
?>