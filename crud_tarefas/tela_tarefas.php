<?php

    session_start();

    if (!isset($_SESSION['usuario_logado'])) {
        header("Location: login.php");
        exit;
    }

    require_once 'leitura_tarefas.php';

    function retornarTarefaDoBancoDados($pdo, $id_edicao, &$tarefa_edicao){
        $sql = "SELECT * FROM tarefas WHERE id = :id";
        $stmt= $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_edicao, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $tarefa_edicao = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    $tarefa_edicao = null;
    $modo_edicao = false;
    $modo_cadastro = true;

    if(isset($_GET['id']) && !empty($_GET['id'])){
        $id_edicao = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        retornarTarefaDoBancoDados($pdo, $id_edicao, $tarefa_edicao);
    }

    if($tarefa_edicao){
        $modo_edicao = true;
        $modo_cadastro = false;
    }

    $mensagens = isset($_SESSION['erros_cadastro_tarefa']) ? 
    $_SESSION['erros_cadastro_tarefa'] : [];
    $tipo = 'danger';

    $descricao = isset($_SESSION['descricao_tarefa']) ? 
    $_SESSION['descricao_tarefa'] : '';

    if(isset($_SESSION['mensagem_atualizacao'])){
        $mensagens = is_array($_SESSION['mensagem_atualizacao']) ? 
        $_SESSION['mensagem_atualizacao'] : [$_SESSION['mensagem_atualizacao']];
        $tipo = isset($_SESSION['tipo_mensagem']) ?
        $_SESSION['tipo_mensagem'] : 'info';
    }

    unset($_SESSION['erros_cadastro_tarefa']);
    unset($_SESSION['mensagem_atualizacao']);
    unset($_SESSION['tipo_mensagem']);
    unset($_SESSION['descricao_tarefa']);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Tela de Tarefas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
    <body>
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="col mt-3 mb-3 d-flex justify-content-md-center align-items-center">
                    <h3 class="text-dark mt-2"><strong>Bem-vindo <?php echo $_SESSION['usuario_nome']; ?>!</strong></h3>
                    <a href="logout.php" class="btn btn-dark ml-5">Sair</a>
                </div>

                <h3 class="text-center text-secondary mt-2"> <?php echo $modo_edicao ? "Atualização de Tarefa" : "Cadastro de Tarefa"; ?> </h3>

                <?php if (!empty($mensagens)): ?>
                    <div class="alert alert-<?php echo $tipo; ?> d-flex justify-content-between align-items-center" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($mensagens as $mensagem): ?>
                                <li><?php echo htmlspecialchars($mensagem); ?></li>
                            <?php endforeach; ?>
                        </ul>                        
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                                    
                <form method="POST" action="<?php echo $modo_edicao ? 'atualizar_tarefa.php' : 'cadastrar_tarefa.php'; ?>">
                    <div class="card mb-3 mt-3">
                        <?php if ($modo_edicao): ?>
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($tarefa_edicao['id']); ?>">
                        <?php endif; ?>
                        <div class="form-group m-2">
                            <label for="descricao">Descrição da Tarefa</label>
                            <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo $modo_edicao ? htmlspecialchars($tarefa_edicao['descricao']) : ($modo_cadastro ? htmlspecialchars($descricao) : ''); ?>" placeholder="Informe a descrição da tarefa">
                        </div>
                        <div class="form-group m-2">
                            <label for="data_entrega">Data da Tarefa</label>
                            <input type="date" class="form-control" id="data_entrega" name="data_entrega" value="<?php echo $modo_edicao ? htmlspecialchars($tarefa_edicao['data_entrega']) : ''; ?>" >
                        </div>
                    </div>
                    <div class="form-row m-2">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100"><?php echo $modo_edicao ? "Atualizar Tarefa" : "Cadastrar Tarefa"; ?></button>
                        </div>
                        <div class="col">
                            <?php if($modo_edicao): ?>
                                <a href="tela_tarefas.php" class="btn btn-outline-secondary flex-grow-1 w-100">Voltar ao Modo Cadastro</a>
                            <?php else: ?>
                                <button type="reset" class="btn btn-secondary flex-grow-1 w-100">Limpar Campos</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>  
                
                <div class="col mt-3 text-center">
                    <?php if (empty($tarefas)) { ?>
                        <div class="alert alert-warning mt-4 text-center fs-5 col-8 mx-auto" role="alert">
                            Nenhum tarefa cadastrada
                        </div>
                    <?php } else { ?> 
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Data de Cadastro</th>
                                    <th scope="col">Data de Entrega</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($tarefas as $tarefa){ 
                                        echo "<tr>";
                                            echo "<td>".$tarefa['id']."</td>";
                                            echo "<td>".htmlspecialchars($tarefa['descricao'])."</td>";
                                            echo "<td>".$tarefa['data_cadastro']."</td>";
                                            echo "<td>".$tarefa['data_entrega']."</td>";                                        
                                            echo "<td>";
                                                echo "<a href='tela_tarefas.php?id={$tarefa['id']}' class='btn btn-warning'>Atualizar</a> ";
                                                echo "<a href='excluir_tarefa.php?id={$tarefa['id']}' class='btn btn-danger' onclick=\"return confirm('Tem certeza que deseja excluir esta tarefa?');\">Excluir</a>";
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</html>