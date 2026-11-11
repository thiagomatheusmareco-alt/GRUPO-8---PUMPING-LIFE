<?php
    session_start();
    if(!isset($_SESSION['usuario'])){
        header('Location:index.php');
    }
    $emails = $_SESSION['emails'];
    $id = array_search($_SESSION['usuario'], $emails);
    $nomes = $_SESSION['nomes'];
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale-1">
        <meta http-equiv="content-language" content="pt-br">
        <title>PHP / Array</title>
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
        <div class="row justify-content-center row-cols-1 row-cols-md-3 text-center">
            <div class="cols">
                <div class="card mb-4 rounded shadow-sw">
                    <div class="card-header py-3">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#000080" class="bi bi-floppy-fill" viewBox="0 0 16 16">
                    <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
                    <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
                    </svg>
                        <font color="black"><b>  SALVAMENTO DE DADOS</b></font></h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $porc = 0;
                        $dados = $_SESSION['nomes'];
                        $conteudo = json_encode($dados, JSON_PRETTY_PRINT);
                        file_put_contents("nome.json", $conteudo);
                        $porc = 25;
                        
                        $dados = $_SESSION['emails'];
                        $conteudo = json_encode($dados, JSON_PRETTY_PRINT);
                        file_put_contents("email.json", $conteudo);
                        $porc = 50;
                        
                        $dados = $_SESSION['senhas'];
                        $conteudo = json_encode($dados, JSON_PRETTY_PRINT);
                        file_put_contents("senha.json", $conteudo);
                        $porc = 75;
                        
                        $dados = $_SESSION['generos'];
                        $conteudo = json_encode($dados, JSON_PRETTY_PRINT);
                        file_put_contents("genero.json", $conteudo);
                        $porc = 100;
                        echo "<div class='progress'>";
                            echo "<div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='75' aria-valuemin='0' aria-valuemax='100' style='width: $porc%'></div>";
                        echo "</div>";
                        if($porc == 100){    
                            echo "<br/><h4>DADOS GRAVADOS COM SUCESSO!</h4>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>