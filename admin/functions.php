<?php
// Funções usadas só pelo painel.
// As funções gerais (formatarMoeda, corPorTipo...) ficam em ../funcoes.php


// monta o endereço completo de uma tela do painel, tipo
// /sistema_gestao/admin/listar/produto
//
// isso é necessário porque o cabeçalho HTTP "Location" não entende a
// tag <base> do HTML: ele precisa do caminho a partir da raiz do site
function urlPainel($rota)
{
    $pasta = dirname($_SERVER["SCRIPT_NAME"]);

    return rtrim($pasta, "/") . "/" . $rota;
}

// guarda uma mensagem e volta para outra tela do painel
// (é o que acontece depois de salvar ou excluir um registro)
function redirecionarCom($rota, $tipo, $texto)
{
    definirMensagem($tipo, $texto);
    header("Location: " . urlPainel($rota));
    exit;
}

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
