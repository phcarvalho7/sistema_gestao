<?php
/**
 * Formulário de inclusão e edição de usuários do painel.
 * Um operador só consegue editar a própria conta; administradores
 * gerenciam todos os usuários.
 */

if (!isset($pagina)) exit;

$idLogado = (int) (usuarioLogado()["id"] ?? 0);
$editandoProprioPerfil = !empty($id) && (int) $id === $idLogado;

if (!ehAdministrador() && !$editandoProprioPerfil) {
    redirecionarCom("index.php", "warning", "Você só pode editar a sua própria conta.");
}

$dadosCadastro = NULL;

if (!empty($id)) {
    $consulta = $pdo->prepare("select * from usuarios where id = :id limit 1");
    $consulta->bindValue(":id", (int) $id, PDO::PARAM_INT);
    $consulta->execute();
    $dadosCadastro = $consulta->fetch(PDO::FETCH_OBJ);

    if (empty($dadosCadastro)) {
        redirecionarCom("listar/usuario", "warning", "Usuário não encontrado.");
    }
}

$idUsuario = $dadosCadastro->id ?? NULL;
$nome      = $dadosCadastro->nome ?? "";
$email     = $dadosCadastro->email ?? "";
$perfil    = $dadosCadastro->perfil ?? "Operador";
$ativo     = $dadosCadastro->ativo ?? "Sim";
$editando  = !empty($idUsuario);
?>

<div class="row g-4">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header cabecalho-card">
                <div>
                    <h5 class="mb-1"><?= $editando ? ($editandoProprioPerfil ? "Minha conta" : "Editar usuário") : "Novo usuário" ?></h5>
                    <span class="texto-mudo texto-mini">
                        <?= $editando ? "Registro #" . str_pad((string) $idUsuario, 3, "0", STR_PAD_LEFT) : "Dados de acesso ao painel" ?>
                    </span>
                </div>
                <?php if (ehAdministrador()) { ?>
                    <div class="d-flex gap-2">
                        <?php if ($editando) { ?>
                            <a href="cadastrar/usuario" class="btn btn-suave btn-sm"><i class="bi bi-plus-lg me-1"></i> Novo</a>
                        <?php } ?>
                        <a href="listar/usuario" class="btn btn-contorno btn-sm"><i class="bi bi-list-ul me-1"></i> Listagem</a>
                    </div>
                <?php } ?>
            </div>

            <div class="card-body">
                <form name="formCadastro" method="post" action="salvar/usuario">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $idUsuario) ?>">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="nome" class="form-label">Nome <span class="obrigatorio">*</span></label>
                            <input type="text" name="nome" id="nome" class="form-control" maxlength="100" required
                                   value="<?= htmlspecialchars($nome) ?>" placeholder="Nome completo">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">E-mail <span class="obrigatorio">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" maxlength="100" required
                                   value="<?= htmlspecialchars($email) ?>" placeholder="voce@livrarianerd.com">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="senha" class="form-label">
                                Senha <?= $editando ? "" : "<span class='obrigatorio'>*</span>" ?>
                            </label>
                            <input type="password" name="senha" id="senha" class="form-control"
                                   <?= $editando ? "" : "required" ?> placeholder="<?= $editando ? "Deixe em branco para manter" : "Mínimo de 6 caracteres" ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="senha_confirmacao" class="form-label">Confirmar senha</label>
                            <input type="password" name="senha_confirmacao" id="senha_confirmacao" class="form-control"
                                   placeholder="Repita a senha">
                        </div>

                        <?php if (ehAdministrador()) { ?>
                            <div class="col-12 col-md-6">
                                <label for="perfil" class="form-label">Perfil</label>
                                <select name="perfil" id="perfil" class="form-select">
                                    <option value="Operador" <?= $perfil === "Operador" ? "selected" : "" ?>>Operador (vendas e cadastros)</option>
                                    <option value="Administrador" <?= $perfil === "Administrador" ? "selected" : "" ?>>Administrador (acesso total)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label d-block">Situação</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="ativo" name="ativo"
                                           <?= $ativo === "Sim" ? "checked" : "" ?>>
                                    <label class="form-check-label" for="ativo">Usuário ativo (pode entrar no painel)</label>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= ehAdministrador() ? "listar/usuario" : "index.php" ?>" class="btn btn-suave">Cancelar</a>
                        <button type="submit" class="btn btn-primario">
                            <i class="bi bi-check2 me-1"></i><?= $editando ? "Salvar alterações" : "Cadastrar usuário" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-shield-lock me-1 text-primary"></i>Segurança</h6>
                <ul class="lista-regras mb-0">
                    <li>As senhas são gravadas com <code>password_hash()</code> — nunca em texto puro.</li>
                    <li>O e-mail é único: serve como login do painel.</li>
                    <li>Usuários inativos não conseguem entrar, mas continuam no histórico de vendas.</li>
                    <li>Você não pode excluir nem desativar a sua própria conta.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
