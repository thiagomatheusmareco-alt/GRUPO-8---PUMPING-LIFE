
<?php
session_start();

require_once 'funcoes.php';

exigirLogin('trabalhador');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$nomes = lerJSON('nome.json');
$tipos = lerJSON('tipoConta.json');
$perfis = lerJSON('perfil.json');
$todasVagas = lerJSON('vagas.json');

if (
    $id === false ||
    $id === null ||
    !isset($nomes[$id]) ||
    !isset($tipos[$id]) ||
    $tipos[$id] !== 'empresa'
) {
    header('Location: inicialTrabalhador.php');
    exit;
}

$nomeEmpresa = $nomes[$id];
$perfilEmpresa = $perfis[$id] ?? [];

// Seleciona as vagas desta empresa que ainda estão ativas e no prazo.
// O índice original do JSON é preservado para o link de candidatura.
$vagasEmpresa = [];

foreach ($todasVagas as $idVaga => $vaga) {
    $dataExpiracao = $vaga['expiracao'] ?? '';

    if (
        (string)($vaga['empresa_id'] ?? '') === (string)$id &&
        ($vaga['status'] ?? '') === 'ativa' &&
        $dataExpiracao !== '' &&
        $dataExpiracao >= date('Y-m-d')
    ) {
        $vagasEmpresa[$idVaga] = $vaga;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Perfil da Empresa</title>

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

        <h1><?php echo protegerTexto($nomeEmpresa); ?></h1>

        <div class="painel-card mt-4">

            <h3>Sobre a empresa</h3>

            <p>
                <?php echo nl2br(protegerTexto(
                    $perfilEmpresa['descricao'] ?? 'Descrição não informada.'
                )); ?>
            </p>

            <h3 class="mt-4">Localização</h3>

            <p>
                <?php echo protegerTexto(
                    $perfilEmpresa['endereco'] ?? 'Não informada'
                ); ?>
            </p>

            <h3 class="mt-4">Informações adicionais</h3>

            <p>
                <?php echo nl2br(protegerTexto(
                    $perfilEmpresa['qualificacoes'] ?? 'Nenhuma informação adicional.'
                )); ?>
            </p>

        </div>

        <h3 class="mt-5 mb-3">Vagas disponíveis</h3>

        <?php if (empty($vagasEmpresa)): ?>

            <p class="vazio">
                Esta empresa ainda não possui vagas disponíveis.
            </p>

        <?php else: ?>

            <div class="row row-cols-1 row-cols-md-2 g-3">

                <?php foreach ($vagasEmpresa as $idVaga => $vaga): ?>

                    <div class="col">

                        <div class="card-listagem text-start">

                            <h4 class="nome-conta">
                                <?php echo protegerTexto($vaga['titulo']); ?>
                            </h4>

                            <p>
                                <?php echo nl2br(protegerTexto($vaga['descricao'])); ?>
                            </p>

                            <strong>Requisitos:</strong>

                            <p>
                                <?php echo nl2br(protegerTexto($vaga['requisitos'])); ?>
                            </p>

                            <p class="text-white-50">
                                Expira em:
                                <?php echo date(
                                    'd/m/Y',
                                    strtotime($vaga['expiracao'])
                                ); ?>
                            </p>

                            <a href="candidatar.php?id_vaga=<?php echo (int)$idVaga; ?>"
                               class="btn btn-dourado">

                                Enviar currículo

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <a href="inicialTrabalhador.php">Voltar</a>

    </main>

</div>

</body>
</html>
