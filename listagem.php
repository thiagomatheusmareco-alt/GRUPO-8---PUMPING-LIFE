<?php
    session_start();
    // Salvar avaliação enviada pelo modal
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' && 
        isset($_POST['avaliacao']) && 
        isset($_POST['relatorio_id']) && 
        isset($_POST['classpsico'])
    ) {
        $avaliacoes = file_exists('avaliacao.json') ? json_decode(file_get_contents('avaliacao.json'), true) : [];
        if (!is_array($avaliacoes)) $avaliacoes = [];
        
        $psicologo_nome = isset($nomes[$id_relatorio]) ? $nomes[$id_relatorio] : 'Desconhecido';
        $nivel_urgencia = $_POST['classpsico'];
        $jaAvaliou = false;
        foreach ($avaliacoes as $idx => $av) {
            if ($av['relatorio_id'] == $id_relatorio && $av['psicologo'] == $psicologo_nome) {
                // Se for edição, atualiza
                $avaliacoes[$idx]['avaliacao'] = $_POST['avaliacao'];
                $avaliacoes[$idx]['classpsico'] = $nivel_urgencia;
                $avaliacoes[$idx]['data'] = date('Y-m-d H:i:s');
                $jaAvaliou = true;
                break;
            }
        }
        if (!$jaAvaliou) {
            $avaliacao = [
                'relatorio_id' => $id_relatorio,
                'avaliacao' => $_POST['avaliacao'],
                'classpsico' => $nivel_urgencia,
                'psicologo' => $psicologo_nome,
                'data' => date('Y-m-d H:i:s')
            ];
            $avaliacoes[] = $avaliacao;
        }
        file_put_contents('avaliacao.json', json_encode($avaliacoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo '<script>alert("Avaliação salva com sucesso!");</script>';
    }
    $avaliacoes = file_exists('avaliacao.json') ? json_decode(file_get_contents('avaliacao.json'), true) : [];
        if (!is_array($avaliacoes)) $avaliacoes = [];
    $id_relatorio = $_SESSION['id'] ?? null;
    if(!isset($_SESSION['usuario'])){
        header('Location:index.php');
    }
    $id = array_search($_SESSION['usuario'], $emails);
    if(!isset($_SESSION['nomes'])){
        $emails = json_decode(file_get_contents("email.json"), true);
        $senhas = json_decode(file_get_contents("senha.json"), true);
        $nomes = json_decode(file_get_contents("nome.json"), true);
        $relatorios = json_decode(file_get_contents("relatorio.json"), true);
        $classes = json_decode(file_get_contents("class.json"), true);
        $id = array_search($_SESSION['usuario'], $emails);
        $_SESSION['nomes'] = $nomes;
        $_SESSION['senhas'] = $senhas;
        $_SESSION['emails'] = $emails;
        $_SESSION['relatorios'] = $relatorios;
        $_SESSION['classes'] = $classes;
    }
    else{
        $emails = $_SESSION['emails'];
        $id = array_search($_SESSION['usuario'], $emails);
        $nomes = $_SESSION['nomes'];
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale-1">
        <meta http-equiv="content-language" content="pt-br">
        <title>PROJETO</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <style>
        .user{
            float: right;
        }
        body{
            background: linear-gradient(-45deg, #696969, #4F4F4F, #363636, #1C1C1C);
            background-size: 400% 400%;
            animation: gradient 18s ease infinite;
        }

        @keyframes gradient {
            0% {
            background-position: 0 50%;
            }
            50% {
            background-position: 100% 50%;
            }
            100% {
            background-position: 0% 50%;
        }
        }
        .cols1{
            width:300px
        }
        /* From Uiverse.io by LightAndy1 */ 
        .group {
        display: flex;
        line-height: 28px;
        align-items: center;
        position: relative;
        max-width: 190px;
        }

        .input {
        font-family: "Montserrat", sans-serif;
        width: 100%;
        height: 45px;
        padding-left: 2.5rem;
        box-shadow: 0 0 0 1.5px #2b2c37, 0 0 25px -17px #000;
        border: 0;
        border-radius: 12px;
        background-color: #16171d;
        outline: none;
        color: #bdbecb;
        transition: all 0.25s cubic-bezier(0.19, 1, 0.22, 1);
        cursor: text;
        z-index: 0;
        }

        .input::placeholder {
        color: white;
        }

        .input:hover {
        box-shadow: 0 0 0 2.5px darkblue, 0px 0px 25px -15px #000;
        }

        .input:active {
        transform: scale(0.95);
        }

        .input:focus {
        box-shadow: 0 0 0 2.5px #2f303d;
        }

        .search-icon {
        position: absolute;
        left: 1rem;
        fill: #bdbecb;
        width: 1rem;
        height: 1rem;
        pointer-events: none;
        z-index: 1;
        }

    </style>
    <body>
        <div class="card-body" style="background-color: #D3D3D3">    
            <center><h2><b>PHP/ARRAY</b></h2></center>
        </div>
        <br/><br/>
        <nav>
            &nbsp;&nbsp;<b><a href="inicial.php" style="color: white; text-decoration:none"> HOME |</a><a href="listagem.php" style="color: white; text-decoration:none"> LISTAGEM |</a><a href="gravar.php" style="color: white; text-decoration:none"> SALVAR DADOS</a></b>
            <div class="user" style="color: white; text-decoration:none">
                <b><?php echo $nomes[$id]; ?> - <a href="sair.php" style="color: white; text-decoration:none">SAIR</a></b>&nbsp;&nbsp;
            </div>
        </nav>
        <br/><br/>
        <center>
        <div class="container">
            <h3 style="color:white">Relatórios Publicados</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover bg-white">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Professor</th>
                            <th>Classificação</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $relatorios = file_exists('relatorios.json') ? json_decode(file_get_contents('relatorios.json'), true) : [];
                        if ($relatorios && is_array($relatorios)) {
                            foreach ($relatorios as $i => $rel) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($rel['data']) . '</td>';
                                echo '<td>' . htmlspecialchars($rel['professor']) . '</td>';
                                echo '<td>' . htmlspecialchars($rel['classificacao']) . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#detalheModal'.$i.'">Ver Detalhes</button>';
                                // Verifica se já existe avaliação deste psicólogo para este relatório
                                $avaliacoes = file_exists('avaliacao.json') ? json_decode(file_get_contents('avaliacao.json'), true) : [];
                                $psicologo_nome = isset($nomes[$id]) ? $nomes[$id] : 'Desconhecido';
                                $avaliacaoExistente = null;
                                if ($avaliacoes && is_array($avaliacoes)) {
                                    foreach ($avaliacoes as $av) {
                                        if ($av['relatorio_id'] == $i && $av['psicologo'] == $psicologo_nome) {
                                            $avaliacaoExistente = $av;
                                            break;
                                        }
                                    }
                                }
                                if ($avaliacaoExistente) {
                                    echo '<button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#avaliarModal'.$i.'">Editar Avaliação</button>';
                                } else {
                                    echo '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#avaliarModal'.$i.'">Avaliar</button>';
                                }
                                echo '</td>';

                            
                                
        
        if ($relatorios && is_array($relatorios)) {
            foreach ($relatorios as $i => $rel) {
        ?>
        <div class="modal fade" id="avaliarModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="avaliarModalLabel<?php echo $i; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avaliarModalLabel<?php echo $i; ?>">Avaliação do Psicólogo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="">
                        <div class="modal-body">
                            <input type="hidden" name="relatorio_id" value="<?php echo $i; ?>">
                            <label for="avaliacao<?php echo $i; ?>" class="form-label">Descreva sua avaliação sobre o caso:</label>
                            <?php
                            // Preenche o campo com a avaliação existente, se houver
                            $valorAvaliacao = '';
                            $valorClasspsico = '';
                            if (isset($avaliacaoExistente)) {
                                $valorAvaliacao = htmlspecialchars($avaliacaoExistente['avaliacao']);
                                $valorClasspsico = isset($avaliacaoExistente['classpsico']) ? $avaliacaoExistente['classpsico'] : '';
                            }
                            ?>
                            <textarea class="form-control" id="avaliacao<?php echo $i; ?>" name="avaliacao" rows="5" required placeholder="Digite sua avaliação aqui..."><?php echo $valorAvaliacao; ?></textarea>
                            <label class="form-label">CLASSIFICAÇÃO</label>
                            <select class="form-select" aria-label="Selecione o nivel de urgencia" name="classpsico" required>
                                <option value="" disabled <?php if($valorClasspsico=='') echo 'selected'; ?>>Selecione o nível de urgência do caso</option>
                                <option value="A" <?php if($valorClasspsico=='A') echo 'selected'; ?>>Alta</option>
                                <option value="B" <?php if($valorClasspsico=='B') echo 'selected'; ?>>Média</option>
                                <option value="C" <?php if($valorClasspsico=='C') echo 'selected'; ?>>Baixa</option>
                            </select>
                            </br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-success">Salvar Avaliação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php }} 
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4">Nenhum relatório encontrado.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modais de Detalhes dos Relatórios -->
        <?php
        if ($relatorios && is_array($relatorios)) {
            foreach ($relatorios as $i => $rel) {
        ?>
        <div class="modal fade" id="detalheModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="detalheModalLabel<?php echo $i; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detalheModalLabel<?php echo $i; ?>">Detalhes do Relatório</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <strong>Data:</strong> <?php echo htmlspecialchars($rel['data']); ?><br>
                        <strong>Professor:</strong> <?php echo htmlspecialchars($rel['professor']); ?><br>
                        <strong>Classificação:</strong> <?php echo htmlspecialchars($rel['classificacao']); ?><br>
                        <strong>Relatório:</strong> <div class="border p-2 bg-light"><?php echo nl2br(htmlspecialchars($rel['relatorio'])); ?></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php }} ?>       
    </body>
</html>