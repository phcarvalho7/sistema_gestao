<?php
// API da dashboard.
//
// Este arquivo não devolve HTML: devolve JSON. E ele NÃO CALCULA NADA.
// Quem faz a busca, o filtro e a paginação são as stored procedures do
// banco; quem calcula os indicadores é o TypeScript, no navegador.
//
// Dá para abrir direto no navegador para ver o JSON:
//   apis/dashboard.php
//   apis/dashboard.php?busca=one&inicio=2026-01-01

// avisa o navegador que a resposta é JSON, e não HTML
header("Content-Type: application/json; charset=utf-8");

require "../config.php";

// ----- 1. filtros recebidos no endereço -----
$busca = "";
$inicio = null;

if (isset($_GET["busca"])) {
    $busca = trim($_GET["busca"]);
}

if (isset($_GET["inicio"]) && $_GET["inicio"] != "") {
    $inicio = $_GET["inicio"];
}

try {
    // ----- 2. totais do filtro -----
    // a procedure sp_dashboard_totais devolve os números em parâmetros
    // OUT. No MySQL isso vira uma variável (@total_itens), que eu leio
    // depois com um SELECT.
    $chamadaTotais = $pdo->prepare(
        "call sp_dashboard_totais(:busca, '', :inicio, null, @total_itens, @total_vendas)"
    );
    $chamadaTotais->bindValue(":busca", $busca);
    $chamadaTotais->bindValue(":inicio", $inicio);
    $chamadaTotais->execute();
    $chamadaTotais->closeCursor();

    $consultaTotais = $pdo->query("select @total_itens as itens, @total_vendas as vendas");
    $totais = $consultaTotais->fetch(PDO::FETCH_OBJ);

    // ----- 3. itens vendidos (uma linha por item da venda) -----
    // os dois últimos parâmetros da procedure são o limite e a posição
    // inicial, ou seja, a paginação feita no banco
    $chamadaItens = $pdo->prepare(
        "call sp_dashboard_itens(:busca, '', :inicio, null, 1000, 0)"
    );
    $chamadaItens->bindValue(":busca", $busca);
    $chamadaItens->bindValue(":inicio", $inicio);
    $chamadaItens->execute();
    $itens = $chamadaItens->fetchAll(PDO::FETCH_ASSOC);
    $chamadaItens->closeCursor();

    // ----- 4. devolve tudo em JSON -----
    echo json_encode(
        array(
            "sucesso" => true,
            "total_itens" => (int) $totais->itens,
            "total_vendas" => (int) $totais->vendas,
            "itens" => $itens
        ),
        JSON_UNESCAPED_UNICODE
    );
} catch (PDOException $erro) {
    // deu erro no banco: responde com o código 500 e explica o motivo
    http_response_code(500);

    echo json_encode(
        array(
            "sucesso" => false,
            "mensagem" => "Erro ao consultar o banco de dados: " . $erro->getMessage()
        ),
        JSON_UNESCAPED_UNICODE
    );
}
