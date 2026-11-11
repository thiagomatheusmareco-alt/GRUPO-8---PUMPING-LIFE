
<?php

session_start();

require_once 'funcoes.php';

exigirLogin('empresa');

$idEmpresa = indiceUsuarioAtual();

$candidaturas = lerJSON('candidaturas.json');

$recebidas = [];

foreach ($candidaturas as $id => $candidatura) {

    if (($candidatura['empresa_id'] ?? null) == $idEmpresa) {
        $recebidas[$id] = $candidatura;
    }
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idCandidatura = filter_input(
        INPUT_POST,
        'id_candidatura',
        FILTER_VALIDATE_INT
    );

    $novoStatus = $_POST['status'] ?? '';

    if (
        $idCandidatura === false ||
        $idCandidatura === null ||
        !isset($candidaturas[$idCandidatura]) ||
        ($candidaturas[$idCandidatura]['empresa_id'] ?? null) != $idEmpresa ||
        !in_array($novoStatus, ['aceito', 'recusado'], true)
    ) {

        $erro = 'Não foi possível atualizar a candidatura.';

    } else {

        $candidaturas[$idCandidatura]['status'] = $novoStatus;

        if (salvarJSON('candidaturas.json', $candidaturas)) {

            header('Location: curriculosRecebidos.php?atualizado=1');
            exit;

        } else {

            $erro = 'Erro ao salvar a decisão.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Currículos recebidos</title>

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

        <h1>Currículos recebidos</h1>

        <?php if (isset($_GET['atualizado'])): ?>

            <div class="alert alert-success">
                Decisão registrada com sucesso!
            </div>

        <?php endif; ?>

        <?php if ($erro !== ''): ?>

            <div class="alert alert-danger">
                <?php echo protegerTexto($erro); ?>
            </div>

        <?php endif; ?>

        <?php if (empty($recebidas)): ?>

            <p class="vazio mt-4">
                Você ainda não recebeu nenhum currículo.
            </p>

        <?php else: ?>

            <?php foreach ($recebidas as $id => $c): ?>

                <div class="painel-card mb-4">

                    <h3>
                        <?php echo protegerTexto($c['nome_trabalhador']); ?>
                    </h3>

                    <p>
                        <strong>Vaga:</strong>
                        <?php echo protegerTexto($c['titulo_vaga']); ?>
                    </p>

                    <p>
                        <strong>Data de envio:</strong>

                        <?php echo date(
                            'd/m/Y H:i',
                            strtotime($c['data_envio'])
                        ); ?>

                    </p>

                    <p>
                        <strong>Modalidade:</strong>

                        <?php echo ($c['modo'] ?? '') === 'automatico'
                            ? 'Currículo automático'
                            : 'Currículo personalizado'; ?>

                    </p>

                    <p>
                        <strong>Situação:</strong>

                        <?php
                        $status = [
                            'pendente' => 'Pendente',
                            'aceito' => 'Aceito',
                            'recusado' => 'Recusado'
                        ];

                        echo protegerTexto(
                            $status[$c['status']] ?? 'Pendente'
                        );
                        ?>

                    </p>

                    <a href="visualizarCurriculo.php?id=<?php echo (int)$id; ?>"
                       class="btn btn-dourado mb-3">

                        Visualizar currículo

                    </a>

                    <?php if (($c['status'] ?? '') === 'pendente'): ?>

                        <div class="d-flex gap-2 flex-wrap">

                            <form method="POST">

                                <input type="hidden"
                                       name="id_candidatura"
                                       value="<?php echo (int)$id; ?>">

                                <input type="hidden"
                                       name="status"
                                       value="aceito">

                                <button type="submit"
                                        class="btn btn-success">

                                    Aceitar

                                </button>

                            </form>

                            <form method="POST">

                                <input type="hidden"
                                       name="id_candidatura"
                                       value="<?php echo (int)$id; ?>">

                                <input type="hidden"
                                       name="status"
                                       value="recusado">

                                <button type="submit"
                                        class="btn btn-danger">

                                    Recusar

                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
