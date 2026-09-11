<?php
/**
 * Listagem de usuários do painel (apenas administradores).
 */

if (!isset($pagina)) exit;

if (!ehAdministrador()) {
    redirecionarCom("index.php", "warning", "Apenas administradores podem gerenciar usuários.");
}

$busca = trim($_GET["busca"] ?? "");

$consulta = $pdo->prepare(
    "select u.*,
            (select count(*) from vendas v where v.usuario_id = u.id) as total_vendas
     from usuarios u
     where (:busca = '' or u.nome like concat('%', :busca, '%') or u.email like concat('%', :busca, '%'))
     order by u.nome"
);
$consulta->execute([":busca" => $busca]);
$usuarios = $consulta->fetchAll(PDO::FETCH_OBJ);

$idLogado = (int) (usuarioLogado()["id"] ?? 0);
?>

<div class="card">
    <div class="card-header cabecalho-card">
        <div>
            <h5 class="mb-1">Usuários do painel</h5>
            <span class="texto-mudo texto-mini">
                <?= count($usuarios) ?> <?= count($usuarios) === 1 ? "registro" : "registros" ?>
            </span>
        </div>
        <a href="cadastrar/usuario" class="btn btn-primario btn-sm">
            <i class="bi bi-person-plus me-1"></i> Novo usuário
        </a>
    </div>

    <form class="barra-filtros" method="get" action="listar/usuario">
        <div class="input-group input-group-busca">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por nome ou e-mail">
            <button class="btn btn-primario" type="submit">Filtrar</button>
            <a href="listar/usuario" class="btn btn-contorno">Limpar</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table tabela-app align-middle mb-0">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Perfil</th>
                    <th class="text-center">Vendas</th>
                    <th class="text-center">Situação</th>
                    <th>Cadastro</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)) { ?>
                    <tr>
                        <td colspan="6">
                            <div class="estado-vazio border-0 my-3">
                                <i class="bi bi-people"></i>
                                <h6 class="mb-1">Nenhum usuário encontrado</h6>
                                <p class="texto-mudo texto-mini mb-3">Cadastre operadores para dividir o atendimento.</p>
                                <a href="cadastrar/usuario" class="btn btn-primario btn-sm">Cadastrar usuário</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>

                <?php foreach ($usuarios as $usuarioLista) { ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-tabela"><?= mb_strtoupper(mb_substr($usuarioLista->nome, 0, 1)) ?></span>
                                <div class="minimo-zero">
                                    <a href="cadastrar/usuario/<?= $usuarioLista->id ?>" class="celula-titulo d-block text-truncate">
                                        <?= htmlspecialchars($usuarioLista->nome) ?>
                                        <?php if ((int) $usuarioLista->id === $idLogado) { ?>
                                            <span class="pilula pilula-neutra ms-1">você</span>
                                        <?php } ?>
                                    </a>
                                    <span class="texto-mudo texto-mini d-block text-truncate"><?= htmlspecialchars($usuarioLista->email) ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($usuarioLista->perfil === "Administrador") { ?>
                                <span class="badge-tipo badge-tipo-livro">Administrador</span>
                            <?php } else { ?>
                                <span class="badge-tipo badge-tipo-manga">Operador</span>
                            <?php } ?>
                        </td>
                        <td class="text-center texto-mudo"><?= (int) $usuarioLista->total_vendas ?></td>
                        <td class="text-center">
                            <?php if ($usuarioLista->ativo === "Sim") { ?>
                                <span class="pilula pilula-success">Ativo</span>
                            <?php } else { ?>
                                <span class="pilula pilula-danger">Inativo</span>
                            <?php } ?>
                        </td>
                        <td class="texto-mudo texto-mini"><?= date("d/m/Y", strtotime($usuarioLista->criado_em)) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="cadastrar/usuario/<?= $usuarioLista->id ?>" class="btn btn-icone-sm" data-bs-toggle="tooltip" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ((int) $usuarioLista->id !== $idLogado) { ?>
                                <button type="button" class="btn btn-icone-sm btn-icone-perigo"
                                        data-bs-toggle="tooltip" title="Excluir"
                                        data-excluir-url="excluir/usuario/<?= $usuarioLista->id ?>"
                                        data-excluir-texto="Excluir o usuário &quot;<?= htmlspecialchars($usuarioLista->nome, ENT_QUOTES) ?>&quot;?">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            <?php } else { ?>
                                <button type="button" class="btn btn-icone-sm" disabled data-bs-toggle="tooltip" title="Você não pode excluir a própria conta">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <span class="texto-mudo texto-mini">
            <i class="bi bi-info-circle me-1"></i>
            Usuários com vendas registradas não são excluídos: eles são desativados para preservar o histórico.
        </span>
    </div>
</div>
