
<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';

$nomes = json_decode(file_get_contents('nome.json'), true);
$emails = json_decode(file_get_contents('email.json'), true);
$senhas = json_decode(file_get_contents('senha.json'), true);

$tipos = file_exists('tipoConta.json')
    ? json_decode(file_get_contents('tipoConta.json'), true)
    : [];

$nomes = is_array($nomes) ? $nomes : [];
$emails = is_array($emails) ? $emails : [];
$senhas = is_array($senhas) ? $senhas : [];
$tipos = is_array($tipos) ? $tipos : [];

// Procura o email cadastrado
$indice = array_search(
    $email,
    array_map('strtolower', $emails),
    true
);

$senhaValida = false;

if ($indice !== false && isset($senhas[$indice])) {

    $senhaArmazenada = $senhas[$indice];

    // Verifica senha criptografada ou senha antiga em texto puro
    if (password_verify($senha, $senhaArmazenada)) {

        $senhaValida = true;

    } elseif (hash_equals(
        (string)$senhaArmazenada,
        (string)$senha
    )) {

        $senhaValida = true;

        // Atualiza senhas antigas para hash
        $senhas[$indice] = password_hash(
            $senha,
            PASSWORD_DEFAULT
        );

        file_put_contents(
            'senha.json',
            json_encode(
                $senhas,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            LOCK_EX
        );
    }
}

// Caso o login seja inválido
if ($indice === false || !$senhaValida) {

    header('Location: index.php?erro=credenciais');
    exit;

}

// Recupera o tipo de conta
$tipo = $tipos[$indice] ?? 'trabalhador';

// Proteção contra tipos inválidos
if (!in_array($tipo, ['empresa', 'trabalhador'], true)) {
    $tipo = 'trabalhador';
}

// Cria a sessão
$_SESSION['usuario'] = $email;
$_SESSION['nome'] = $nomes[$indice] ?? '';
$_SESSION['tipo_conta'] = $tipo;

// Redirecionamento
if ($tipo === 'empresa') {

    header('Location: inicialEmpresa.php');

} else {

    header('Location: inicialTrabalhador.php');

}

exit;

?>
