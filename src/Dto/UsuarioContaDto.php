<?php

namespace App\Dto;

class UsuarioContaDto
{
    private ?int $id = null;
    private ?string $nome = null;
    private ?string $email = null;
    private ?string $senha = null; 
    private ?string $telefone = null;
    private ?string $cpf = null;
    private ?string $numeroConta = null;
    private ?string $saldo = null;


    public function getId(): ?int
    {
        return $this->id;
    }



    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }



    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }



    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
        return $this;
    }



    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
        return $this;
    }


    
    public function getCpf()
    {
        return $this->cpf;
    }

    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }



    public function getNumeroConta()
    {
        return $this->numeroConta;
    }

    public function setNumeroConta($numeroConta)
    {
        $this->numeroConta = $numeroConta;
        return $this;
    }



    public function getSaldo()
    {
        return $this->saldo;
    }

    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
        return $this;
    }
}
