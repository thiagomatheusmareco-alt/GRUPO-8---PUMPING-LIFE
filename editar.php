<?php
    session_start();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $genero = $_POST['genero'];
        $_SESSION['nomes'][$id] = $nome;
        $_SESSION['emails'][$id] = $email;
        $_SESSION['senhas'][$id] = $senha;
        $_SESSION['generos'][$id] = $genero;
        header("Location:listagem.php");
        
    }
?>