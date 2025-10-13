<?php

    session_start();

    require_once 'conexao.php';

    function excluirTarefaDoBancoDados($pdo, $id){
        $sql = "DELETE FROM tarefas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    function main($pdo){
        if(isset($_GET['id'])){
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); 

            try{
                excluirTarefaDoBancoDados($pdo, $id);
                header("Location: tela_tarefas.php");
                exit();
            }catch(PDOException $e){
                erroLogInterno("Erro ao deletar produto: " . $e->getMessage() . "(excluir_produto.php)");
            }

        }
    }

    main($pdo);

?>