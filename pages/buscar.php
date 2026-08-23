<?php
    $busca = trim($_POST["busca"] ?? "");
?>
<div class="container my-5">
    <h3 class="mb-4">Resultados para: "<?= htmlspecialchars($busca) ?>"</h3>
    <div class="row g-3">
        <?php
            if (empty($busca)) {
                echo '<p class="text-muted">Digite um termo para buscar.</p>';
            } else {
                $termo = "%{$busca}%";

                $sql = "select p.*, c.nome as categoria
                from produtos p
                inner join categorias c on c.id = p.categoria_id
                where p.titulo like :termo or p.autor like :termo
                order by p.titulo";
                $consulta = $pdo->prepare($sql);
                $consulta->bindParam(":termo", $termo);
                $consulta->execute();

                $dadosProdutos = $consulta->fetchAll(PDO::FETCH_OBJ);

                if (empty($dadosProdutos)) {
                    echo '<p class="text-muted">Nenhum produto encontrado.</p>';
                } else {
                    foreach ($dadosProdutos as $produto) {
                        renderizarCardProduto($produto);
                    }
                }
            }
        ?>
    </div>
</div>
