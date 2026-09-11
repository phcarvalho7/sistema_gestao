<?php
// Lista os produtos de uma categoria.

if (!isset($pdo)) {
    exit;
}

// busca o nome da categoria
$consultaCategoria = $pdo->prepare("select * from categorias where id = :id limit 1");
$consultaCategoria->bindValue(":id", $id, PDO::PARAM_INT);
$consultaCategoria->execute();
$categoria = $consultaCategoria->fetch(PDO::FETCH_OBJ);

if (!$categoria) {
    include "pages/erro.php";
    return;
}

// busca os produtos dessa categoria
$sql = "select p.*, c.nome as categoria
        from produtos p
        inner join categorias c on c.id = p.categoria_id
        where p.categoria_id = :id
        order by p.titulo";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$produtos = $consulta->fetchAll(PDO::FETCH_OBJ);
?>

<section class="container py-5">
    <h3>Categoria: <?= htmlspecialchars($categoria->nome) ?></h3>
    <p class="texto-mudo"><?= count($produtos) ?> título(s) nesta categoria</p>

    <div class="row g-3 mt-2">
        <?php if (count($produtos) == 0) { ?>
            <div class="col-12">
                <div class="aviso-vazio">
                    Nenhum produto cadastrado nesta categoria.
                    <a href="produtos">Ver o catálogo completo</a>.
                </div>
            </div>
        <?php } ?>

        <?php foreach ($produtos as $produto) {
            mostrarCardProduto($produto);
        } ?>
    </div>
</section>
