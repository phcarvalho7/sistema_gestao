<?php
/**
 * Retorna uma cor de destaque de acordo com o tipo do produto,
 * usada no selo/placeholder de capa (Livro, HQ ou Mangá).
 */
function corPorTipo($tipo) {
    switch ($tipo) {
        case "HQ":
            return "#e63946";
        case "Mangá":
            return "#457b9d";
        default: // Livro
            return "#2a9d8f";
    }
}

/**
 * Monta o card visual de um produto (usado no catálogo e na home).
 * Como o projeto ainda não trabalha upload de capas reais,
 * é exibido um selo colorido com o tipo do produto.
 */
function renderizarCardProduto($produto) {
    $cor = corPorTipo($produto->tipo);
    ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card produto-card h-100 shadow-sm">
            <div class="produto-capa" style="background-color: <?= $cor ?>">
                <span><?= htmlspecialchars($produto->tipo) ?></span>
            </div>
            <div class="card-body d-flex flex-column">
                <span class="badge bg-secondary align-self-start mb-2"><?= htmlspecialchars($produto->categoria ?? "") ?></span>
                <h6 class="card-title mb-1"><?= htmlspecialchars($produto->titulo) ?></h6>
                <p class="text-muted small mb-2"><?= htmlspecialchars($produto->autor) ?></p>
                <p class="fw-bold text-success mt-auto mb-2">R$ <?= number_format($produto->preco, 2, ",", ".") ?></p>
                <a href="produto/<?= $produto->id ?>" class="btn btn-outline-dark btn-sm">Ver detalhes</a>
            </div>
        </div>
    </div>
    <?php
}
