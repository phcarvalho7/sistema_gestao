<?php
    // inicia a sessão do painel administrativo
    session_start();
    // conexão com o banco de dados
    include "../config.php";
    // funções auxiliares do painel (ex.: formatação de moeda)
    include "functions.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraria Nerd - Painel Administrativo</title>

    <base href="http://<?= $_SERVER["SERVER_NAME"] . dirname($_SERVER["SCRIPT_NAME"]) ?>/">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
        // Sem POST e sem sessão ativa -> mostra o login
        if ((!$_POST) and (!isset($_SESSION["livraria_nerd"]["id"]))) {
            include "pages/login.php";
        }
        // Recebeu POST e ainda não está logado -> tenta autenticar
        else if (($_POST) and (!isset($_SESSION["livraria_nerd"]["id"]))) {
            $email = trim($_POST["email"] ?? NULL);
            $senha = trim($_POST["senha"] ?? NULL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('E-mail inválido');</script>";
                include "pages/login.php";
            } else if (empty($senha)) {
                echo "<script>alert('Preencha a senha');</script>";
                include "pages/login.php";
            } else {
                $sql = "select * from usuarios where email = :email and ativo = 'Sim' limit 1";
                $consulta = $pdo->prepare($sql);
                $consulta->bindParam(":email", $email);
                $consulta->execute();

                $dadosUsuario = $consulta->fetch(PDO::FETCH_OBJ);

                if (empty($dadosUsuario->id)) {
                    echo "<script>alert('Usuário não encontrado ou inativo');</script>";
                    include "pages/login.php";
                } else if (!password_verify($senha, $dadosUsuario->senha)) {
                    echo "<script>alert('Senha inválida');</script>";
                    include "pages/login.php";
                } else {
                    $_SESSION["livraria_nerd"] = array(
                        "id" => $dadosUsuario->id,
                        "nome" => $dadosUsuario->nome,
                        "email" => $dadosUsuario->email
                    );
                    echo "<script>location.href='index.php'</script>";
                }
            }
        }
        // Sessão ativa -> mostra o painel
        else {
            ?>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php">📚 Livraria Nerd - Admin</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="listar/categoria">Categorias</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="listar/produto">Produtos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="listar/venda">Vendas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../" target="_blank">Ver Loja</a>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center text-light">
                            Olá, <strong class="mx-1"><?= htmlspecialchars($_SESSION["livraria_nerd"]["nome"]) ?></strong>
                            <a href="sair.php" class="btn btn-warning btn-sm ms-2">Sair</a>
                        </div>
                    </div>
                </div>
            </nav>
            <main class="container-fluid p-4">
                <?php
                    $param = $_GET["param"] ?? "pages/home";
                    $param = explode("/", $param);

                    $pasta = $param[0] ?? NULL;
                    $pagina = $param[1] ?? NULL;
                    $id = $param[2] ?? NULL;

                    $pagina = "{$pasta}/{$pagina}.php";

                    if (file_exists($pagina)) {
                        include $pagina;
                    } else {
                        include "pages/erro.php";
                    }
                ?>
            </main>
            <?php
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
