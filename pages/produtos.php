<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h3 class="mb-0">Catálogo Completo</h3>
        <form class="d-flex gap-2" method="get" action="produtos">
            <select name="tipo" class="form-select" onchange="this.form.submit()">
                <option value="">Todos os tipos</option>
                <?php foreach (["Livro", "HQ", "Mangá"] as $tipoOpcao) { ?>
                    <option value="<?= $tipoOpcao ?>" <?= (($_GET["tipo"] ?? "") == $tipoOpcao) ? "selected" : "" ?>>
                        <?= $tipoOpcao ?>
                    </option>
                <?php } ?>
            </select>
        </form>
    </div>

    <div class="row g-3">
        <?php
            $tipoFiltro = $_GET["tipo"] ?? NULL;

            $sql = "select p.*, c.nome as categoria
            from produtos p
            inner join categorias c on c.id = p.categoria_id";

            if (!empty($tipoFiltro)) {
                $sql .= " where p.tipo = :tipo";
            }

            $sql .= " order by p.titulo";

            $consulta = $pdo->prepare($sql);

            if (!empty($tipoFiltro)) {
                $consulta->bindParam(":tipo", $tipoFiltro);
            }

            $consulta->execute();

            $dadosProdutos = $consulta->fetchAll(PDO::FETCH_OBJ);

            if (empty($dadosProdutos)) {
                echo '<p class="text-muted">Nenhum produto encontrado.</p>';
            } else {
                foreach ($dadosProdutos as $produto) {
                    renderizarCardProduto($produto);
                }
            }
        ?>
    </div>
</div>
