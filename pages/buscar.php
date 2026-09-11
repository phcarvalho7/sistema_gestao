<?php
// Resultado da busca por título ou autor.
// O formulário do menu envia por POST e o botão desta tela usa GET,
// por isso os dois são aceitos aqui.

if (!isset($pdo)) {
    exit;
}

$busca = "";

if (isset($_POST["busca"])) {
    $busca = trim($_POST["busca"]);
} else if (isset($_GET["busca"])) {
    $busca = trim($_GET["busca"]);
}

$produtos = array();

if ($busca != "") {
    // os % fazem o LIKE procurar o texto em qualquer parte do campo
    $termo = "%" . $busca . "%";

    $sql = "select p.*, c.nome as categoria
            from produtos p
            inner join categorias c on c.id = p.categoria_id
            where p.titulo like :termo or p.autor like :termo
            order by p.titulo";

    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(":termo", $termo);
    $consulta->execute();
    $produtos = $consulta->fetchAll(PDO::FETCH_OBJ);
}
?>

<section class="container py-5">
    <h3>Busca</h3>

    <form method="get" action="buscar" class="d-flex gap-2 mb-4">
        <input type="search" name="busca" class="form-control" value="<?= htmlspecialchars($busca) ?>"
               placeholder="Título ou autor">
        <button type="submit" class="btn btn-primario">Buscar</button>
    </form>

    <?php if ($busca != "") { ?>
        <p class="texto-mudo">
            <?= count($produtos) ?> resultado(s) para "<?= htmlspecialchars($busca) ?>"
        </p>
    <?php } ?>

    <div class="row g-3">
        <?php if ($busca == "") { ?>
            <div class="col-12">
                <div class="aviso-vazio">Digite um título ou o nome de um autor para buscar.</div>
            </div>
        <?php } else if (count($produtos) == 0) { ?>
            <div class="col-12">
                <div class="aviso-vazio">
                    Nada encontrado para "<?= htmlspecialchars($busca) ?>".
                    <a href="produtos">Ver o catálogo completo</a>.
                </div>
            </div>
        <?php } ?>

        <?php foreach ($produtos as $produto) {
            mostrarCardProduto($produto);
        } ?>
    </div>
</section>
