<?php
// Formulário de venda (registro e edição).
//
// Para ficar simples, o formulário tem três linhas de item fixas.
// Basta deixar em branco as que não vão ser usadas: o salvar/venda.php
// ignora as linhas sem produto.

if (!isset($pdo)) {
    exit;
}

$clienteNome = "";
$statusVenda = "Concluída";
$itens = array();

// edição: busca a venda e os itens dela
if ($id > 0) {
    $consultaVenda = $pdo->prepare("select * from vendas where id = :id limit 1");
    $consultaVenda->bindValue(":id", $id, PDO::PARAM_INT);
    $consultaVenda->execute();
    $venda = $consultaVenda->fetch(PDO::FETCH_OBJ);

    if (!$venda) {
        redirecionarCom("listar/venda", "warning", "Venda não encontrada.");
    }

    $clienteNome = $venda->cliente_nome;
    $statusVenda = $venda->status;

    $consultaItens = $pdo->prepare(
        "select produto_id, quantidade from venda_itens where venda_id = :id"
    );
    $consultaItens->bindValue(":id", $id, PDO::PARAM_INT);
    $consultaItens->execute();
    $itens = $consultaItens->fetchAll(PDO::FETCH_OBJ);
}

// produtos que podem ser escolhidos
$consultaProdutos = $pdo->prepare("select id, titulo, preco, estoque from produtos order by titulo");
$consultaProdutos->execute();
$produtos = $consultaProdutos->fetchAll(PDO::FETCH_OBJ);

// quantas linhas de item o formulário vai ter (no mínimo três)
$totalLinhas = 3;

if (count($itens) > $totalLinhas) {
    $totalLinhas = count($itens);
}
?>

<div class="card">
    <div class="cabecalho-card">
        <h5 class="mb-0"><?= $id > 0 ? "Editar venda #" . $id : "Registrar venda" ?></h5>
        <a href="listar/venda" class="btn btn-contorno btn-sm">Voltar para a listagem</a>
    </div>

    <div class="p-4">
        <?php if (count($produtos) == 0) { ?>
            <div class="alert alert-warning mb-0">
                Cadastre um produto antes de registrar vendas.
            </div>
        <?php } else { ?>

            <form method="post" action="salvar/venda">
                <input type="hidden" name="id" value="<?= $id > 0 ? $id : "" ?>">

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label for="cliente_nome" class="form-label">Cliente *</label>
                        <input type="text" name="cliente_nome" id="cliente_nome" class="form-control" required
                               value="<?= htmlspecialchars($clienteNome) ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Concluída" <?= $statusVenda == "Concluída" ? "selected" : "" ?>>
                                Concluída (desconta do estoque)
                            </option>
                            <option value="Cancelada" <?= $statusVenda == "Cancelada" ? "selected" : "" ?>>
                                Cancelada (devolve ao estoque)
                            </option>
                        </select>
                    </div>
                </div>

                <h6>Itens da venda</h6>
                <p class="texto-mudo texto-mini">
                    Deixe em branco as linhas que não for usar. O preço usado é o preço atual do produto.
                </p>

                <table class="table tabela">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th style="width: 140px">Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($linha = 0; $linha < $totalLinhas; $linha++) {

                            // se a venda já tem um item nesta linha, ele vem preenchido
                            $produtoEscolhido = 0;
                            $quantidade = "";

                            if (isset($itens[$linha])) {
                                $produtoEscolhido = $itens[$linha]->produto_id;
                                $quantidade = $itens[$linha]->quantidade;
                            }
                            ?>
                            <tr>
                                <td>
                                    <select name="produto_id[]" class="form-select">
                                        <option value="">-- sem item --</option>
                                        <?php foreach ($produtos as $produto) { ?>
                                            <option value="<?= $produto->id ?>"
                                                <?= $produtoEscolhido == $produto->id ? "selected" : "" ?>>
                                                <?= htmlspecialchars($produto->titulo) ?>
                                                (<?= formatarMoeda($produto->preco) ?> - <?= $produto->estoque ?> em estoque)
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantidade[]" class="form-control" min="1"
                                           value="<?= $quantidade ?>">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <a href="listar/venda" class="btn btn-suave">Cancelar</a>
                    <button type="submit" class="btn btn-primario">Salvar venda</button>
                </div>
            </form>

        <?php } ?>
    </div>
</div>
