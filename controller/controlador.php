<?php
require_once("../model/banco.php"); //puxa os métodos do banco
require_once("../model/usuario.php"); //puxa os métodos do usuario


class Controlador{

    //Atributo
    private $banco;

    function __construct(){
        $this->banco = new banco();
    }

    public function getUser($email, $senha)
    {
        $user = $this->banco->getUser($email, $senha);
        if ($user == false) 
        {
            return null;
        }
        else
        {
            $usuario = new Usuario(
                $user['nome'],
                $user['email'],
                $user['cpf'],
                $user['senha']
            );
            return $usuario;
        }
    }

    public function Login($email, $senha)
    {
        $user = $this->getUser($email, $senha);
        if ($user !== null) 
        {
            $_SESSION['usuario'] = $user;
            return true;
        }  
        else 
        {
            return false;
        }
    }

    public function cadastrarUsuario($nome, $email, $cpf, $senha)
    {
        $usuario = new Usuario($nome, $email, $cpf, $senha);
        $this->banco->inserirUsuario($usuario);
    }

    

}

?>
