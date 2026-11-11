<?php

session_start();
require_once 'funcoes.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$emails = file_exists('email.json') ? json_decode(file_get_contents('email.json'), true) : [];
$nomes = file_exists('nome.json') ? json_decode(file_get_contents('nome.json'), true) : [];
$emails = is_array($emails) ? $emails : [];
$nomes = is_array($nomes) ? $nomes : [];

$id = array_search($_SESSION['usuario'], $emails, true);

if ($id === false) {
    header('Location: index.php');
    exit;
}

$perfis = file_exists('perfil.json') ? json_decode(file_get_contents('perfil.json'), true) : [];
$perfis = is_array($perfis) ? $perfis : [];
$meuPerfil = $perfis[$id] ?? [];

$mensagem = isset($_GET['salvo']) ? 'Perfil atualizado com sucesso!' : '';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Meu Perfil | Projeto</title>

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

            <h1>Meu Perfil</h1>

            <?php if ($mensagem !== ''): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div class="painel-card text-start">

                <form action="salvarPerfil.php" method="post" enctype="multipart/form-data">

                    <label for="nomeConta">Nome</label>
                    <input
                        class="form-control"
                        id="nomeConta"
                        type="text"
                        value="<?php echo htmlspecialchars($nomes[$id] ?? ''); ?>"
                        disabled
                    >

                    <label for="endereco">Endereço</label>
                    <input
                        class="form-control"
                        id="endereco"
                        name="endereco"
                        type="text"
                        maxlength="200"
                        placeholder="Cidade, estado..."
                        value="<?php echo htmlspecialchars($meuPerfil['endereco'] ?? ''); ?>"
                    >

                    <label for="genero">Gênero</label>
                    <select class="form-select" id="genero" name="genero">
                        <option value="" <?php echo empty($meuPerfil['genero']) ? 'selected' : ''; ?>>
                            Prefiro não informar
                        </option>
                        <option value="Masculino" <?php echo ($meuPerfil['genero'] ?? '') === 'Masculino' ? 'selected' : ''; ?>>
                            Masculino
                        </option>
                        <option value="Feminino" <?php echo ($meuPerfil['genero'] ?? '') === 'Feminino' ? 'selected' : ''; ?>>
                            Feminino
                        </option>
                        <option value="Outro" <?php echo ($meuPerfil['genero'] ?? '') === 'Outro' ? 'selected' : ''; ?>>
                            Outro
                        </option>
                    </select>

                    <label for="descricao">Descrição</label>
                    <textarea
                        class="form-control"
                        id="descricao"
                        name="descricao"
                        rows="4"
                        maxlength="1000"
                        placeholder="Fale um pouco sobre você (ou sobre a empresa)..."
                    ><?php echo htmlspecialchars($meuPerfil['descricao'] ?? ''); ?></textarea>

                    <label for="qualificacoes">Qualificações</label>
                    <textarea
                        class="form-control"
                        id="qualificacoes"
                        name="qualificacoes"
                        rows="4"
                        maxlength="1000"
                        placeholder="Cursos, habilidades, experiências..."
                    ><?php echo htmlspecialchars($meuPerfil['qualificacoes'] ?? ''); ?></textarea>

                    <label for="curriculo">Currículo (PDF, até 5MB)</label>

                    <?php if (!empty($meuPerfil['curriculo'])): ?>
                        <div class="mb-2">
                            Arquivo atual:
                            <a href="<?php echo htmlspecialchars($meuPerfil['curriculo']); ?>" target="_blank">
                                ver currículo enviado
                            </a>
                        </div>
                    <?php endif; ?>

                    <input
                        class="form-control"
                        id="curriculo"
                        name="curriculo"
                        type="file"
                        accept="application/pdf"
                    >

                    <button class="btn btn-dourado mt-4" type="submit">
                        Salvar alterações
                    </button>

                </form>

            </div>

        </main>

    </div>

</body>

</html>