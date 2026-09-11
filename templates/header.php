<?php
// TEMPLATE - topo de todas as páginas da loja (head, menu e busca).
// Como o index.php inclui este arquivo em todas as telas, para mudar
// o menu do site inteiro basta mexer aqui.

// categorias que aparecem no menu
$sqlMenu = "select id, nome from categorias order by nome";
$consultaMenu = $pdo->prepare($sqlMenu);
$consultaMenu->execute();
$categoriasMenu = $consultaMenu->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prosa &amp; Traço - <?= $tituloPagina ?></title>

    <!-- a tag base faz os endereços amigáveis (produto/5) funcionarem -->
    <base href="http://<?= $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- MENU -->
    <nav class="navbar navbar-expand-lg navbar-loja">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                Prosa <span class="marca-destaque">&amp; Traço</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produtos">Catálogo</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Categorias
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categoriasMenu as $categoria) { ?>
                                <li>
                                    <a class="dropdown-item" href="categoria/<?= $categoria->id ?>">
                                        <?= htmlspecialchars($categoria->nome) ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>

                <form class="d-flex me-2" method="post" action="buscar">
                    <input class="form-control me-2" type="search" name="busca" placeholder="Título ou autor">
                    <button class="btn btn-primario" type="submit">Buscar</button>
                </form>

                <a href="admin/" target="_blank" class="btn btn-contorno">Painel</a>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION["mensagem"])) { ?>
        <div class="container mt-3">
            <?php mostrarMensagem(); ?>
        </div>
    <?php } ?>

    <main>
