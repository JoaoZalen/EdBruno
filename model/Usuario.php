<?php

class Usuario {

    private string $nome;
    private string $email;
    private string $cpf;
    private string $senha;
    private string $foto;

    public function __construct(
        string $nome,
        string $email,
        string $cpf,
        string $senha
    ){

        $this->nome = $nome;
        $this->email = $email;
        $this->cpf = $cpf;
        $this->senha = $senha;
        $this->foto = "default.png";

    }

    //GETTERS

    public function get_Nome(): string{

        return $this->nome;

    }

    public function get_Email(): string{

        return $this->email;

    }

    public function get_Cpf(): string{

        return $this->cpf;

    }

    public function get_Senha(): string{

        return $this->senha;

    }

    public function get_Foto(): string{

        return $this->foto;

    }

    //SETTERS

    public function set_Nome(string $nome): void{

        $this->nome = $nome;

    }

    public function set_Email(string $email): void{

        $this->email = $email;

    }

    public function set_Cpf(string $cpf): void{

        $this->cpf = $cpf;

    }

    public function set_Senha(string $senha): void{

        $this->senha = $senha;

    }

    public function set_Foto(string $foto): void{

        $this->foto = $foto;

    }

    //MÉTODO

    public function imprimir(): string{

        return "Nome: {$this->nome}
                | E-mail: {$this->email}
                | CPF: {$this->cpf}
                | Foto: {$this->foto}";

    }

}

?>