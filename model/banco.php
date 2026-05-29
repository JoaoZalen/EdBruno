<?php

class banco
{

    private $host;
    private $login;
    private $senha;
    private $dataBase;

    public function __construct(){

        $this->host = "localhost";
        $this->login = "root";
        $this->senha = "";
        $this->dataBase = "ed_ensino";

    }

    //Métodos
    public function conectarBD(){

        $conexao = mysqli_connect(
            $this->host,
            $this->login,
            $this->senha,
            $this->dataBase
        );

        return $conexao;

    }

    public function verificarLogin($email, $senha){

        $conexao = $this->conectarBD();
        $consulta = "SELECT * FROM usuarios 
                     WHERE email = '$email' 
                     AND senha = '$senha'";

        $resultado = mysqli_query($conexao, $consulta);

        if(mysqli_num_rows($resultado) > 0){

            return true;

        }else{
            return false;
        }

    }

    public function inserirUsuario($usuario){

        $conexao = $this->conectarBD();

        $consulta = "INSERT INTO usuarios
                    (nome, email, cpf, senha, foto)
                    VALUES
                    (
                        '" . $usuario->get_Nome() . "',
                        '" . $usuario->get_Email() . "',
                        '" . $usuario->get_Cpf() . "',
                        '" . $usuario->get_Senha() . "',
                        '" . $usuario->get_Foto() . "'
                    )";

        mysqli_query($conexao, $consulta);

    }
    public function getUser($email, $senha)
    {
            $conexao = $this->conectarBD();
            $consulta = "SELECT * FROM usuarios 
                        WHERE email = '$email' 
                        AND senha = '$senha'";

            $resultado = mysqli_query($conexao, $consulta);

            if(mysqli_num_rows($resultado) > 0)
            {
                $user = mysqli_fetch_assoc($resultado);
                return $user;
            }

        return false;
    }
}
?>
