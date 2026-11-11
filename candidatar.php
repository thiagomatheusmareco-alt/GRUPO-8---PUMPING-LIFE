
<?php

session_start();

require_once 'funcoes.php';

exigirLogin('trabalhador');

$idTrabalhador = indiceUsuarioAtual();

if ($idTrabalhador === false) {
    header('Location: index.php');
    exit;
}

$idVaga = filter_input(INPUT_GET, 'id_vaga', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVaga = filter_input(INPUT_POST, 'id_vaga', FILTER_VALIDATE_INT);
}

$vagas = lerJSON('vagas.json');
$nomes = lerJSON('nome.json');
$perfis = lerJSON('perfil.json');
$tipos = lerJSON('tipoConta.json');

if (
    $idVaga === false ||
    $idVaga === null ||
    !isset($vagas[$idVaga])
) {
    die('Vaga não encontrada.');
}

$vaga = $vagas[$idVaga];

$idEmpresa = $vaga['empresa_id'];

if (
    ($tipos[$idEmpresa] ?? '') !== 'empresa' ||
    ($vaga['status'] ?? '') !== 'ativa' ||
    empty($vaga['expiracao']) ||
    $vaga['expiracao'] < date('Y-m-d')
) {
    die('Esta vaga não está mais disponível.');
}

$perfil = $perfis[$idTrabalhador] ?? [];

$candidaturas = lerJSON('candidaturas.json');

$jaEnviou = false;

foreach ($candidaturas as $candidatura) {

    if (
        ($candidatura['vaga_id'] ?? null) == $idVaga &&
        ($candidatura['trabalhador_id'] ?? null) == $idTrabalhador
    ) {
        $jaEnviou = true;
        break;
    }
}

if ($jaEnviou) {
    die('Você já enviou currículo para esta vaga.');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modo = $_POST['modo'] ?? '';

    if (!in_array($modo, ['automatico', 'personalizado'], true)) {
        $erro = 'Selecione uma modalidade válida.';
    } else {

        $curriculo = [];

        if ($modo === 'automatico') {

            $curriculo = [
                'nome' => $_SESSION['nome'],
                'endereco' => $perfil['endereco'] ?? '',
                'descricao' => $perfil['descricao'] ?? '',
                'qualificacoes' => $perfil['qualificacoes'] ?? '',
                'genero' => $perfil['genero'] ?? '',
                'arquivo' => ''
            ];

            // Copia o PDF para que a candidatura não dependa
            // do arquivo que poderá ser substituído no perfil.
            if (
                !empty($perfil['curriculo']) &&
                is_file($perfil['curriculo'])
            ) {

                $pasta = 'uploads/candidaturas/';

                if (!is_dir($pasta)) {
                    mkdir($pasta, 0755, true);
                }

                $destino = $pasta . 'curriculo_' .
                    $idTrabalhador . '_' .
                    $idVaga . '_' . time() . '.pdf';

                if (copy($perfil['curriculo'], $destino)) {
                    $curriculo['arquivo'] = $destino;
                }
            }

        } else {

            $curriculo = [
                'nome' => $_SESSION['nome'],
                'endereco' => trim($_POST['endereco'] ?? ''),
                'descricao' => trim($_POST['descricao'] ?? ''),
                'qualificacoes' => trim($_POST['qualificacoes'] ?? ''),
                'genero' => $_POST['genero'] ?? '',
                'arquivo' => ''
            ];

            if (!in_array(
                $curriculo['genero'],
                ['', 'Masculino', 'Feminino', 'Outro'],
                true
            )) {
                $curriculo['genero'] = '';
            }

            // PDF personalizado opcional
            if (
                isset($_FILES['curriculo']) &&
                $_FILES['curriculo']['error'] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES['curriculo']['error'] !== UPLOAD_ERR_OK) {
                    $erro = 'Erro no envio do arquivo.';
                } else {

                    $arquivo = $_FILES['curriculo'];

                    $extensao = strtolower(
                        pathinfo($arquivo['name'], PATHINFO_EXTENSION)
                    );

                    $mime = function_exists('mime_content_type')
                        ? mime_content_type($arquivo['tmp_name'])
                        : '';

                    if (
                        $extensao !== 'pdf' ||
                        $mime !== 'application/pdf' ||
                        $arquivo['size'] > 5 * 1024 * 1024
                    ) {
                        $erro = 'O arquivo deve ser PDF e ter no máximo 5MB.';
                    } else {

                        $pasta = 'uploads/candidaturas/';

                        if (!is_dir($pasta)) {
                            mkdir($pasta, 0755, true);
                        }

                        $destino = $pasta . 'personalizado_' .
                            $idTrabalhador . '_' .
                            $idVaga . '_' . time() . '.pdf';

                        if (move_uploaded_file(
                            $arquivo['tmp_name'],
                            $destino
                        )) {
                            $curriculo['arquivo'] = $destino;
                        } else {
                            $erro = 'Não foi possível salvar o PDF.';
                        }
                    }
                }
            }

            if ($erro === '') {

                $personalizados = lerJSON('curriculosPersonalizados.json');

                $personalizados[] = [
                    'trabalhador_id' => $idTrabalhador,
                    'vaga_id' => $idVaga,
                    'empresa_id' => $idEmpresa,
                    'curriculo' => $curriculo
                ];

                if (!salvarJSON(
                    'curriculosPersonalizados.json',
                    $personalizados
                )) {
                    $erro = 'Não foi possível salvar o currículo personalizado.';
                }
            }
        }

        if ($erro === '') {

            $candidaturas[] = [
                'trabalhador_id' => $idTrabalhador,
                'empresa_id' => $idEmpresa,
                'vaga_id' => $idVaga,

                'nome_trabalhador' => $_SESSION['nome'],
                'nome_empresa' => $nomes[$idEmpresa] ?? '',
                'titulo_vaga' => $vaga['titulo'],

                'modo' => $modo,
                'curriculo' => $curriculo,

                'status' => 'pendente',
                'data_envio' => date('Y-m-d H:i:s')
            ];

            if (salvarJSON('candidaturas.json', $candidaturas)) {

                header('Location: curriculosEnviados.php?enviado=1');
                exit;

            } else {
                $erro = 'Não foi possível registrar a candidatura.';
            }
        }
    }
}

