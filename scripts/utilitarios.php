<?php
    // key e iv para criptografar e descriptografar dados no openssl.

    // ce = confirmar email
    $key_ce = hash('sha256', 'b1b743a1d258d668664242c23ad37e60d1e83d1587da4b3e4f8a8602241c43e1');
    $iv_ce = hash('fnv1a64', '6a35d703868c481b');

    // ns = nova senha
    $key_ns = hash('sha256', 'b8236811574f3322660fcee17ad044911a35f49203de0e3304210e56b20ade39');
    $iv_ns = hash('fnv1a64', '100c95497dca4bf0');
?>