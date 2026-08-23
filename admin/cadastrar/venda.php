<?php
    if (!isset($pagina)) exit;

    $sqlProdutos = "select id, titulo, preco, estoque from produtos where estoque > 0 order by titulo";
    $consultaProdutos = $pdo->prepare($sqlProdutos);
    $consultaProdutos->execute();
    $dadosProdutosDisponiveis = $consultaProdutos->fetchAll(PDO::FETCH_OBJ);
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Registrar Venda</h5>
        <a href="listar/venda" class="btn btn-primary btn-sm">Listar Vendas</a>
    </div>
    <div class="card-body">
        <form name="formVenda" method="post" action="salvar/venda" id="formVenda">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label for="cliente_nome" class="form-label">Cliente:</label>
                    <input type="text" name="cliente_nome" id="cliente_nome" class="form-control" required>
                </div>
            </div>

            <table class="table table-bordered align-middle" id="tabelaItens">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th style="width: 120px">Quantidade</th>
                        <th style="width: 140px">Preço Unit.</th>
                        <th style="width: 60px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="linha-item">
                        <td>
                            <select name="produto_id[]" class="form-control select-produto" required>
                                <option value="">Selecione um produto</option>
                                <?php foreach ($dadosProdutosDisponiveis as $p) { ?>
                                    <option value="<?= $p->id ?>" data-preco="<?= $p->preco ?>" data-estoque="<?= $p->estoque ?>">
                                        <?= htmlspecialchars($p->titulo) ?> (estoque: <?= $p->estoque ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="number" name="quantidade[]" class="form-control input-quantidade" min="1" value="1" required></td>
                        <td><input type="text" class="form-control input-preco" readonly></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-remover">&times;</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAdicionarItem">+ Adicionar Item</button>

            <hr>
            <button type="submit" class="btn btn-success float-end">Finalizar Venda</button>
        </form>
    </div>
</div>

<script>
    // Preenche o preço automaticamente ao escolher um produto
    document.addEventListener("change", function (evento) {
        if (evento.target.classList.contains("select-produto")) {
            const linha = evento.target.closest("tr");
            const opcaoSelecionada = evento.target.selectedOptions[0];
            const preco = opcaoSelecionada ? opcaoSelecionada.dataset.preco : "";
            linha.querySelector(".input-preco").value = preco ? "R$ " + parseFloat(preco).toFixed(2) : "";
        }
    });

    // Remove uma linha de item (mantendo ao menos uma)
    document.getElementById("tabelaItens").addEventListener("click", function (evento) {
        if (evento.target.classList.contains("btn-remover")) {
            const linhas = document.querySelectorAll(".linha-item");
            if (linhas.length > 1) {
                evento.target.closest("tr").remove();
            }
        }
    });

    // Adiciona uma nova linha de item, clonando a primeira
    document.getElementById("btnAdicionarItem").addEventListener("click", function () {
        const tabela = document.querySelector("#tabelaItens tbody");
        const novaLinha = tabela.querySelector(".linha-item").cloneNode(true);
        novaLinha.querySelector(".select-produto").value = "";
        novaLinha.querySelector(".input-quantidade").value = 1;
        novaLinha.querySelector(".input-preco").value = "";
        tabela.appendChild(novaLinha);
    });
</script>
