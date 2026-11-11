
<?php

session_start();

require_once 'funcoes.php';

exigirLogin('trabalhador');

$nomes = lerJSON('nome.json');
$tipos = lerJSON('tipoConta.json');

$pesquisa = trim($_GET['pesquisa'] ?? '');

$empresas = [];

foreach ($tipos as $id => $tipo) {

    if ($tipo !== 'empresa') {
        continue;
    }

    $nome = $nomes[$id] ?? 'Empresa sem nome';

    // Pesquisa parcial, sem diferenciar maiúsculas e minúsculas
    if ($pesquisa !== '') {

        $nomeBusca = function_exists('mb_strtolower')
            ? mb_strtolower($nome, 'UTF-8')
            : strtolower($nome);

        $termoBusca = function_exists('mb_strtolower')
            ? mb_strtolower($pesquisa, 'UTF-8')
            : strtolower($pesquisa);

        if (strpos($nomeBusca, $termoBusca) === false) {
            continue;
        }
    }

    $empresas[$id] = $nome;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Área do Trabalhador</title>

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

        <h1>
            Bem-vindo, <?php echo protegerTexto($_SESSION['nome']); ?>!
        </h1>

        <p>
            Encontre empresas, conheça suas oportunidades
            e envie seu currículo.
        </p>

        <h3 class="mt-4 mb-3">Pesquisar empresas</h3>

        <form method="GET" action="inicialTrabalhador.php"
              class="d-flex gap-2 mb-4">

            <input
                type="search"
                name="pesquisa"
                class="form-control"
                placeholder="Digite o nome da empresa..."
                value="<?php echo protegerTexto($pesquisa); ?>"
            >

            <button type="submit" class="btn btn-dourado">
                Pesquisar
            </button>

            <?php if ($pesquisa !== ''): ?>

                <a href="inicialTrabalhador.php"
                   class="btn btn-outline-light">
                    Limpar
                </a>

            <?php endif; ?>

        </form>

        <h3 class="mb-3">Empresas cadastradas</h3>

        <?php if (empty($empresas)): ?>

            <p class="vazio">
                <?php echo $pesquisa !== ''
                    ? 'Nenhuma empresa encontrada com esse nome.'
                    : 'Nenhuma empresa cadastrada até o momento.'; ?>
            </p>

        <?php else: ?>

            <div class="row row-cols-1 row-cols-md-3 g-3">

                <?php foreach ($empresas as $id => $nomeEmpresa): ?>

                    <div class="col">

                        <div class="card-listagem">

                            <div class="nome-conta">
                                <?php echo protegerTexto($nomeEmpresa); ?>
                            </div>

                            <a class="btn btn-dourado"
                               href="perfilPublico.php?id=<?php echo (int)$id; ?>">

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
