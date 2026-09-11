<?php
// LOJA - este é o único arquivo que o navegador chama.
// Ele decide qual página mostrar e monta a tela em 3 partes:
// templates/header.php + pages/<pagina>.php + templates/footer.php
//
// O .htaccess manda todos os endereços para cá:
// /produtos    -> $pagina = "produtos"
// /produto/5   -> $pagina = "produto" e $id = 5

session_start();

include "config.php";
include "funcoes.php";


// ----- descobre a página pedida no endereço -----

$rota = "";

if (isset($_GET["param"])) {
    $rota = $_GET["param"];
}

$partes = explode("/", $rota);

$pagina = $partes[0];
$id = 0;

if (isset($partes[1])) {
    $id = (int) $partes[1];
}

if ($pagina == "") {
    $pagina = "home";
}

// basename() tira as barras do nome, assim ninguém consegue usar o
// endereço para abrir arquivos de outras pastas
$pagina = basename($pagina);

$arquivo = "pages/" . $pagina . ".php";

if (!file_exists($arquivo)) {
    $pagina = "erro";
    $arquivo = "pages/erro.php";
}


// ----- título da aba do navegador -----

$titulos = array(
    "home"      => "Livros, HQs e Mangás",
    "produtos"  => "Catálogo",
    "produto"   => "Detalhes do produto",
    "categoria" => "Categoria",
    "buscar"    => "Busca",
    "erro"      => "Página não encontrada"
);

$tituloPagina = "Prosa & Traço";

if (isset($titulos[$pagina])) {
    $tituloPagina = $titulos[$pagina];
}


// ----- monta a tela -----

include "templates/header.php";
include $arquivo;
include "templates/footer.php";
