<?php
// Funções usadas pelo painel.
// Ficam aqui para não repetir o mesmo código em várias telas.


// ----- 1. formatação -----

// 24.9 vira "R$ 24,90"
function formatarMoeda($valor)
{
    return "R$ " . number_format($valor, 2, ",", ".");
}


// cor de cada tipo de produto (usada nas etiquetas das listagens)
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


// ----- 2. avisos na tela -----

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


// ----- 3. endereços do painel -----

// monta o endereço completo de uma tela, tipo
// /sistema_gestao/listar/produto
//
// isso é necessário porque o cabeçalho HTTP "Location" não entende a
// tag <base> do HTML: ele precisa do caminho a partir da raiz do site
function urlPainel($rota)
{
    $pasta = dirname($_SERVER["SCRIPT_NAME"]);

    return rtrim($pasta, "/") . "/" . $rota;
}


// guarda uma mensagem e volta para outra tela
// (é o que acontece depois de salvar ou excluir um registro)
function redirecionarCom($rota, $tipo, $texto)
{
    definirMensagem($tipo, $texto);
    header("Location: " . urlPainel($rota));
    exit;
}


// ----- 4. etiquetas e paginação -----

// etiqueta colorida da situação do estoque
// a situação (Normal, Crítico ou Esgotado) vem calculada da função
// fn_situacao_estoque, lá no banco
function pilulaEstoque($situacao, $estoque)
{
    if ($situacao == "Esgotado") {
        return '<span class="pilula pilula-vermelha">Esgotado</span>';
    }

    if ($situacao == "Crítico") {
        return '<span class="pilula pilula-amarela">' . $estoque . ' un.</span>';
    }

    return '<span class="pilula pilula-verde">' . $estoque . ' un.</span>';
}


// etiqueta colorida do status da venda
function pilulaStatus($status)
{
    if ($status == "Cancelada") {
        return '<span class="pilula pilula-vermelha">Cancelada</span>';
    }

    return '<span class="pilula pilula-verde">Concluída</span>';
}


// mostra os links das páginas (1, 2, 3...) embaixo de uma listagem
//
// $extra são os filtros que já estão na tela e precisam continuar
// valendo quando o usuário troca de página, tipo "busca=one&"
function mostrarPaginacao($rota, $paginaAtual, $totalPaginas, $extra = "")
{
    if ($totalPaginas <= 1) {
        return;
    }
    ?>
    <ul class="pagination pagination-sm mb-0">
        <?php for ($numero = 1; $numero <= $totalPaginas; $numero++) { ?>
            <li class="page-item <?= $numero == $paginaAtual ? "active" : "" ?>">
                <a class="page-link" href="<?= $rota ?>?<?= $extra ?>p=<?= $numero ?>"><?= $numero ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php
}
