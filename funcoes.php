
<?php

function lerJSON($arquivo) {
    if (!file_exists($arquivo)) {
        return [];
    }

    $dados = json_decode(file_get_contents($arquivo), true);

    return is_array($dados) ? $dados : [];
}

function salvarJSON($arquivo, $dados) {
    $json = json_encode(
        $dados,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    if ($json === false) {
        return false;
    }

    return file_put_contents(
        $arquivo,
        $json,
        LOCK_EX
    ) !== false;
}

function protegerTexto($texto) {
    return htmlspecialchars(
        (string)$texto,
        ENT_QUOTES,
        'UTF-8'
    );
}

function indiceUsuarioAtual() {
    $emails = lerJSON('email.json');

    return array_search(
        $_SESSION['usuario'] ?? '',
        $emails,
        true
    );
}

function exigirLogin($tipo = null) {

    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit;
    }

    if (
        $tipo !== null &&
        ($_SESSION['tipo_conta'] ?? '') !== $tipo
    ) {
        $destino = ($_SESSION['tipo_conta'] ?? '') === 'empresa'
            ? 'inicialEmpresa.php'
            : 'inicialTrabalhador.php';

        header('Location: ' . $destino);
        exit;
    }
}

?>
