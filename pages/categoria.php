<?php
    $sqlCategoria = "select * from categorias where id = :id limit 1";
    $consultaCategoria = $pdo->prepare($sqlCategoria);
    $consultaCategoria->bindParam(":id", $id);
    $consultaCategoria->execute();
    $categoria = $consultaCategoria->fetch(PDO::FETCH_OBJ);

    if (empty($categoria)) {
        include "pages/erro.php";
        return;
    }
?>
<div class="container my-5">
    <h3 class="mb-4">Categoria: <?= htmlspecialchars($categoria->nome) ?></h3>
    <div class="row g-3">
        <?php
            $sql = "select p.*, c.nome as categoria
            from produtos p
            inner join categorias c on c.id = p.categoria_id
            where p.categoria_id = :categoria_id
            order by p.titulo";
            $consulta = $pdo->prepare($sql);
            $consulta->bindParam(":categoria_id", $id);
            $consulta->execute();

            $dadosProdutos = $consulta->fetchAll(PDO::FETCH_OBJ);

            if (empty($dadosProdutos)) {
                echo '<p class="text-muted">Nenhum produto cadastrado nesta categoria.</p>';
            } else {
                foreach ($dadosProdutos as $produto) {
                    renderizarCardProduto($produto);
                }
            }
        ?>
    </div>
</div>
