<?php
// Grava o produto no banco (cadastro novo ou edição).
//
// Este arquivo não desenha tela: ele valida, grava e volta para a
// listagem levando uma mensagem.

if (!isset($pdo)) {
    exit;
}

// ----- 1. recebe os dados do formulário -----
$idProduto = "";

if (isset($_POST["id"])) {
    $idProduto = trim($_POST["id"]);
}

$titulo = trim($_POST["titulo"]);
$autor = trim($_POST["autor"]);
$editora = trim($_POST["editora"]);
$tipo = $_POST["tipo"];
$categoriaId = (int) $_POST["categoria_id"];
$preco = (float) $_POST["preco"];
$estoque = (int) $_POST["estoque"];
$estoqueMinimo = (int) $_POST["estoque_minimo"];
$sinopse = trim($_POST["sinopse"]);

// para onde voltar se algum dado estiver errado
$voltarPara = "cadastrar/produto";

if ($idProduto != "") {
    $voltarPara = "cadastrar/produto/" . $idProduto;
}

// ----- 2. validações -----
if ($titulo == "" || $autor == "" || $editora == "") {
    redirecionarCom($voltarPara, "warning", "Preencha título, autor e editora.");
}

if ($tipo != "Livro" && $tipo != "HQ" && $tipo != "Mangá") {
    redirecionarCom($voltarPara, "warning", "Escolha o tipo do produto.");
}

if ($categoriaId == 0) {
    redirecionarCom($voltarPara, "warning", "Escolha a categoria do produto.");
}

// não deixa cadastrar o mesmo título do mesmo autor duas vezes
$consultaDuplicado = $pdo->prepare(
    "select id from produtos where titulo = :titulo and autor = :autor limit 1"
);
$consultaDuplicado->bindValue(":titulo", $titulo);
$consultaDuplicado->bindValue(":autor", $autor);
$consultaDuplicado->execute();
$duplicado = $consultaDuplicado->fetch(PDO::FETCH_OBJ);

if ($duplicado && $duplicado->id != $idProduto) {
    redirecionarCom($voltarPara, "warning", "Já existe um produto com este título e autor.");
}

// ----- 3. grava -----
// id vazio = INSERT, id preenchido = UPDATE
// o preço e o estoque ainda passam pelos triggers do banco, que
// transformam valor negativo em positivo
if ($idProduto == "") {
    $sql = "insert into produtos
                (titulo, autor, editora, tipo, categoria_id, preco, estoque, estoque_minimo, sinopse)
            values
                (:titulo, :autor, :editora, :tipo, :categoria_id, :preco, :estoque, :estoque_minimo, :sinopse)";
} else {
    $sql = "update produtos set
                titulo = :titulo,
                autor = :autor,
                editora = :editora,
                tipo = :tipo,
                categoria_id = :categoria_id,
                preco = :preco,
                estoque = :estoque,
                estoque_minimo = :estoque_minimo,
                sinopse = :sinopse
            where id = :id
            limit 1";
}

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":titulo", $titulo);
$consulta->bindValue(":autor", $autor);
$consulta->bindValue(":editora", $editora);
$consulta->bindValue(":tipo", $tipo);
$consulta->bindValue(":categoria_id", $categoriaId, PDO::PARAM_INT);
$consulta->bindValue(":preco", $preco);
$consulta->bindValue(":estoque", $estoque, PDO::PARAM_INT);
$consulta->bindValue(":estoque_minimo", $estoqueMinimo, PDO::PARAM_INT);
$consulta->bindValue(":sinopse", $sinopse);

if ($idProduto != "") {
    $consulta->bindValue(":id", $idProduto, PDO::PARAM_INT);
}

$consulta->execute();

if ($idProduto == "") {
    redirecionarCom("listar/produto", "success", "Produto \"{$titulo}\" cadastrado com sucesso.");
}

redirecionarCom("listar/produto", "success", "Produto \"{$titulo}\" atualizado com sucesso.");
