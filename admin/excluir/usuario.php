<?php
/**
 * Exclusão de usuário do painel.
 * Regras de negócio:
 *   - somente administradores excluem usuários;
 *   - ninguém exclui a própria conta;
 *   - usuários com vendas registradas são desativados (e não
 *     apagados), para não perder o histórico das vendas.
 */

if (!isset($pagina)) exit;

if (!ehAdministrador()) {
    redirecionarCom("index.php", "warning", "Apenas administradores podem excluir usuários.");
}

if (empty($id)) {
    redirecionarCom("listar/usuario", "warning", "Registro inválido para exclusão.");
}

$idLogado = (int) (usuarioLogado()["id"] ?? 0);

if ((int) $id === $idLogado) {
    redirecionarCom("listar/usuario", "warning", "Você não pode excluir a sua própria conta.");
}

$consultaUsuario = $pdo->prepare("select id, nome, ativo from usuarios where id = :id limit 1");
$consultaUsuario->bindValue(":id", (int) $id, PDO::PARAM_INT);
$consultaUsuario->execute();
$usuario = $consultaUsuario->fetch(PDO::FETCH_OBJ);

if (empty($usuario)) {
    redirecionarCom("listar/usuario", "warning", "Este usuário já não existe mais no sistema.");
}

// Impede que o sistema fique sem nenhum administrador ativo.
$consultaAdmins = $pdo->query("select count(*) as total from usuarios where perfil = 'Administrador' and ativo = 'Sim'");
$totalAdmins = (int) $consultaAdmins->fetch(PDO::FETCH_OBJ)->total;

$consultaVendas = $pdo->prepare("select count(*) as total from vendas where usuario_id = :id");
$consultaVendas->bindValue(":id", (int) $id, PDO::PARAM_INT);
$consultaVendas->execute();
$totalVendas = (int) $consultaVendas->fetch(PDO::FETCH_OBJ)->total;

try {
    if ($totalVendas > 0) {
        // Não apaga: desativa, preservando o vínculo com as vendas.
        if ($usuario->ativo === "Não") {
            redirecionarCom(
                "listar/usuario",
                "warning",
                "\"{$usuario->nome}\" possui {$totalVendas} venda(s) registrada(s) e já está inativo. "
                . "O registro é mantido para preservar o histórico."
            );
        }

        $consulta = $pdo->prepare("update usuarios set ativo = 'Não' where id = :id limit 1");
        $consulta->bindValue(":id", (int) $id, PDO::PARAM_INT);
        $consulta->execute();

        redirecionarCom(
            "listar/usuario",
            "warning",
            "\"{$usuario->nome}\" possui {$totalVendas} venda(s) registrada(s), então foi apenas desativado. "
            . "Assim o histórico de vendas continua íntegro."
        );
    }

    if ($totalAdmins <= 1) {
        $consultaPerfil = $pdo->prepare("select perfil from usuarios where id = :id limit 1");
        $consultaPerfil->bindValue(":id", (int) $id, PDO::PARAM_INT);
        $consultaPerfil->execute();
        $perfilUsuario = $consultaPerfil->fetch(PDO::FETCH_OBJ)->perfil ?? "";

        if ($perfilUsuario === "Administrador") {
            redirecionarCom("listar/usuario", "danger", "Este é o último administrador ativo: o sistema ficaria sem acesso.");
        }
    }

    $consulta = $pdo->prepare("delete from usuarios where id = :id limit 1");
    $consulta->bindValue(":id", (int) $id, PDO::PARAM_INT);
    $consulta->execute();

    redirecionarCom("listar/usuario", "success", "Usuário \"{$usuario->nome}\" excluído com sucesso.");
} catch (PDOException $erro) {
    redirecionarCom("listar/usuario", "danger", "Não foi possível excluir o usuário: " . $erro->getMessage());
}
