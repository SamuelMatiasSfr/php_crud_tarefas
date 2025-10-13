<?php

    session_start();

    require_once 'conexao.php';

    function validarCampos(&$erros, $id, $descricao, $data_entrega){
        if ($id <= 0 || filter_var($id, FILTER_VALIDATE_INT) === false) {
            $erros[] = "O ID é inválido";
        }
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

    function retornarTarefaDoBancoDados($pdo, $id, &$tarefa_edicao){
        try {
            $sql = "SELECT * FROM tarefas WHERE id = :id";
            $stmt= $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                $tarefa_edicao = $stmt->fetch(PDO::FETCH_ASSOC);
            } 
        } catch (PDOException $e) {
            erroLogInterno("Erro ao buscar tarefa: " . $e->getMessage());
        }
    }

    function verificaSeHouveAtualizacao($tarefa_edicao, $descricao, $data_entrega){
        $houve_alteracao = false;
        if(
            $tarefa_edicao['descricao'] != $descricao ||
            $tarefa_edicao['data_entrega'] != $data_entrega
        ) {
            $houve_alteracao = true;
        }
        return $houve_alteracao;
    }

    function atualizarTarefaNoBancoDados($pdo, $id, $descricao, $data_entrega){
        try {
            $sql = "UPDATE tarefas SET
                    descricao = :descricao,
                    data_entrega = :data_entrega
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':data_entrega', $data_entrega);

            $stmt->execute();

            if($stmt->rowCount() > 0){
                $_SESSION['mensagem_atualizacao'] = ["Tarefa atualizado com sucesso"];
                $_SESSION['tipo_mensagem'] = "success";
                header("Location: tela_tarefas.php?id=$id");
                exit();
            }else{
                $_SESSION['mensagem_atualizacao'] = ["Erro: Nenhuma linha foi atualizada"];
                $_SESSION['tipo_mensagem'] = "danger";
                header("Location: tela_tarefas.php?id=$id");
                exit();
            }

        } catch (PDOException $e) {
            $_SESSION['mensagem_atualizacao'] = ["Erro interno: Tarefa não atualizado"];
            $_SESSION['tipo_mensagem'] = "danger";
            erroLogInterno("Erro ao atualizar o tarefa: " . $e->getMessage());
            header("Location: tela_tarefas.php?id=$id");
            exit();
        }
    }

    function armazenarErrosNaSessao($erros, $id){
        if(!empty($erros)){
            $_SESSION['mensagem_atualizacao'] = $erros;
            $_SESSION['tipo_mensagem'] = 'danger';
            header("Location: tela_tarefas.php?id=$id");
            exit();
        }
    }

    function main($pdo){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); 
            $descricao = htmlspecialchars(trim($_POST['descricao']), ENT_QUOTES, 'UTF-8');
            $data_entrega = trim($_POST['data_entrega']);

            $erros = [];
            validarCampos($erros, $id, $descricao, $data_entrega);
            armazenarErrosNaSessao($erros, $id);

            if(empty($erros)){
                $tarefa_edicao = null;
                retornarTarefaDoBancoDados($pdo, $id, $tarefa_edicao);

                if(!$tarefa_edicao){
                    $_SESSION['mensagem_atualizacao'] = ['Erro: Tarefa não encontrado'];
                    $_SESSION['tipo_mensagem'] = 'danger';
                    header("Location: tela_tarefas.php");
                    exit();
                }

                if(!verificaSeHouveAtualizacao($tarefa_edicao, $descricao, $data_entrega)){
                    $_SESSION['mensagem_atualizacao'] = ['Aviso: Nenhuma alteração foi feita'];
                    $_SESSION['tipo_mensagem'] = 'warning';
                    header("Location: tela_tarefas.php?id=$id");
                    exit();
                }else{
                    atualizarTarefaNoBancoDados($pdo, $id, $descricao, $data_entrega);
                }

            }

        }
    }

    main($pdo);

?>