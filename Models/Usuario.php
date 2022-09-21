<?php

class Usuario {

    private $nome;
    private $email;
    private $senha;
    public  $confirmaSenha;

    function setNome($nome) {
        if (!empty($nome) && !is_null($nome)) {
            $this->nome = $nome;
        } else {
            throw new Exception('Campo nome não pode ser vazio!');
        }
    }
    function setEmail($email) {
        if (!empty($email) && !is_null($email)) {
            $this->email = $email;
        } else {
            throw new Exception('Campo email não pode ser vazio!');
        }
    }
    function setSenha($senha) {
        if (!empty($senha) && !is_null($senha)) {
            if (strlen($senha) >= 8 ) {
                $this->senha = $senha;
            } else {
                throw new Exception('Senha deve conter no mínimo 8 caracteres!');
            }
        } else {
            throw new Exception('Campo senha não pode ser vazio!');
        }
    }

    function getNome() {
        return $this-> nome;
    }

    function getEmail() {
        return $this-> email;
    }

    function getSenha() {
        return $this-> senha;
    }

    function verificaSenha($senha1,$senha2) {
        if (strcmp($senha1,$senha2) == 0) {
            return true;
        } else {
            return false;
        }
    }
}


?>