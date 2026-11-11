
<?php

$paginaAtual = basename($_SERVER['PHP_SELF']);

$tipoContaMenu = $_SESSION['tipo_conta'] ?? 'trabalhador';

$homeLinkMenu = $tipoContaMenu === 'empresa'
    ? 'inicialEmpresa.php'
    : 'inicialTrabalhador.php';

?>

<aside class="sidebar">

    <div class="sidebar-usuario">

        <?php echo protegerTexto($_SESSION['nome'] ?? ''); ?>

    </div>

    <nav class="nav flex-column">

        <a
            class="nav-link<?php echo $paginaAtual === $homeLinkMenu ? ' active' : ''; ?>"
            href="<?php echo $homeLinkMenu; ?>"
        >
            Início
        </a>

        <a
            class="nav-link<?php echo $paginaAtual === 'perfil.php' ? ' active' : ''; ?>"
            href="perfil.php"
        >
            Meu Perfil
        </a>

        <?php if ($tipoContaMenu === 'trabalhador'): ?>

            <a
                class="nav-link<?php echo $paginaAtual === 'curriculosEnviados.php' ? ' active' : ''; ?>"
                href="curriculosEnviados.php"
            >
                Currículos enviados
            </a>

        <?php endif; ?>

        <?php if ($tipoContaMenu === 'empresa'): ?>

            <a
                class="nav-link<?php echo $paginaAtual === 'vagas.php' ? ' active' : ''; ?>"
                href="vagas.php"
            >
                Vagas
            </a>

            <a
                class="nav-link<?php echo $paginaAtual === 'curriculosRecebidos.php' ? ' active' : ''; ?>"
                href="curriculosRecebidos.php"
            >
                Currículos recebidos
            </a>

        <?php endif; ?>

        <a class="nav-link" href="sair.php">
            Sair
        </a>

    </nav>

</aside>