$modoAtual = $_GET['modo'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Enviar currículo</title>

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

        <h1>Enviar currículo</h1>

        <h3 class="mt-3">
            <?php echo protegerTexto($vaga['titulo']); ?>
        </h3>

        <p>
            Empresa:
            <?php echo protegerTexto($nomes[$idEmpresa] ?? ''); ?>
        </p>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger">
                <?php echo protegerTexto($erro); ?>
            </div>
        <?php endif; ?>

        <?php if (
            $modoAtual !== 'personalizado' &&
            !(
                $_SERVER['REQUEST_METHOD'] === 'POST' &&
                ($_POST['modo'] ?? '') === 'personalizado'
            )
        ): ?>

            <div class="painel-card mt-4">

                <h3>Escolha como deseja enviar seu currículo</h3>

                <p>
                    Você pode utilizar as informações do seu perfil
                    ou preparar uma versão exclusiva para esta vaga.
                </p>

                <form method="POST" class="mb-3">

                    <input type="hidden" name="id_vaga"
                           value="<?php echo (int)$idVaga; ?>">

                    <input type="hidden" name="modo"
                           value="automatico">

                    <button type="submit" class="btn btn-dourado">
                        Enviar currículo automático
                    </button>

                </form>

                <a href="candidatar.php?id_vaga=<?php echo (int)$idVaga; ?>&modo=personalizado"
                   class="btn btn-outline-light">

                    Montar currículo específico

                </a>

            </div>

        <?php else: ?>

            <div class="painel-card mt-4">

                <h3>Currículo personalizado</h3>

                <form method="POST"
                      enctype="multipart/form-data">

                    <input type="hidden" name="id_vaga"
                           value="<?php echo (int)$idVaga; ?>">

                    <input type="hidden" name="modo"
                           value="personalizado">

                    <label>Nome</label>

                    <input class="form-control"
                           value="<?php echo protegerTexto($_SESSION['nome']); ?>"
                           disabled>

                    <label>Endereço</label>

                    <input class="form-control"
                           name="endereco"
                           maxlength="200"
                           value="<?php echo protegerTexto($perfil['endereco'] ?? ''); ?>">

                    <label>Descrição</label>

                    <textarea class="form-control"
                              name="descricao"
                              rows="4"
                              maxlength="1000"><?php echo protegerTexto($perfil['descricao'] ?? ''); ?></textarea>

                    <label>Qualificações</label>

                    <textarea class="form-control"
                              name="qualificacoes"
                              rows="4"
                              maxlength="1000"><?php echo protegerTexto($perfil['qualificacoes'] ?? ''); ?></textarea>

                    <label>Gênero</label>

                    <select class="form-select" name="genero">

                        <option value="">Prefiro não informar</option>

                        <option value="Masculino">Masculino</option>

                        <option value="Feminino">Feminino</option>

                        <option value="Outro">Outro</option>

                    </select>

                    <label>Currículo em PDF (opcional, até 5MB)</label>

                    <input type="file"
                           name="curriculo"
                           class="form-control"
                           accept="application/pdf">

                    <button type="submit"
                            class="btn btn-dourado mt-4">

                        Enviar currículo personalizado

                    </button>

                </form>

            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
