<?php
function verificarLogin($usuario, $senha) {
    $usuariosValidos = [
        "admin" => "admin123",
        "user" => "user123"
    ];
    return isset($usuariosValidos[$usuario]) && $usuariosValidos[$usuario] === $senha;
}
?>
