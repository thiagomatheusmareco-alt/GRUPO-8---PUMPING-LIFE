
<?php

session_start();

require_once 'funcoes.php';

exigirLogin('trabalhador');

$idTrabalhador = indiceUsuarioAtual();

$candidaturas = lerJSON('candidaturas.json');

$minhasCandidaturas = [];

foreach ($candidaturas as $id => $candidatura) {

    if (($candidatura['trabalhador_id'] ?? null) == $idTrabalhador) {
        $minhasCandidaturas[$id] = $candidatura;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Currículos enviados</title>

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

        <h1>Currículos enviados</h1>

        <?php if (isset($_GET['enviado'])): ?>

            <div class="alert alert-success">
                Currículo enviado com sucesso!
            </div>

        <?php endif; ?>

        <?php if (empty($minhasCandidaturas)): ?>

            <p class="vazio mt-4">
                Você ainda não enviou nenhum currículo.
            </p>

        <?php else: ?>

            <div class="table-responsive mt-4">

                <table class="table table-dark table-hover">

                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Vaga</th>
                            <th>Data</th>
                            <th>Situação</th>
                            <th>Currículo</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($minhasCandidaturas as $id => $c): ?>

                        <tr>

                            <td>
                                <?php echo protegerTexto($c['nome_empresa']); ?>
                            </td>

                            <td>
                                <?php echo protegerTexto($c['titulo_vaga']); ?>
                            </td>

                            <td>
                                <?php echo date(
                                    'd/m/Y',
                                    strtotime($c['data_envio'])
                                ); ?>
                            </td>

                            <td>

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

                            </td>

                            <td>

                                <a href="visualizarCurriculo.php?id=<?php echo (int)$id; ?>"
                                   class="btn btn-sm btn-dourado">

                                    Visualizar

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
