<?php

    session_start();

    require_once 'conexao.php';

    function validarCampos(&$erros, $email, $senha){
        if(empty($email) || empty($senha)){
            $erros[] = "Todos os campos são obrigatórios";
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erros[] = "Email é inválido";
        }
        if(strlen($senha) < 6){
            $erros[] = "A senha deve ter pelo menos 6 caracteres";
        }
    }

    function iniciarSessao(&$erros, $usuario, $senha){
        if($usuario && password_verify($senha, $usuario['senha'])){
            session_regenerate_id(true);
            
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];

            $nome = 'user_login';
            $valor_cookie = $usuario['email'];
            $expiracao = time() + (60 * 60 * 24 * 30);
            setcookie($nome, $valor_cookie, [
                'expires' => $expiracao,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            header("Location: tela_tarefas.php");
            exit;
        }else{
            $erros[] = "Senha incorreta";
        }
    }

    function verificarCadastroNoBancoDados(&$erros, $pdo, $email, $senha){
        if(empty($erros)){
            try{
                $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $email]);

                if($stmt->rowCount() > 0){
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    iniciarSessao($erros, $usuario, $senha);
                }else{
                    $erros[] = "Email incorreto";
                }
            }catch (PDOException $e){
                erroLogInterno("Erro ao verificar usuário: " . $e->getMessage());
            }
        }
    }

    function redirecionarParaLogin($erros, $email){
        if(!empty($erros)){
            $_SESSION['erros_login'] = $erros;
            $_SESSION['email_login'] = $email;
            header("Location: login.php");
            exit;
        }
    }

    function main($pdo){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $senha = $_POST['senha'];

            $erros = [];

            validarCampos($erros, $email, $senha);
            verificarCadastroNoBancoDados($erros, $pdo, $email, $senha);
            redirecionarParaLogin($erros, $email);
        }
    }

    main($pdo);

?>