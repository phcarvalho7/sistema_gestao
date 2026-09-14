<?php
// Listagem de produtos.
//
// A busca, o filtro por tipo e a paginação NÃO são feitos no PHP: quem
// faz tudo isso é a stored procedure sp_listar_produtos, no banco.
// O PHP só executa o CALL e mostra o resultado.
//
// A procedure também traz duas colunas calculadas por funções do banco:
// situacao_estoque (fn_situacao_estoque) e faturamento
// (fn_faturamento_produto).

if (!isset($pdo)) {
    exit;
}

// ----- 1. filtros da tela -----
$busca = "";
$tipo = "";
$paginaAtual = 1;

if (isset($_GET["busca"])) {
    $busca = trim($_GET["busca"]);
}

if (isset($_GET["tipo"])) {
    $tipo = $_GET["tipo"];
}

if (isset($_GET["p"])) {
    $paginaAtual = (int) $_GET["p"];
}

if ($paginaAtual < 1) {
    $paginaAtual = 1;
}

$porPagina = 8;
$primeiroRegistro = ($paginaAtual - 1) * $porPagina;

// ----- 2. quantos produtos existem com esse filtro? -----
// chamo a procedure com um limite grande só para contar as linhas e
// saber quantas páginas vão existir
$consultaTotal = $pdo->prepare("call sp_listar_produtos(:busca, :tipo, 0, 1000, 0)");
$consultaTotal->bindValue(":busca", $busca);
$consultaTotal->bindValue(":tipo", $tipo);
$consultaTotal->execute();
$total = count($consultaTotal->fetchAll(PDO::FETCH_OBJ));
$consultaTotal->closeCursor();

$totalPaginas = ceil($total / $porPagina);

// ----- 3. agora a página atual -----
// a procedure recebe o limite e a posição inicial e devolve só os
// registros daquela página
$consulta = $pdo->prepare("call sp_listar_produtos(:busca, :tipo, 0, :limite, :inicio)");
$consulta->bindValue(":busca", $busca);
$consulta->bindValue(":tipo", $tipo);
$consulta->bindValue(":limite", $porPagina, PDO::PARAM_INT);
$consulta->bindValue(":inicio", $primeiroRegistro, PDO::PARAM_INT);
$consulta->execute();
$produtos = $consulta->fetchAll(PDO::FETCH_OBJ);
$consulta->closeCursor();
?>

<div class="card">
    <div class="cabecalho-card">
        <div>
            <h5 class="mb-0">Produtos</h5>
            <span class="texto-mudo texto-mini"><?= $total ?> registro(s)</span>
        </div>
        <a href="cadastrar/produto" class="btn btn-primario btn-sm">Novo produto</a>
    </div>

    <!-- filtros -->
    <form method="get" action="listar/produto" class="barra-filtros row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <label for="busca" class="form-label">Buscar por título ou autor</label>
            <input type="search" name="busca" id="busca" class="form-control"
                   value="<?= htmlspecialchars($busca) ?>">
        </div>
        <div class="col-6 col-md-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="">Todos</option>
                <option value="Livro" <?= $tipo == "Livro" ? "selected" : "" ?>>Livro</option>
                <option value="HQ" <?= $tipo == "HQ" ? "selected" : "" ?>>HQ</option>
                <option value="Mangá" <?= $tipo == "Mangá" ? "selected" : "" ?>>Mangá</option>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <button type="submit" class="btn btn-primario btn-sm">Filtrar</button>
            <a href="listar/produto" class="btn btn-contorno btn-sm">Limpar</a>
        </div>
    </form>

    <!-- tabela -->
    <div class="table-responsive">
        <table class="table tabela mb-0">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th class="text-end">Preço</th>
                    <th class="text-center">Estoque</th>
                    <th class="text-end">Faturamento</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($produtos) == 0) { ?>
                    <tr>
                        <td colspan="7" class="text-center texto-mudo py-4">
                            Nenhum produto encontrado.
                        </td>
                    </tr>
                <?php } ?>

                <?php foreach ($produtos as $produto) { ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($produto->titulo) ?></strong>
                            <span class="texto-mudo texto-mini d-block"><?= htmlspecialchars($produto->autor) ?></span>
                        </td>
                        <td>
                            <span class="etiqueta" style="background-color: <?= corPorTipo($produto->tipo) ?>">
                                <?= htmlspecialchars($produto->tipo) ?>
                            </span>
                        </td>
                        <td class="texto-mudo"><?= htmlspecialchars($produto->categoria) ?></td>
                        <td class="text-end"><?= formatarMoeda($produto->preco) ?></td>
                        <td class="text-center"><?= pilulaEstoque($produto->situacao_estoque, $produto->estoque) ?></td>
                        <td class="text-end texto-mudo"><?= formatarMoeda($produto->faturamento) ?></td>
                        <td class="text-end">
                            <a href="cadastrar/produto/<?= $produto->id ?>" class="btn btn-contorno btn-sm">Editar</a>
                            <a href="excluir/produto/<?= $produto->id ?>" class="btn btn-perigo btn-sm"
                               onclick="return confirm('Excluir o produto <?= htmlspecialchars($produto->titulo, ENT_QUOTES) ?>?')">
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
            <?php
                // mantém os filtros ao trocar de página
                $filtros = "busca=" . urlencode($busca) . "&tipo=" . urlencode($tipo) . "&";
                mostrarPaginacao("listar/produto", $paginaAtual, $totalPaginas, $filtros);
            ?>
        </div>
    <?php } ?>
</div>
