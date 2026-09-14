<?php
/**
 * Gravação (inclusão e edição) de usuários do painel.
 */

if (!isset($pagina)) exit;

if (!$_POST) {
    redirecionarCom("listar/usuario", "warning", "Requisição inválida.");
}

$idUsuario        = trim($_POST["id"] ?? "");
$nome             = trim($_POST["nome"] ?? "");
$email            = trim($_POST["email"] ?? "");
$senha            = $_POST["senha"] ?? "";
$senhaConfirmacao = $_POST["senha_confirmacao"] ?? "";

$idLogado = (int) (usuarioLogado()["id"] ?? 0);
$editandoProprioPerfil = $idUsuario !== "" && (int) $idUsuario === $idLogado;

if (!ehAdministrador() && !$editandoProprioPerfil) {
    redirecionarCom("index.php", "warning", "Você só pode editar a sua própria conta.");
}

// Perfil e situação só podem ser alterados por administradores.
$perfil = ehAdministrador() && ($_POST["perfil"] ?? "") === "Administrador" ? "Administrador" : "Operador";
$ativo  = ehAdministrador() ? (isset($_POST["ativo"]) ? "Sim" : "Não") : "Sim";

$rotaVolta = $idUsuario !== "" ? "cadastrar/usuario/{$idUsuario}" : "cadastrar/usuario";

// ---- Validações ----
if ($nome === "" || $email === "") {
    redirecionarCom($rotaVolta, "warning", "Preencha o nome e o e-mail do usuário.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirecionarCom($rotaVolta, "warning", "Informe um e-mail válido.");
}

if ($idUsuario === "" && $senha === "") {
    redirecionarCom($rotaVolta, "warning", "Defina uma senha para o novo usuário.");
}

if ($senha !== "" && mb_strlen($senha) < 6) {
    redirecionarCom($rotaVolta, "warning", "A senha deve ter no mínimo 6 caracteres.");
}

if ($senha !== "" && $senhaConfirmacao !== "" && $senha !== $senhaConfirmacao) {
    redirecionarCom($rotaVolta, "warning", "As senhas digitadas não conferem.");
}

// E-mail é único (login do painel)
$consultaDuplicado = $pdo->prepare(
    "select id from usuarios where email = :email and (:id = '' or id <> :id) limit 1"
);
$consultaDuplicado->execute([":email" => $email, ":id" => $idUsuario]);

if (!empty($consultaDuplicado->fetch(PDO::FETCH_OBJ))) {
    redirecionarCom($rotaVolta, "warning", "Já existe um usuário cadastrado com o e-mail {$email}.");
}

// Impede que o administrador logado desative ou rebaixe a si mesmo
// e fique sem acesso ao painel.
if ($editandoProprioPerfil) {
    $perfil = usuarioLogado()["perfil"] ?? $perfil;
    $ativo = "Sim";
}

try {
    if ($idUsuario === "") {
        $consulta = $pdo->prepare(
            "insert into usuarios (nome, email, senha, perfil, ativo)
             values (:nome, :email, :senha, :perfil, :ativo)"
        );
        $consulta->bindValue(":senha", password_hash($senha, PASSWORD_DEFAULT));
    } elseif ($senha !== "") {
        $consulta = $pdo->prepare(
            "update usuarios set nome = :nome, email = :email, senha = :senha, perfil = :perfil, ativo = :ativo
             where id = :id limit 1"
        );
        $consulta->bindValue(":senha", password_hash($senha, PASSWORD_DEFAULT));
        $consulta->bindValue(":id", (int) $idUsuario, PDO::PARAM_INT);
    } else {
        $consulta = $pdo->prepare(
            "update usuarios set nome = :nome, email = :email, perfil = :perfil, ativo = :ativo
             where id = :id limit 1"
        );
        $consulta->bindValue(":id", (int) $idUsuario, PDO::PARAM_INT);
    }

    $consulta->bindValue(":nome", $nome);
    $consulta->bindValue(":email", $email);
    $consulta->bindValue(":perfil", $perfil);
    $consulta->bindValue(":ativo", $ativo);
    $consulta->execute();

    // Mantém a sessão coerente quando o usuário edita a própria conta.
    if ($editandoProprioPerfil) {
        $_SESSION["livraria_nerd"]["nome"] = $nome;
        $_SESSION["livraria_nerd"]["email"] = $email;
    }

    $mensagem = $idUsuario === ""
        ? "Usuário \"{$nome}\" cadastrado com sucesso."
        : "Dados de \"{$nome}\" atualizados com sucesso.";

    $rotaDestino = ehAdministrador() ? "listar/usuario" : "index.php";

    redirecionarCom($rotaDestino, "success", $mensagem);
} catch (PDOException $erro) {
    redirecionarCom($rotaVolta, "danger", "Não foi possível salvar o usuário: " . $erro->getMessage());
}
