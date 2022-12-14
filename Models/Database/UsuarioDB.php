<?php

require('ConnectionDB.php');
//require(__DIR__.'../../Usuario.php');

class UsuarioDB
{

    function cadastro(Usuario $usuario)
    {

        try {

            $connect = new ConnectionDB();
            $searchEmail = $usuario->getEmail();
            $query = "select usu_email from USUARIOS where usu_email = '$searchEmail'";

            $result = mysqli_query($connect->connect(), $query);
            $row = mysqli_num_rows($result);

            if ($row == 1) {
                throw new Exception('Usuário Já Cadastrado!!');
            } else {
                $user_email = mysqli_real_escape_string($connect->connect(), $usuario->getEmail());
                $user_name  = mysqli_real_escape_string($connect->connect(), $usuario->getNome());
                $user_pass  = mysqli_real_escape_string($connect->connect(), hash('sha512', $usuario->getSenha()));

                $sql = "insert into usuarios(usu_nome,usu_email,usu_senha) values('$user_name','$user_email','$user_pass')";

                if (mysqli_query($connect->connect(), $sql)) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Throwable $th) {
            throw new Exception('Erro no Banco de Dados, Contate o Administrador!!');
        }
    }

    function login($email, $pass)
    {


        try {

            $connect = new ConnectionDB();

            $email = mysqli_real_escape_string($connect->connect(), $email);
            $pass  = mysqli_real_escape_string($connect->connect(), hash('sha512', $pass));

            $query = "select usu_id, usu_email from usuarios where usu_email = '$email' and usu_senha = '$pass'";
            $result = mysqli_query($connect->connect(), $query);
            $row = mysqli_num_rows($result);

            if ($row == 1) {
                return true;
            } else {
                return false;
            }
        } catch (Throwable $th) {
            throw new Exception('Erro no Banco de Dados, Contate o Administrador!!');
        }
    }

    function getUser($email)
    {
        $connect = new ConnectionDB();

        $query = "select usu_nome, usu_id from usuarios where usu_email = '$email'";
        $result = mysqli_query($connect->connect(), $query);
        $data = mysqli_fetch_array($result);

        return $data;
    }

    function getDadosUsuario($id)
    {

        $connect = new ConnectionDB();

        $query = "select usu_nome,usu_email from usuarios where usu_id = $id";
        $result = mysqli_query($connect->connect(), $query);

        $list = mysqli_fetch_assoc($result);
        $total = mysqli_num_rows($result);

        $dados = [];

        if ($total > 0) {
            do {

                $nome  = $list['usu_nome'];
                $email = $list['usu_email'];

                $dados = [$nome, $email];
            } while ($list = mysqli_fetch_assoc($result));
        }

        $_SESSION['dados_editar'] = $dados;
    }

    function alterarCadastro($nome, $email, $id)
    {

        try {

            $connect = new ConnectionDB();

            $nome = mysqli_real_escape_string($connect->connect(), $nome);
            $email = mysqli_real_escape_string($connect->connect(), $email);

            $sql = "update usuarios set usu_nome = '$nome', usu_email = '$email' where usu_id = $id";

            if (mysqli_query($connect->connect(), $sql)) {
                return true;
            } else {
                return false;
            }
        } catch (mysqli_sql_exception $th) {
            throw new Exception('Falha ao Atualizar Dados, Tente Novamente!');
        }
    }

    function alterarSenha($senhaAntiga, $senhaNova, $id)
    {

        try {

            $connect = new ConnectionDB();
            $usuario = new Usuario();

            $senhaAntiga = mysqli_real_escape_string($connect->connect(), hash('sha512', $senhaAntiga));
            $senhaNova = mysqli_real_escape_string($connect->connect(), hash('sha512', $senhaNova));

            $sql_getSenha = "select usu_senha from usuarios where usu_id = $id";
            $resultado = mysqli_query($connect->connect(), $sql_getSenha);
            $get = mysqli_fetch_array($resultado);

            $senhaBD = $get[0];

            if ($usuario->verificaSenha($senhaBD, $senhaAntiga)) {
                $sql = "update usuarios set usu_senha = '$senhaNova' where usu_id = $id";
                if (mysqli_query($connect->connect(), $sql)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                throw new Exception("Senhas Divergentes! Tente Novamente...");
            }
        } catch (mysqli_sql_exception $th) {
            return $th->getMessage();
        }
    }

    function bkpDepartamentos($id)
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');
            $nomeArq = 'bkpDep' . date('d/m/Y_H:i');

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $nomeArq . '.csv');

            $connect = new ConnectionDB();

            $saida = fopen('php://output', 'w');
            fputcsv($saida, array('ID', 'USU_ID', 'NOME', 'RESPONSAVEL', 'TELEFONE', 'EMAIL'));
            $sqlDept = "select * from departamentos where usu_id = $id";
            $linhas = mysqli_query($connect->connect(), $sqlDept);

            while ($linha = mysqli_fetch_assoc($linhas)) {
                fputcsv($saida, $linha);
            };
        } catch (Throwable $th) {
            return $th;
        }
    }

    function bkpSaidas($id)
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');
            $nomeArq = 'bkpSaida' . date('d/m/Y_H:i');

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $nomeArq . '.csv');

            $connect = new ConnectionDB();

            $saida = fopen('php://output', 'w');
            fputcsv($saida, array('ID', 'SAIDA_ID', 'DATA', 'DEPARTAMENTO', 'PRODUTO', 'OBSERVAÇÃO'));
            $sqlDept = "select * from saidas where usu_id = $id";
            $linhas = mysqli_query($connect->connect(), $sqlDept);

            while ($linha = mysqli_fetch_assoc($linhas)) {
                fputcsv($saida, $linha);
            };
        } catch (Throwable $th) {
            return $th;
        }
    }

    function bkpChamados($id)
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');
            $nomeArq = 'bkpCham' . date('d/m/Y_H:i');

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $nomeArq . '.csv');

            $connect = new ConnectionDB();

            $saida = fopen('php://output', 'w');
            fputcsv($saida, array('ID', 'USUARIO_ID', 'DATA', 'PRODUTO', 'OBSERVAÇÃO', 'STATUS', 'DEPARTAMENTO'));
            $sqlDept = "select * from chamados where usu_id = $id";
            $linhas = mysqli_query($connect->connect(), $sqlDept);

            while ($linha = mysqli_fetch_assoc($linhas)) {
                fputcsv($saida, $linha);
            };

            return true;
        } catch (Throwable $th) {
            return $th;
        }
    }

    function deletarConta($id)
    {
        try {
            $connect = new ConnectionDB();

            $delDep = 'delete from departamentos where usu_id = ' . $id . '';
            $delSai = 'delete from saidas where usu_id = ' . $id . '';
            $delCha = 'delete from chamados where usu_id = ' . $id . '';
            $delUsu = 'delete from usuarios where usu_id = ' . $id . '';

            if (
                mysqli_query($connect->connect(), $delDep) &&
                mysqli_query($connect->connect(), $delSai) &&
                mysqli_query($connect->connect(), $delCha) &&
                mysqli_query($connect->connect(), $delUsu)
            ) {
                return true;
            } else {
                throw new Exception('Falha ao Excluir Conta!');
            }
        } catch (mysqli_sql_exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    function checarSenha($senha, $id)
    {
        try {
            $connect = new ConnectionDB();
            $usuario = new Usuario();
            $senha = mysqli_real_escape_string($connect->connect(), hash('sha512', $senha));
            $sql_getSenha = "select usu_senha from usuarios where usu_id = $id";
            $resultado = mysqli_query($connect->connect(), $sql_getSenha);
            $get = mysqli_fetch_array($resultado);

            $senhaBD = $get[0];

            if ($usuario->verificaSenha($senhaBD, $senha)) {
                return true;
            } else {
                throw new Exception("Senhas Divergentes! Tente Novamente...");
            }
        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
