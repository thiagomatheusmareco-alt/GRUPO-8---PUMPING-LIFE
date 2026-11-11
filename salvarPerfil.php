<?php

session_start();

if (!isset($_SESSION['usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$emails = file_exists('email.json') ? json_decode(file_get_contents('email.json'), true) : [];
$emails = is_array($emails) ? $emails : [];

$id = array_search($_SESSION['usuario'], $emails, true);

if ($id === false) {
    header('Location: index.php');
    exit;
}

$perfis = file_exists('perfil.json') ? json_decode(file_get_contents('perfil.json'), true) : [];
$perfis = is_array($perfis) ? $perfis : [];

$atual = $perfis[$id] ?? [
    'endereco' => '',
    'descricao' => '',
    'qualificacoes' => '',
    'genero' => '',
    'curriculo' => ''
];

$endereco = trim($_POST['endereco'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$qualificacoes = trim($_POST['qualificacoes'] ?? '');
$genero = $_POST['genero'] ?? '';

if (!in_array($genero, ['', 'Masculino', 'Feminino', 'Outro'], true)) {
    $genero = '';
}

$atual['endereco'] = mb_substr($endereco, 0, 200);
$atual['descricao'] = mb_substr($descricao, 0, 1000);
$atual['qualificacoes'] = mb_substr($qualificacoes, 0, 1000);
$atual['genero'] = $genero;

// Upload do currículo (opcional, apenas PDF, até 5MB)
if (!empty($_FILES['curriculo']['name']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK) {

    $tamanhoMax = 5 * 1024 * 1024;
    $extensaoValida = strtolower(pathinfo($_FILES['curriculo']['name'], PATHINFO_EXTENSION)) === 'pdf';
    $tipoValido = function_exists('mime_content_type')
        ? mime_content_type($_FILES['curriculo']['tmp_name']) === 'application/pdf'
        : true;

    if ($extensaoValida && $tipoValido && $_FILES['curriculo']['size'] <= $tamanhoMax) {

        $pastaDestino = 'uploads/curriculos/';

        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0755, true);
        }

        $nomeArquivo = 'curriculo_' . $id . '_' . time() . '.pdf';
        $caminhoDestino = $pastaDestino . $nomeArquivo;

        if (move_uploaded_file($_FILES['curriculo']['tmp_name'], $caminhoDestino)) {

            // Remove o currículo anterior, se existir
            if (!empty($atual['curriculo']) && is_file($atual['curriculo'])) {
                @unlink($atual['curriculo']);
            }

            $atual['curriculo'] = $caminhoDestino;
        }
    }
}

$perfis[$id] = $atual;

file_put_contents(
    'perfil.json',
    json_encode($perfis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);

header('Location: perfil.php?salvo=1');
exit;