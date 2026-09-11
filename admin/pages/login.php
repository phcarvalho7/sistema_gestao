<?php
// Tela de login do painel.
//
// É uma página completa (tem o próprio <head>) porque não usa o menu
// lateral nem a barra de cima. Quem confere o e-mail e a senha é o
// admin/index.php.
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Entrar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="corpo-login">
    <div class="caixa-login">
        <div class="card p-4">
            <h4 class="mb-1">Entrar no painel</h4>
            <p class="texto-mudo texto-mini mb-4">Prosa &amp; Traço - Sistema de Gestão de Vendas</p>

            <?php mostrarMensagem(); ?>

            <form method="post" action="index.php">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primario w-100">Entrar</button>
            </form>

            <div class="alert alert-info texto-mini mt-4 mb-0">
                <strong>Acesso de teste</strong><br>
                admin@prosaetraco.com / 123456
            </div>

            <p class="text-center mt-3 mb-0">
                <a href="../" class="texto-mini">Voltar para a loja</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
