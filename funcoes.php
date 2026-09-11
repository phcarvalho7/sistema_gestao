<?php
// Funções que a loja e o painel usam.
// Ficam aqui para não repetir o mesmo código em várias telas.


// 24.9 vira "R$ 24,90"
function formatarMoeda($valor)
{
    return "R$ " . number_format($valor, 2, ",", ".");
}


// cor de cada tipo de produto (usada na capa e nas etiquetas)
function corPorTipo($tipo)
{
    if ($tipo == "HQ") {
        return "#f04438";
    }

    if ($tipo == "Mangá") {
        return "#0ba5ec";
    }

    return "#6366f1"; // Livro
}


// as duas primeiras letras do título
// o projeto não tem upload de imagem, então a capa é feita com essas
// letras em cima da cor do tipo
function iniciaisTitulo($titulo)
{
    return mb_strtoupper(mb_substr($titulo, 0, 2));
}


// guarda um aviso na sessão para aparecer na tela seguinte
// $tipo pode ser success, danger, warning ou info
function definirMensagem($tipo, $texto)
{
    $_SESSION["mensagem"] = array("tipo" => $tipo, "texto" => $texto);
}


// mostra e apaga o aviso guardado na sessão
function mostrarMensagem()
{
    if (!isset($_SESSION["mensagem"])) {
        return;
    }

    $tipo = $_SESSION["mensagem"]["tipo"];
    $texto = $_SESSION["mensagem"]["texto"];

    // apaga para não aparecer de novo quando recarregar a página
    unset($_SESSION["mensagem"]);
    ?>
    <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($texto) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php
}


// a capa colorida do produto ($tamanho: "media" ou "grande")
function mostrarCapa($produto, $tamanho = "media")
{
    $classe = "capa";

    if ($tamanho == "grande") {
        $classe = "capa capa-grande";
    }
    ?>
    <div class="<?= $classe ?>" style="background-color: <?= corPorTipo($produto->tipo) ?>">
        <span class="capa-letras"><?= iniciaisTitulo($produto->titulo) ?></span>
        <span class="capa-tipo"><?= htmlspecialchars($produto->tipo) ?></span>
    </div>
    <?php
}


// o card do produto, usado na home, no catálogo, na busca e na categoria
function mostrarCardProduto($produto)
{
    ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card card-produto h-100">
            <a href="produto/<?= $produto->id ?>">
                <?php mostrarCapa($produto); ?>
            </a>
            <div class="card-body">
                <span class="etiqueta" style="background-color: <?= corPorTipo($produto->tipo) ?>">
                    <?= htmlspecialchars($produto->tipo) ?>
                </span>
                <h6 class="mt-2 mb-1"><?= htmlspecialchars($produto->titulo) ?></h6>
                <p class="texto-mudo texto-mini mb-2"><?= htmlspecialchars($produto->autor) ?></p>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong><?= formatarMoeda($produto->preco) ?></strong>

                    <?php if ($produto->estoque > 0) { ?>
                        <span class="pilula pilula-verde"><?= $produto->estoque ?> un.</span>
                    <?php } else { ?>
                        <span class="pilula pilula-vermelha">Esgotado</span>
                    <?php } ?>
                </div>

                <a href="produto/<?= $produto->id ?>" class="btn btn-suave btn-sm w-100">Ver detalhes</a>
            </div>
        </div>
    </div>
    <?php
}
