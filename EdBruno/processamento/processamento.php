<?php

session_start();

require_once("../controller/controlador.php");
require_once("../model/banco.php");
require_once("../model/Usuario.php");

$controlador = new Controlador();

if(isset($_POST['acao']) && $_POST['acao'] == "login"){

    $email = isset($_POST['inputEmail']) ? $_POST['inputEmail'] : "";
    $senha = isset($_POST['inputSenha']) ? $_POST['inputSenha'] : "";

    if(empty($email) || empty($senha)){

        $_SESSION['Error'] = "Por favor, preencha todos os campos.";

        header("Location:../view/login.php");
        exit();
    }

    if($controlador->Login($email, $senha) == true){

        $_SESSION['estaLogado'] = true;

        if(isset($_SESSION['usuario'])){

            $usuario = $_SESSION['usuario'];

            if(is_object($usuario) && method_exists($usuario, "get_Nome")){
                $_SESSION['usuario_nome'] = $usuario->get_Nome();
            }
        }

        header("Location:../view/home.php");
        exit();
    }
    else{

        $_SESSION['estaLogado'] = false;

        $_SESSION['Error'] = "Email ou senha inválidos.";

        header("Location:../view/login.php");
        exit();
    }
}

if(isset($_POST['acao']) && $_POST['acao'] == "cadastro"){

    $nome = isset($_POST['nome']) ? $_POST['nome'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : "";
    $senha = isset($_POST['senha']) ? $_POST['senha'] : "";
    $confirmarSenha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : "";

    if(empty($nome) || empty($email) || empty($cpf) || empty($senha) || empty($confirmarSenha)){

        $_SESSION['Error'] = "Por favor, preencha todos os campos.";

        header("Location:../view/cadastro.php");
        exit();
    }

    if($senha != $confirmarSenha){

        $_SESSION['Error'] = "As senhas não coincidem.";

        header("Location:../view/cadastro.php");
        exit();
    }

    try{

        $controlador->cadastrarUsuario(
            $nome,
            $email,
            $cpf,
            $senha
        );

        $_SESSION['Error'] = "Cadastro realizado com sucesso. Faça login.";

        header("Location:../view/login.php");
        exit();

    }catch(Exception $e){

        $_SESSION['Error'] = "CPF já cadastrado. Use outro CPF ou faça login.";

        header("Location:../view/cadastro.php");
        exit();
    }
}

header("Location:../view/login.php");
exit();

?>