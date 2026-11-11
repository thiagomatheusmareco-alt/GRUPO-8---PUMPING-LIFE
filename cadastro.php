<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classe = $_POST['classificacao'];
    $relatorio = $_POST['relatorio'];
    $data = date('d/m/Y');
    $professor = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'desconhecido';

    $novoRelatorio = [  
        'professor' => $professor,
        'data' => $data,
        'relatorio' => $relatorio,
        'classificacao' => $classe
    ];

    $arquivo = 'relatorios.json';
    if (file_exists($arquivo)) {
        $relatorios = json_decode(file_get_contents($arquivo), true);
        if (!is_array($relatorios)) {
            $relatorios = [];
        }
    } else {
        $relatorios = [];
    }

    $relatorios[] = $novoRelatorio;
    file_put_contents($arquivo, json_encode($relatorios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "<script>alert('Relatório salvo com sucesso!'); window.location.href='inicialTrabalhador.php';';</script>";
    exit;
}
?>