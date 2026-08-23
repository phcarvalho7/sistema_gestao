<?php
    $sql = "select p.*, c.nome as categoria
    from produtos p
    inner join categorias c on c.id = p.categoria_id
    where p.id = :id limit 1";
    $consulta = $pdo->prepare($sql);
    $consulta->bindParam(":id", $id);
    $consulta->execute();

    $produto = $consulta->fetch(PDO::FETCH_OBJ);

    if (empty($produto)) {
        include "pages/erro.php";
        return;
    }
?>
<div class="container my-5">
    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="produto-capa produto-capa-grande" style="background-color: <?= corPorTipo($produto->tipo) ?>">
                <span><?= htmlspecialchars($produto->tipo) ?></span>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($produto->categoria) ?></span>
            <h2><?= htmlspecialchars($produto->titulo) ?></h2>
            <p class="text-muted">Autor(a): <?= htmlspecialchars($produto->autor) ?> &middot; Editora: <?= htmlspecialchars($produto->editora) ?></p>
            <h4 class="text-success">R$ <?= number_format($produto->preco, 2, ",", ".") ?></h4>
            <p><?= nl2br(htmlspecialchars($produto->sinopse)) ?></p>
            <?php if ($produto->estoque > 0) { ?>
                <span class="badge bg-success">Em estoque (<?= $produto->estoque ?> unid.)</span>
            <?php } else { ?>
                <span class="badge bg-danger">Fora de estoque</span>
            <?php } ?>
        </div>
    </div>
</div>
