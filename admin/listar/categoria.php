<?php
    if (!isset($pagina)) exit;
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listagem de Categorias</h5>
        <a href="cadastrar/categoria" class="btn btn-success btn-sm">Novo Registro</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "select * from categorias order by nome";
                    $consulta = $pdo->prepare($sql);
                    $consulta->execute();

                    $dadosCategorias = $consulta->fetchAll(PDO::FETCH_OBJ);

                    if (empty($dadosCategorias)) {
                        echo '<tr><td colspan="3" class="text-center text-muted">Nenhuma categoria cadastrada.</td></tr>';
                    }

                    foreach ($dadosCategorias as $dados) {
                        ?>
                        <tr>
                            <td><?= $dados->id ?></td>
                            <td><?= htmlspecialchars($dados->nome) ?></td>
                            <td>
                                <a href="cadastrar/categoria/<?= $dados->id ?>" class="btn btn-primary btn-sm">Editar</a>
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
        if (confirm("Deseja realmente excluir esta categoria?")) {
            location.href = "excluir/categoria/" + id;
        }
    }
</script>
