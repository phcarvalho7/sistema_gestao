<?php
    if (!isset($pagina)) exit;
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listagem de Produtos</h5>
        <a href="cadastrar/produto" class="btn btn-success btn-sm">Novo Registro</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "select p.*, c.nome as categoria
                    from produtos p
                    inner join categorias c on c.id = p.categoria_id
                    order by p.titulo";
                    $consulta = $pdo->prepare($sql);
                    $consulta->execute();

                    $dadosProdutos = $consulta->fetchAll(PDO::FETCH_OBJ);

                    if (empty($dadosProdutos)) {
                        echo '<tr><td colspan="7" class="text-center text-muted">Nenhum produto cadastrado.</td></tr>';
                    }

                    foreach ($dadosProdutos as $dados) {
                        ?>
                        <tr>
                            <td><?= $dados->id ?></td>
                            <td><?= htmlspecialchars($dados->titulo) ?></td>
                            <td><span class="badge bg-info text-dark"><?= $dados->tipo ?></span></td>
                            <td><?= htmlspecialchars($dados->categoria) ?></td>
                            <td><?= formatarMoeda($dados->preco) ?></td>
                            <td>
                                <?php if ($dados->estoque <= 5) { ?>
                                    <span class="badge bg-danger"><?= $dados->estoque ?></span>
                                <?php } else { ?>
                                    <?= $dados->estoque ?>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="cadastrar/produto/<?= $dados->id ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="javascript:excluir(<?= $dados->id ?>)" class="btn btn-danger btn-sm">Excluir</a>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function excluir(id) {
        if (confirm("Deseja realmente excluir este produto?")) {
            location.href = "excluir/produto/" + id;
        }
    }
</script>
