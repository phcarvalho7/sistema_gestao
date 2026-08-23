<?php
    include "config.php";
    include "funcoes.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraria Nerd - Livros, HQs e Mangás</title>

    <base href="http://<?= $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"] ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">📚 Livraria <span class="text-warning">Nerd</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produtos">Catálogo</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categorias
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                                $sqlCategoria = "select * from categorias order by nome";
                                $consultaCategoria = $pdo->prepare($sqlCategoria);
                                $consultaCategoria->execute();

                                $dadosCategorias = $consultaCategoria->fetchAll(PDO::FETCH_OBJ);

                                foreach ($dadosCategorias as $dados) {
                                    ?>
                                    <li><a class="dropdown-item" href="categoria/<?= $dados->id ?>"><?= $dados->nome ?></a></li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/" target="_blank">Painel Administrativo</a>
                    </li>
                </ul>
                <form class="d-flex" role="search" method="post" action="buscar">
                    <input class="form-control me-2" name="busca" type="search" placeholder="Título ou autor" aria-label="Search" />
                    <button class="btn btn-warning" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <main>
        <?php
            if (isset($_GET["param"])) {
                $param = explode("/", $_GET["param"]);
            }

            $page = $param[0] ?? "home";
            $id = $param[1] ?? NULL;

            $page = "pages/{$page}.php";

            if (file_exists($page)) include $page;
            else include "pages/erro.php";
        ?>
    </main>

    <footer class="bg-dark text-light p-4 mt-5">
        <p class="text-center mb-0">&copy; <?= date("Y") ?> Livraria Nerd - Sistema de Gestão de Vendas. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>
