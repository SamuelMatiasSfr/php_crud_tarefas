<?php

    session_start(); 

    require_once 'conexao.php';

    function validarCampos(&$erros, $descricao, $data_entrega){
        if (empty($descricao) || empty($data_entrega)) {
            $erros[] = "Todos os campos são obrigatórios";
        }
        if (is_numeric($descricao)){
            $erros[] = "Descrição inválida";
        }
        if(strlen($descricao) < 2){
            $erros[] = "Descrição deve ter acima de 2 letras";
        }
        if(!filter_var($data_entrega, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^\d{4}-\d{2}-\d{2}$/"]])){
            $erros[] = "Data inválida";
        }
    }

    function cadastrarTarefaNoBancoDados(&$erros, $pdo, $descricao, $data_entrega, $id_usuario){
        if(empty($erros)){
            try{
                $sql = "INSERT INTO tarefas(descricao, data_entrega, id_usuario)
                VALUES (:descricao, :data_entrega, :id_usuario)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'descricao' => $descricao,
                    'data_entrega' => $data_entrega,
                    'id_usuario' => $id_usuario
                ]);
                header("Location: tela_tarefas.php");
                exit;
            }catch (PDOException $e){
                erroLogInterno("Erro ao cadastrar tarefa: ".$e->getMessage());
            }
        }
    }

    function armazenarDadosNaSessao($erros, $descricao){
        if(!empty($erros)){
            $_SESSION['erros_cadastro_tarefa'] = $erros;
            $_SESSION['descricao_tarefa'] = $descricao;
            header("Location: tela_tarefas.php");
            exit;
        }
    }

    function main($pdo){
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_logado'])){
            $descricao = htmlspecialchars(trim($_POST['descricao']), ENT_QUOTES, 'UTF-8');
            $data_entrega = trim($_POST['data_entrega']);

            $erros = [];

            validarCampos($erros, $descricao, $data_entrega);
            cadastrarTarefaNoBancoDados($erros, $pdo, $descricao, $data_entrega, $_SESSION['usuario_id']);
            armazenarDadosNaSessao($erros, $descricao);
        }
    }

    main($pdo);

?>