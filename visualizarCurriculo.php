
<?php

session_start();

require_once 'funcoes.php';

exigirLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$candidaturas = lerJSON('candidaturas.json');
$emails = lerJSON('email.json');

if ($id === false || $id === null || !isset($candidaturas[$id])) {
    die('Candidatura não encontrada.');
}

$c = $candidaturas[$id];

$idAtual = indiceUsuarioAtual();

$autorizado = false;

if (($_SESSION['tipo_conta'] ?? '') === 'trabalhador') {

    $autorizado = $idAtual == $c['trabalhador_id'];

} elseif (($_SESSION['tipo_conta'] ?? '') === 'empresa') {

    $autorizado = $idAtual == $c['empresa_id'];
}

if (!$autorizado) {
    die('Você não possui autorização para visualizar este currículo.');
}

$curriculo = $c['curriculo'] ?? [];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Visualizar currículo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="painel.css" rel="stylesheet">

</head>

<body class="painel">

<header class="topo text-center">
    <h2><b>PROJETO</b></h2>
</header>

<div class="layout">

    <?php include 'sidebar.php'; ?>

    <main class="conteudo-principal">

        <h1>Currículo recebido</h1>

        <div class="painel-card mt-4">

            <h3>
                <?php echo protegerTexto($curriculo['nome'] ?? ''); ?>
            </h3>

            <p>
                <strong>Empresa:</strong>
                <?php echo protegerTexto($c['nome_empresa']); ?>
            </p>

            <p>
                <strong>Vaga:</strong>
                <?php echo protegerTexto($c['titulo_vaga']); ?>
            </p>

            <hr>

            <h4>Endereço</h4>

            <p>
                <?php echo protegerTexto($curriculo['endereco'] ?? ''); ?>
            </p>

            <h4>Descrição</h4>

            <p>
                <?php echo nl2br(protegerTexto($curriculo['descricao'] ?? '')); ?>
            </p>

            <h4>Qualificações</h4>

            <p>
                <?php echo nl2br(protegerTexto($curriculo['qualificacoes'] ?? '')); ?>
            </p>

            <h4>Gênero</h4>

            <p>
                <?php echo protegerTexto($curriculo['genero'] ?? ''); ?>
            </p>

            <?php if (!empty($curriculo['arquivo']) && is_file($curriculo['arquivo'])): ?>

                <a href="<?php echo protegerTexto($curriculo['arquivo']); ?>"
                   target="_blank"
                   class="btn btn-dourado">

                    Abrir PDF

                </a>

            <?php endif; ?>

        </div>

        <?php if (($_SESSION['tipo_conta'] ?? '') === 'empresa'): ?>

            <a href="curriculosRecebidos.php"
               class="btn btn-outline-light mt-3">

                Voltar aos currículos recebidos

            </a>

        <?php else: ?>

            <a href="curriculosEnviados.php"
               class="btn btn-outline-light mt-3">

                Voltar aos currículos enviados

            </a>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
