<?php

session_start();
require_once 'funcoes.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

if (($_SESSION['tipo_conta'] ?? '') !== 'empresa') {
    header('Location: inicialTrabalhador.php');
    exit;
}

// Lista os trabalhadores já cadastrados para exibir na página inicial da empresa.
// (O sistema de pesquisa/filtro sobre essa listagem será implementado em uma etapa futura.)
$nomesTodos = file_exists('nome.json') ? json_decode(file_get_contents('nome.json'), true) : [];
$tiposTodos = file_exists('tipoConta.json') ? json_decode(file_get_contents('tipoConta.json'), true) : [];

$nomesTodos = is_array($nomesTodos) ? $nomesTodos : [];
$tiposTodos = is_array($tiposTodos) ? $tiposTodos : [];

$trabalhadores = [];
foreach ($tiposTodos as $i => $tipo) {
    if ($tipo === 'trabalhador') {
        $trabalhadores[$i] = $nomesTodos[$i] ?? 'Trabalhador sem nome';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Área da Empresa | Projeto</title>

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

            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome'] ?? ''); ?>!</h1>

            <p>
                Esta é a sua área como empresa. Em breve você poderá pesquisar
                trabalhadores e entrar em contato diretamente por aqui.
            </p>

            <h3 class="mt-4 mb-3">Trabalhadores cadastrados</h3>

            <?php if (empty($trabalhadores)): ?>

                <p class="vazio">Nenhum trabalhador cadastrado até o momento.</p>

            <?php else: ?>

                <div class="row row-cols-1 row-cols-md-3 g-3">

                    <?php foreach ($trabalhadores as $id => $nomeTrabalhador): ?>

                        <div class="col">
                            <div class="card-listagem">
                                <div class="nome-conta">
                                    <?php echo htmlspecialchars($nomeTrabalhador); ?>
                                </div>
                                <a class="btn btn-dourado"
                                   href="perfilPublico.php?id=<?php echo (int) $id; ?>">
                                    Ver perfil
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </main>

    </div>

</body>

</html>