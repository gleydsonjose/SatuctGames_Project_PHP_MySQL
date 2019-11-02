<?php
    // Script com métodos para várias coisas.
    require_once 'dados.php';
    $dados = new Dados("nome do banco de dados", "host", "nome de usuario", "senha");

    // Recebendo o nome do game pela barra de busca e procurando o game no banco de dados a partir desse nome.
    // Depois disso será retornado os dados na tela.
    if(isset($_POST['buscar'])) {
        $buscar_dados = $dados->BarraBuscaGame($_POST['buscar']);

        if(isset($buscar_dados)){
            foreach($buscar_dados as $bd){ ?>
                <li class="nav-item"><a href="<?= $bd['nome_url_pagina'] ?>.php" class="text-preto nav-link py-1"><?= $bd['nome_game'] ?></a></li>
      <?php } 
        }
    }
?>