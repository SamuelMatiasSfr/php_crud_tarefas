<?php
    session_start();

    $erros = isset($_SESSION['erros_cadastro']) ? 
    $_SESSION['erros_cadastro'] : [];
    $nome = isset($_SESSION['dados_cadastro']['nome']) ? 
    $_SESSION['dados_cadastro']['nome'] : '';
    $email = isset($_SESSION['dados_cadastro']['email']) ? 
    $_SESSION['dados_cadastro']['email'] : '';

    unset($_SESSION['erros_cadastro']);
    unset($_SESSION['dados_cadastro']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Tela de Produtos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
    <body>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="text-center mt-2">Tela de Cadastro</h3>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>                        
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                    
                <form action="processar_cadastro.php" method="POST">
                    <div class="card mb-3 mt-3">
                        <div class="form-group m-2">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" placeholder="Digite o seu nome">
                        </div>
                        <div class="form-group m-2">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Digite o seu email">
                        </div>
                        <div class="form-group m-2">
                            <label for="senha">Senha</label>
                            <input type="text" class="form-control" id="senha" name="senha" placeholder="Digite uma senha">
                        </div>
                    </div>
                    <div class="form-row m-2">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </div>
                        <div class="col">
                            <a href="login.php" class="btn btn-outline-secondary w-100 text-center d-block">Já está cadastrado? Faça login</a>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
    </body>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</html>