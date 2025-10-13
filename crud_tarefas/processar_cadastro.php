<?php

    session_start();

    require_once 'conexao.php';

    function validarCampos(&$erros, $nome, $email, $senha){
        if(empty($nome) || empty($email) || empty($senha)){
            $erros[] = "Todos os campos são obrigatórios";
        }
        if(is_numeric($nome) || strlen($nome) < 2){
            $erros[] = "Nome inválido";
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erros[] = "Email é inválido";
        }
        if(strlen($senha) < 6){
            $erros[] = "A senha deve ter pelo menos 6 caracteres";
        }
    }

    function verificarSeEmailEstaCadastrado(&$erros, $pdo, $email){
        if(empty($erros)){
            try{
                $sql = "SELECT id FROM usuarios WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $email]);
                if($stmt->rowCount() > 0){
                    $erros[] = "Este email já está cadastrado";
                }
            }catch (PDOException $e){
                erroLogInterno("Erro ao verificar email: " . $e->getMessage());
            }
        }
    }

    function cadastrarUsuarioNoBancoDados(&$erros, $pdo, $nome, $email, $senha){
        if(empty($erros)){
            try{
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha_hash
                ]);
                header("Location: login.php?sucesso=1");
                exit;
            }catch (PDOExceeption $e){
                erroLogInterno("Erro ao cadastrar usuário: " . $e->getMessage());
            }
        }
    }

    function armazenarNaSessao($erros, $nome, $email){
        if(!empty($erros)){
            $_SESSION['erros_cadastro'] = $erros;
            $_SESSION['dados_cadastro'] = [
                'nome' => $nome,
                'email' => $email
            ];
            header("Location: cadastro.php");
            exit;
        }
    }

    function main($pdo){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nome = htmlspecialchars(trim($_POST['nome']), ENT_QUOTES, 'UTF-8');
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $senha = $_POST['senha'];

            $erros = [];

            validarCampos($erros, $nome, $email, $senha);
            verificarSeEmailEstaCadastrado($erros, $pdo, $email);
            cadastrarUsuarioNoBancoDados($erros, $pdo, $nome, $email, $senha);
            armazenarNaSessao($erros, $nome, $email);
        }
    }

    main($pdo);

?>