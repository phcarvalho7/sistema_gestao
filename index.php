<?php
// PAINEL - este é o único arquivo que o navegador chama.
// Ele faz três coisas: cuida do login, descobre qual tela foi pedida
// e monta a tela com os templates.
//
// Quem abre o endereço do sistema sem estar logado cai na tela de
// login (pages/login.php). Depois de entrar, vai para a dashboard.
//
// O endereço tem o formato pasta/tela/id:
// listar/produto        -> listar/produto.php
// cadastrar/produto/7   -> cadastrar/produto.php com $id = 7
// excluir/categoria/3   -> excluir/categoria.php com $id = 3

session_start();

include "config.php";
include "funcoes.php";

// ----- 1. login -----
$logado = isset($_SESSION["usuario"]);

// recebeu o formulário de login e ainda não está logado: confere os dados
if (!$logado && isset($_POST["email"])) {
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    $consulta = $pdo->prepare("select * from usuarios where email = :email limit 1");
    $consulta->bindValue(":email", $email);
    $consulta->execute();
    $usuario = $consulta->fetch(PDO::FETCH_OBJ);

    if (!$usuario) {
        definirMensagem("danger", "Usuário não encontrado. Confira o e-mail digitado.");
    } else if ($usuario->ativo != "Sim") {
        definirMensagem("danger", "Este usuário está inativo.");
    } else if (!password_verify($senha, $usuario->senha)) {
        // password_verify compara a senha digitada com o hash do banco
        definirMensagem("danger", "Senha incorreta. Tente novamente.");
    } else {
        // login correto: guarda os dados na sessão e recarrega a página
        $_SESSION["usuario"] = array(
            "id" => $usuario->id,
            "nome" => $usuario->nome,
            "email" => $usuario->email,
            "perfil" => $usuario->perfil
        );

        definirMensagem("success", "Bem-vindo(a), " . $usuario->nome . "!");
        header("Location: " . urlPainel("index.php"));
        exit;
    }
}

// sem sessão: mostra a tela de login e para aqui
if (!isset($_SESSION["usuario"])) {
    include "pages/login.php";
    exit;
}

// ----- 2. qual tela foi pedida -----
$rota = "pages/home";

if (isset($_GET["param"]) && $_GET["param"] != "") {
    $rota = $_GET["param"];
}

$partes = explode("/", $rota);

$pasta = basename($partes[0]);
$tela = "home";
$id = 0;

if (isset($partes[1])) {
    $tela = basename($partes[1]);
}

if (isset($partes[2])) {
    $id = (int) $partes[2];
}

// só estas pastas podem ser abertas pelo endereço
$pastasPermitidas = array("pages", "listar", "cadastrar", "salvar", "excluir");

if (!in_array($pasta, $pastasPermitidas)) {
    $pasta = "pages";
    $tela = "erro";
}

$arquivo = $pasta . "/" . $tela . ".php";

if (!file_exists($arquivo)) {
    $pasta = "pages";
    $tela = "erro";
    $arquivo = "pages/erro.php";
}

// ----- 3. monta a tela -----

// as pastas salvar e excluir não têm tela: elas gravam no banco e
// redirecionam de volta para a listagem. Por isso são incluídas antes
// do template, quando nada ainda foi escrito na página.
if ($pasta == "salvar" || $pasta == "excluir") {
    include $arquivo;
    exit;
}

// título da tela e item do menu que fica destacado
$titulos = array(
    "home"      => "Início",
    "produto"   => "Produtos",
    "categoria" => "Categorias",
    "venda"     => "Vendas",
    "erro"      => "Página não encontrada"
);

$tituloPagina = "Painel";

if (isset($titulos[$tela])) {
    $tituloPagina = $titulos[$tela];
}

$menuAtivo = $tela;

include "templates/header.php";
include $arquivo;
include "templates/footer.php";
