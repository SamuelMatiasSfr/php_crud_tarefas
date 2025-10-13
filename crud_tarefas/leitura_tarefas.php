<?php

    require_once 'conexao.php';

    function lerTarefasDoBancoDados($pdo, $id_usuario){
        $sql = "SELECT id, descricao, data_cadastro, data_entrega FROM tarefas
        WHERE id_usuario = :id_usuario ORDER BY data_entrega ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function main($pdo){
        $tarefas = [];

        try{
            $tarefas = lerTarefasDoBancoDados($pdo, $_SESSION['usuario_id']);
        }catch(PDOException $e){

        }
        return $tarefas;
    }

    $tarefas = main($pdo);

?>