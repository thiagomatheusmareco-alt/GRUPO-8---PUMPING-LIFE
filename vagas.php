
<?php

session_start();

require_once 'funcoes.php';

exigirLogin('empresa');

$idEmpresa = indiceUsuarioAtual();

if ($idEmpresa === false) {
    header('Location: index.php');
    exit;
}

$mensagem = '';
$erro = '';

$vagas = lerJSON('vagas.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $requisitos = trim($_POST['requisitos'] ?? '');
    $expiracao = $_POST['expiracao'] ?? '';

    $dataValida = DateTime::createFromFormat('Y-m-d', $expiracao);

    if (
        $titulo === '' ||
        $descricao === '' ||
        $requisitos === '' ||
        !$dataValida ||
        $dataValida->format('Y-m-d') !== $expiracao ||
        $expiracao < date('Y-m-d')
    ) {

        $erro = 'Preencha todos os campos e informe uma data de expiração válida.';

    } else {

        $vagas[] = [
            'empresa_id' => $idEmpresa,
            'empresa_nome' => $_SESSION['nome'],

            'titulo' => mb_substr($titulo, 0, 150),
            'descricao' => mb_substr($descricao, 0, 3000),
            'requisitos' => mb_substr($requisitos, 0, 3000),

            'expiracao' => $expiracao,
            'status' => 'ativa',

            'data_criacao' => date('Y-m-d H:i:s')
        ];

        if (salvarJSON('vagas.json', $vagas)) {

            header('Location: vagas.php?criada=1');
            exit;

        } else {

            $erro = 'Não foi possível salvar a vaga.';
        }
    }
}

// Recupera somente as vagas da empresa atual
$minhasVagas = [];

foreach ($vagas as $id => $vaga) {

    if (($vaga['empresa_id'] ?? null) == $idEmpresa) {

        $minhasVagas[$id] = $vaga;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gerenciar vagas</title>

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

        <h1>Minhas vagas</h1>

        <?php if (isset($_GET['criada'])): ?>

            <div class="alert alert-success">
                Vaga cadastrada com sucesso!
            </div>

        <?php endif; ?>

        <?php if ($erro !== ''): ?>

            <div class="alert alert-danger">
                <?php echo protegerTexto($erro); ?>
            </div>

        <?php endif; ?>

        <h3 class="mt-5 mb-3">Vagas cadastradas</h3>

        <?php if (empty($minhasVagas)): ?>

            <p class="vazio">
                Você ainda não cadastrou nenhuma vaga.
            </p>

        <?php else: ?>

            <?php foreach ($minhasVagas as $id => $vaga): ?>

                <div class="painel-card mb-3">

                    <h4>
                        <?php echo protegerTexto($vaga['titulo']); ?>
                    </h4>

                    <p>
                        <?php echo nl2br(protegerTexto($vaga['descricao'])); ?>
                    </p>

                    <p>
                        <strong>Requisitos:</strong><br>
                        <?php echo nl2br(protegerTexto($vaga['requisitos'])); ?>
                    </p>

                    <p>
                        <strong>Expiração:</strong>

                        <?php echo date(
                            'd/m/Y',
                            strtotime($vaga['expiracao'])
                        ); ?>
                    </p>

                    <p>
                        <strong>Situação:</strong>

                        <?php
                        echo $vaga['expiracao'] < date('Y-m-d')
                            ? 'Expirada'
                            : 'Ativa';
                        ?>
                    </p>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

        <div class="d-flex justify-content-end mt-4">
            <button type="button"
                    class="btn btn-dourado"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAdicionarVaga">
                + Adicionar vaga
            </button>
        </div>

        <!-- Formulário em janela modal -->
        <div class="modal fade" id="modalAdicionarVaga" tabindex="-1"
             aria-labelledby="tituloModalVaga" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content painel-card">
                    <div class="modal-header border-secondary">
                        <h3 class="modal-title" id="tituloModalVaga">
                            Adicionar vaga
                        </h3>
                        <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <form method="POST">
                        <div class="modal-body">
                            <label for="titulo">Título da vaga</label>
                            <input type="text" id="titulo" name="titulo"
                                   class="form-control" maxlength="150" required>

                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao"
                                      class="form-control" rows="4"
                                      maxlength="3000" required></textarea>

                            <label for="requisitos">Requisitos</label>
                            <textarea id="requisitos" name="requisitos"
                                      class="form-control" rows="4"
                                      maxlength="3000" required></textarea>

                            <label for="expiracao">Data de expiração</label>
                            <input type="date" id="expiracao" name="expiracao"
                                   class="form-control"
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-outline-light"
                                    data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-dourado">
                                Cadastrar vaga
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($erro !== ''): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('modalAdicionarVaga'));
        modal.show();
    });
</script>
<?php endif; ?>
</body>
</html>
