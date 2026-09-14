<?php
// Grava a categoria no banco (cadastro novo ou edição).

if (!isset($pdo)) {
    exit;
}

$idCategoria = "";

if (isset($_POST["id"])) {
    $idCategoria = trim($_POST["id"]);
}

$nome = trim($_POST["nome"]);

$voltarPara = "cadastrar/categoria";

if ($idCategoria != "") {
    $voltarPara = "cadastrar/categoria/" . $idCategoria;
}

// ----- validações -----
if ($nome == "") {
    redirecionarCom($voltarPara, "warning", "Informe o nome da categoria.");
}

// a coluna nome é UNIQUE no banco; confiro aqui antes para mostrar uma
// mensagem clara em vez de um erro do MySQL
$consultaDuplicado = $pdo->prepare("select id from categorias where nome = :nome limit 1");
$consultaDuplicado->bindValue(":nome", $nome);
$consultaDuplicado->execute();
$duplicado = $consultaDuplicado->fetch(PDO::FETCH_OBJ);

if ($duplicado && $duplicado->id != $idCategoria) {
    redirecionarCom($voltarPara, "warning", "Já existe uma categoria chamada \"{$nome}\".");
}

// ----- grava -----
if ($idCategoria == "") {
    $consulta = $pdo->prepare("insert into categorias (nome) values (:nome)");
    $consulta->bindValue(":nome", $nome);
    $consulta->execute();

    redirecionarCom("listar/categoria", "success", "Categoria \"{$nome}\" cadastrada com sucesso.");
}

$consulta = $pdo->prepare("update categorias set nome = :nome where id = :id limit 1");
$consulta->bindValue(":nome", $nome);
$consulta->bindValue(":id", $idCategoria, PDO::PARAM_INT);
$consulta->execute();

redirecionarCom("listar/categoria", "success", "Categoria \"{$nome}\" atualizada com sucesso.");
