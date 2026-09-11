<?php
// Detalhes de um produto.
//
// A situação do estoque (Normal, Crítico ou Esgotado) não é calculada no
// PHP: ela vem da função fn_situacao_estoque, chamada dentro do SELECT.

if (!isset($pdo)) {
    exit;
}

$sql = "select p.*, c.nome as categoria,
               fn_situacao_estoque(p.estoque, p.estoque_minimo) as situacao
        from produtos p
        inner join categorias c on c.id = p.categoria_id
        where p.id = :id
        limit 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$produto = $consulta->fetch(PDO::FETCH_OBJ);

// produto não encontrado: mostra a tela de erro e para aqui
if (!$produto) {
    include "pages/erro.php";
    return;
}
?>

<section class="container py-5">
    <p class="texto-mudo texto-mini">
        <a href="index.php">Início</a> /
        <a href="produtos">Catálogo</a> /
        <a href="categoria/<?= $produto->categoria_id ?>"><?= htmlspecialchars($produto->categoria) ?></a>
    </p>

    <div class="card">
        <div class="row g-0">
            <div class="col-12 col-md-4 p-4">
                <?php mostrarCapa($produto, "grande"); ?>
            </div>

            <div class="col-12 col-md-8 p-4">
                <span class="etiqueta" style="background-color: <?= corPorTipo($produto->tipo) ?>">
                    <?= htmlspecialchars($produto->tipo) ?>
                </span>

                <h2 class="mt-2"><?= htmlspecialchars($produto->titulo) ?></h2>
                <p class="texto-mudo">
                    <?= htmlspecialchars($produto->autor) ?> &middot; <?= htmlspecialchars($produto->editora) ?>
                </p>

                <h3 class="preco-grande"><?= formatarMoeda($produto->preco) ?></h3>

                <p>
                    <?php if ($produto->situacao == "Esgotado") { ?>
                        <span class="pilula pilula-vermelha">Esgotado</span>
                    <?php } else if ($produto->situacao == "Crítico") { ?>
                        <span class="pilula pilula-amarela">Últimas <?= $produto->estoque ?> unidades</span>
                    <?php } else { ?>
                        <span class="pilula pilula-verde"><?= $produto->estoque ?> em estoque</span>
                    <?php } ?>
                </p>

                <h6>Sinopse</h6>
                <p class="texto-corpo"><?= nl2br(htmlspecialchars($produto->sinopse)) ?></p>

                <a href="produtos?tipo=<?= urlencode($produto->tipo) ?>" class="btn btn-primario">
                    Ver outros <?= htmlspecialchars($produto->tipo) ?>s
                </a>
                <a href="produtos" class="btn btn-suave">Voltar ao catálogo</a>
            </div>
        </div>
    </div>
</section>
