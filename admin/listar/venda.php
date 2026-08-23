<?php
    if (!isset($pagina)) exit;
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Histórico de Vendas</h5>
        <a href="cadastrar/venda" class="btn btn-success btn-sm">Registrar Venda</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "select * from vendas order by data_venda desc";
                    $consulta = $pdo->prepare($sql);
                    $consulta->execute();

                    $dadosVendas = $consulta->fetchAll(PDO::FETCH_OBJ);

                    if (empty($dadosVendas)) {
                        echo '<tr><td colspan="5" class="text-center text-muted">Nenhuma venda registrada.</td></tr>';
                    }

                    foreach ($dadosVendas as $dados) {
                        ?>
                        <tr>
                            <td>#<?= $dados->id ?></td>
                            <td><?= htmlspecialchars($dados->cliente_nome) ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($dados->data_venda)) ?></td>
                            <td><?= formatarMoeda($dados->total) ?></td>
                            <td>
                                <?php if ($dados->status == "Concluída") { ?>
                                    <span class="badge bg-success">Concluída</span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">Cancelada</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
