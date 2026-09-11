<?php
// Catálogo completo, com filtro por tipo, ordenação e paginação.

if (!isset($pdo)) {
    exit;
}

// ----- 1. lê os filtros que vieram no endereço -----
$tipo = "";
$ordem = "titulo";
$paginaAtual = 1;

if (isset($_GET["tipo"])) {
    $tipo = $_GET["tipo"];
}

if (isset($_GET["ordem"])) {
    $ordem = $_GET["ordem"];
}

if (isset($_GET["p"])) {
    $paginaAtual = (int) $_GET["p"];
}

if ($paginaAtual < 1) {
    $paginaAtual = 1;
}

$porPagina = 12;
$primeiroRegistro = ($paginaAtual - 1) * $porPagina;

// a ordenação vai direto na consulta, então só aceito os valores desta
// lista - nunca o que o usuário digitar no endereço
$ordenacoes = array(
    "titulo" => "p.titulo",
    "menor"  => "p.preco asc",
    "maior"  => "p.preco desc"
);

$ordenarPor = "p.titulo";

if (isset($ordenacoes[$ordem])) {
    $ordenarPor = $ordenacoes[$ordem];
}

// o filtro de tipo é opcional: só entra na consulta se foi escolhido
$filtroTipo = "";

if ($tipo != "") {
    $filtroTipo = "where p.tipo = :tipo";
}

// ----- 2. conta quantos produtos existem (para montar a paginação) -----
$sqlTotal = "select count(*) as total from produtos p {$filtroTipo}";
$consultaTotal = $pdo->prepare($sqlTotal);

if ($tipo != "") {
    $consultaTotal->bindValue(":tipo", $tipo);
}

$consultaTotal->execute();
$total = $consultaTotal->fetch(PDO::FETCH_OBJ)->total;

$totalPaginas = ceil($total / $porPagina);

if ($totalPaginas < 1) {
    $totalPaginas = 1;
}

// ----- 3. busca os produtos da página atual -----
$sql = "select p.*, c.nome as categoria
        from produtos p
        inner join categorias c on c.id = p.categoria_id
        {$filtroTipo}
        order by {$ordenarPor}
        limit {$porPagina} offset {$primeiroRegistro}";

$consulta = $pdo->prepare($sql);

if ($tipo != "") {
    $consulta->bindValue(":tipo", $tipo);
}

$consulta->execute();
$produtos = $consulta->fetchAll(PDO::FETCH_OBJ);
?>

<section class="container py-5">
    <h3>Catálogo</h3>
    <p class="texto-mudo"><?= $total ?> título(s) encontrado(s)</p>

    <!-- filtros -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="produtos" class="btn btn-sm <?= $tipo == "" ? "btn-primario" : "btn-contorno" ?>">Todos</a>
        <a href="produtos?tipo=Livro" class="btn btn-sm <?= $tipo == "Livro" ? "btn-primario" : "btn-contorno" ?>">Livros</a>
        <a href="produtos?tipo=HQ" class="btn btn-sm <?= $tipo == "HQ" ? "btn-primario" : "btn-contorno" ?>">HQs</a>
        <a href="produtos?tipo=Mangá" class="btn btn-sm <?= $tipo == "Mangá" ? "btn-primario" : "btn-contorno" ?>">Mangás</a>
    </div>

    <form method="get" action="produtos" class="row g-2 align-items-end mb-4">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">

        <div class="col-12 col-md-4">
            <label for="ordem" class="form-label">Ordenar por</label>
            <select name="ordem" id="ordem" class="form-select">
                <option value="titulo" <?= $ordem == "titulo" ? "selected" : "" ?>>Título (A-Z)</option>
                <option value="menor" <?= $ordem == "menor" ? "selected" : "" ?>>Menor preço</option>
                <option value="maior" <?= $ordem == "maior" ? "selected" : "" ?>>Maior preço</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-primario">Aplicar</button>
        </div>
    </form>

    <!-- lista -->
    <div class="row g-3">
        <?php if (count($produtos) == 0) { ?>
            <div class="col-12">
                <div class="aviso-vazio">
                    Nenhum produto encontrado com esse filtro.
                    <a href="produtos">Ver o catálogo completo</a>.
                </div>
            </div>
        <?php } ?>

        <?php foreach ($produtos as $produto) {
            mostrarCardProduto($produto);
        } ?>
    </div>

    <!-- paginação -->
    <?php if ($totalPaginas > 1) { ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($numero = 1; $numero <= $totalPaginas; $numero++) { ?>
                    <li class="page-item <?= $numero == $paginaAtual ? "active" : "" ?>">
                        <a class="page-link"
                           href="produtos?tipo=<?= urlencode($tipo) ?>&ordem=<?= $ordem ?>&p=<?= $numero ?>">
                            <?= $numero ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    <?php } ?>
</section>
