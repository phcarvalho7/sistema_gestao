<?php
// Histórico de vendas, com filtro por status e paginação.

if (!isset($pdo)) {
    exit;
}

// ----- 1. filtros da tela -----
$status = "";
$paginaAtual = 1;

if (isset($_GET["status"])) {
    $status = $_GET["status"];
}

if (isset($_GET["p"])) {
    $paginaAtual = (int) $_GET["p"];
}

if ($paginaAtual < 1) {
    $paginaAtual = 1;
}

$porPagina = 8;
$primeiroRegistro = ($paginaAtual - 1) * $porPagina;

// o filtro só entra na consulta se um status foi escolhido
$filtroStatus = "";

if ($status == "Concluída" || $status == "Cancelada") {
    $filtroStatus = "where v.status = :status";
}

// ----- 2. total de vendas (para a paginação) -----
$consultaTotal = $pdo->prepare("select count(*) as total from vendas v {$filtroStatus}");

if ($filtroStatus != "") {
    $consultaTotal->bindValue(":status", $status);
}

$consultaTotal->execute();
$total = $consultaTotal->fetch(PDO::FETCH_OBJ)->total;
$totalPaginas = ceil($total / $porPagina);

// ----- 3. vendas da página atual -----
// o usuario_id guarda quem registrou a venda no painel
$sql = "select v.*, u.nome as vendedor
        from vendas v
        left join usuarios u on u.id = v.usuario_id
        {$filtroStatus}
        order by v.data_venda desc
        limit {$porPagina} offset {$primeiroRegistro}";

$consulta = $pdo->prepare($sql);

if ($filtroStatus != "") {
    $consulta->bindValue(":status", $status);
}

$consulta->execute();
$vendas = $consulta->fetchAll(PDO::FETCH_OBJ);
?>

<div class="card">
    <div class="cabecalho-card">
        <div>
            <h5 class="mb-0">Vendas</h5>
            <span class="texto-mudo texto-mini"><?= $total ?> registro(s)</span>
        </div>
        <a href="cadastrar/venda" class="btn btn-primario btn-sm">Registrar venda</a>
    </div>

    <form method="get" action="listar/venda" class="barra-filtros row g-2 align-items-end">
        <div class="col-6 col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">Todos</option>
                <option value="Concluída" <?= $status == "Concluída" ? "selected" : "" ?>>Concluída</option>
                <option value="Cancelada" <?= $status == "Cancelada" ? "selected" : "" ?>>Cancelada</option>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <button type="submit" class="btn btn-primario btn-sm">Filtrar</button>
            <a href="listar/venda" class="btn btn-contorno btn-sm">Limpar</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table tabela mb-0">
            <thead>
                <tr>
                    <th>Venda</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Vendedor</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($vendas) == 0) { ?>
                    <tr>
                        <td colspan="7" class="text-center texto-mudo py-4">Nenhuma venda encontrada.</td>
                    </tr>
                <?php } ?>

                <?php foreach ($vendas as $venda) { ?>
                    <tr>
                        <td><strong>#<?= $venda->id ?></strong></td>
                        <td><?= htmlspecialchars($venda->cliente_nome) ?></td>
                        <td class="texto-mudo"><?= date("d/m/Y H:i", strtotime($venda->data_venda)) ?></td>
                        <td class="texto-mudo"><?= htmlspecialchars($venda->vendedor) ?></td>
                        <td class="text-end"><strong><?= formatarMoeda($venda->total) ?></strong></td>
                        <td class="text-center"><?= pilulaStatus($venda->status) ?></td>
                        <td class="text-end">
                            <a href="cadastrar/venda/<?= $venda->id ?>" class="btn btn-contorno btn-sm">Editar</a>
                            <a href="excluir/venda/<?= $venda->id ?>" class="btn btn-perigo btn-sm"
                               onclick="return confirm('Excluir a venda #<?= $venda->id ?>? O estoque dos itens será devolvido.')">
                                Excluir
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPaginas > 1) { ?>
        <div class="rodape-card">
            <span class="texto-mudo texto-mini">Página <?= $paginaAtual ?> de <?= $totalPaginas ?></span>
            <?php mostrarPaginacao("listar/venda", $paginaAtual, $totalPaginas, "status=" . urlencode($status) . "&"); ?>
        </div>
    <?php } ?>
</div>
