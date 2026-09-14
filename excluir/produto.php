<?php
// Exclui um produto.
//
// REGRA DE NEGÓCIO: produto que já apareceu em alguma venda não pode
// ser excluído, senão o histórico e os relatórios ficariam quebrados.
// Nesse caso o sistema explica o motivo para o usuário.

if (!isset($pdo)) {
    exit;
}

if ($id == 0) {
    redirecionarCom("listar/produto", "warning", "Registro inválido.");
}

// busca o produto, para poder usar o título na mensagem
$consulta = $pdo->prepare("select titulo from produtos where id = :id limit 1");
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$produto = $consulta->fetch(PDO::FETCH_OBJ);

if (!$produto) {
    redirecionarCom("listar/produto", "warning", "Este produto já não existe mais.");
}

// conta em quantos itens de venda ele aparece
$consultaVendas = $pdo->prepare("select count(*) as total from venda_itens where produto_id = :id");
$consultaVendas->bindValue(":id", $id, PDO::PARAM_INT);
$consultaVendas->execute();
$totalVendas = $consultaVendas->fetch(PDO::FETCH_OBJ)->total;

if ($totalVendas > 0) {
    redirecionarCom(
        "listar/produto",
        "danger",
        "Não é possível excluir \"{$produto->titulo}\": ele aparece em {$totalVendas} item(ns) de venda. " .
        "Se quiser tirá-lo de circulação, deixe o estoque em zero."
    );
}

// pode excluir
$excluir = $pdo->prepare("delete from produtos where id = :id limit 1");
$excluir->bindValue(":id", $id, PDO::PARAM_INT);
$excluir->execute();

redirecionarCom("listar/produto", "success", "Produto \"{$produto->titulo}\" excluído com sucesso.");
