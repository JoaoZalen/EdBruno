<?php
session_start();    
require_once("../controller/controlador.php"); //puxa os métodos do controlador
require_once("../model/banco.php"); //puxa os métodos do banco
require_once("../model/usuario.php"); //puxa os métodos do usuario

$controlador = new Controlador(); //instancia o controlador

//Login
if(isset($_POST['acao']) && $_POST['acao'] === 'login')
{
    $email = $_POST['inputEmail'] ?? '';
    $senha = $_POST['inputSenha'] ?? '';

    if(empty($email) || empty($senha)){
        $error = "Por favor, preencha todos os campos."; 
        $_SESSION['Error'] = $error;
        header('Location:login.php');
        die();
    }

    if($controlador->Login($email, $senha) == true){
        $_SESSION['estaLogado'] = TRUE;
        header('Location:../view/home.php');
        die();
    } else 
    {
        $_SESSION['estaLogado'] = FALSE;
        $error = "Erro ao fazer login. Verifique suas credenciais."; 
        $_SESSION['Error'] = $error;
        header('Location:../view/login.php');
        die();
    }
}

if(isset($_POST['acao']) && $_POST['acao'] === 'cadastro')
{
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if($senha !== $confirmarSenha){
        $error = "As senhas não coincidem."; 
        $_SESSION['Error'] = $error;
        header('Location:../view/cadastro.php');
        die();
    }

    if(empty($nome) || empty($email) || empty($cpf) || empty($senha)){
        $error = "Por favor, preencha todos os campos."; 
        $_SESSION['Error'] = $error;
        header('Location:../view/cadastro.php');
        die();
    }

    $controlador->cadastrarUsuario($nome, $email, $cpf, $senha);

    if($controlador->Login($email, $senha) == true){
        $_SESSION['estaLogado'] = TRUE;
        if (isset($_SESSION['usuario'])) {
            $usuario = $_SESSION['usuario'];
        }
        header('Location:../view/home.php');
        die();

    } else {
        $_SESSION['estaLogado'] = FALSE;
        $error = "Erro ao fazer login. Verifique suas credenciais."; 
        $_SESSION['Error'] = $error;
        header('Location:../view/login.php');
        die(); 
    }
}
