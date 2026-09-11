<?php
// Exclui uma categoria.
//
// REGRA DE NEGÓCIO: categoria com produtos vinculados não pode ser
// excluída. O banco já impediria isso pela chave estrangeira, mas
// confiro antes para mostrar uma mensagem clara em vez de um erro
// do MySQL.

if (!isset($pdo)) {
    exit;
}

if ($id == 0) {
    redirecionarCom("listar/categoria", "warning", "Registro inválido.");
}

$consulta = $pdo->prepare("select nome from categorias where id = :id limit 1");
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$categoria = $consulta->fetch(PDO::FETCH_OBJ);

if (!$categoria) {
    redirecionarCom("listar/categoria", "warning", "Esta categoria já não existe mais.");
}

// conta quantos produtos usam a categoria
$consultaProdutos = $pdo->prepare("select count(*) as total from produtos where categoria_id = :id");
$consultaProdutos->bindValue(":id", $id, PDO::PARAM_INT);
$consultaProdutos->execute();
$totalProdutos = $consultaProdutos->fetch(PDO::FETCH_OBJ)->total;

if ($totalProdutos > 0) {
    redirecionarCom(
        "listar/categoria",
        "danger",
        "Não é possível excluir a categoria \"{$categoria->nome}\": existem {$totalProdutos} produto(s) " .
        "vinculados a ela. Mude a categoria desses produtos primeiro."
    );
}

$excluir = $pdo->prepare("delete from categorias where id = :id limit 1");
$excluir->bindValue(":id", $id, PDO::PARAM_INT);
$excluir->execute();

redirecionarCom("listar/categoria", "success", "Categoria \"{$categoria->nome}\" excluída com sucesso.");
