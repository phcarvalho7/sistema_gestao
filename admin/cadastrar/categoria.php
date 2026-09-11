<?php
// Formulário de categoria (cadastro e edição).
// Quem grava no banco é o salvar/categoria.php.

if (!isset($pdo)) {
    exit;
}

$nome = "";

if ($id > 0) {
    $consulta = $pdo->prepare("select * from categorias where id = :id limit 1");
    $consulta->bindValue(":id", $id, PDO::PARAM_INT);
    $consulta->execute();
    $categoria = $consulta->fetch(PDO::FETCH_OBJ);

    if (!$categoria) {
        redirecionarCom("listar/categoria", "warning", "Categoria não encontrada.");
    }

    $nome = $categoria->nome;
}
?>

<div class="card">
    <div class="cabecalho-card">
        <h5 class="mb-0"><?= $id > 0 ? "Editar categoria" : "Nova categoria" ?></h5>
        <a href="listar/categoria" class="btn btn-contorno btn-sm">Voltar para a listagem</a>
    </div>

    <div class="p-4">
        <form method="post" action="salvar/categoria">
            <input type="hidden" name="id" value="<?= $id > 0 ? $id : "" ?>">

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="nome" class="form-label">Nome da categoria *</label>
                    <input type="text" name="nome" id="nome" class="form-control" required autofocus
                           value="<?= htmlspecialchars($nome) ?>">
                    <span class="texto-mudo texto-mini">
                        O nome aparece no menu da loja. Não pode repetir.
                    </span>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="listar/categoria" class="btn btn-suave">Cancelar</a>
                <button type="submit" class="btn btn-primario">Salvar</button>
            </div>
        </form>
    </div>
</div>
