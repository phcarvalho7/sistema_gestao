<?php
// TEMPLATE - topo do painel (head, menu lateral e barra de cima).

// itens do menu lateral: endereço, nome e qual chave deixa o item aceso
$menu = array(
    array("rota" => "index.php",        "nome" => "Início",     "chave" => "home"),
    array("rota" => "listar/produto",   "nome" => "Produtos",   "chave" => "produto"),
    array("rota" => "listar/categoria", "nome" => "Categorias", "chave" => "categoria"),
    array("rota" => "listar/venda",     "nome" => "Vendas",     "chave" => "venda")
);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - <?= $tituloPagina ?></title>

    <base href="http://<?= $_SERVER["HTTP_HOST"] . rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/") ?>/">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/painel.css" rel="stylesheet">
</head>

<body>
    <!-- menu lateral -->
    <div class="menu-lateral">
        <a class="marca" href="index.php">
            Prosa <span class="marca-destaque">&amp; Traço</span>
            <small>Painel de gestão</small>
        </a>

        <?php foreach ($menu as $item) { ?>
            <a href="<?= $item["rota"] ?>" class="menu-item <?= $menuAtivo == $item["chave"] ? "ativo" : "" ?>">
                <?= $item["nome"] ?>
            </a>
        <?php } ?>

        <hr>

        <a href="cadastrar/venda" class="menu-item">Registrar venda</a>
        <a href="sair.php" class="menu-item texto-vermelho">Sair</a>
    </div>

    <!-- conteúdo -->
    <div class="area-conteudo">
        <div class="topo">
            <div>
                <h1 class="topo-titulo"><?= $tituloPagina ?></h1>
                <span class="texto-mudo texto-mini"><?= date("d/m/Y") ?></span>
            </div>

            <div class="text-end">
                <strong><?= htmlspecialchars($_SESSION["usuario"]["nome"]) ?></strong>
                <span class="texto-mudo texto-mini d-block"><?= htmlspecialchars($_SESSION["usuario"]["perfil"]) ?></span>
            </div>
        </div>

        <div class="corpo">
            <?php mostrarMensagem(); ?>
