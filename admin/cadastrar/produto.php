<?php
// Formulário de produto.
//
// O mesmo arquivo serve para cadastrar e para editar:
// cadastrar/produto     -> $id = 0, formulário em branco
// cadastrar/produto/7   -> $id = 7, formulário preenchido
//
// Quem grava no banco é o salvar/produto.php.

if (!isset($pdo)) {
    exit;
}

// valores em branco (usados quando é um cadastro novo)
$titulo = "";
$autor = "";
$editora = "";
$tipo = "";
$categoriaId = "";
$preco = "";
$estoque = "";
$estoqueMinimo = 5;
$sinopse = "";

// se veio um id no endereço, busca o produto para preencher o formulário
if ($id > 0) {
    $consulta = $pdo->prepare("select * from produtos where id = :id limit 1");
    $consulta->bindValue(":id", $id, PDO::PARAM_INT);
    $consulta->execute();
    $produto = $consulta->fetch(PDO::FETCH_OBJ);

    if (!$produto) {
        redirecionarCom("listar/produto", "warning", "Produto não encontrado.");
    }

    $titulo = $produto->titulo;
    $autor = $produto->autor;
    $editora = $produto->editora;
    $tipo = $produto->tipo;
    $categoriaId = $produto->categoria_id;
    $preco = $produto->preco;
    $estoque = $produto->estoque;
    $estoqueMinimo = $produto->estoque_minimo;
    $sinopse = $produto->sinopse;
}

// categorias do campo de seleção
$consultaCategorias = $pdo->prepare("select id, nome from categorias order by nome");
$consultaCategorias->execute();
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_OBJ);
?>

<div class="card">
    <div class="cabecalho-card">
        <h5 class="mb-0"><?= $id > 0 ? "Editar produto" : "Novo produto" ?></h5>
        <a href="listar/produto" class="btn btn-contorno btn-sm">Voltar para a listagem</a>
    </div>

    <div class="p-4">
        <form method="post" action="salvar/produto">
            <!-- o id vai escondido no formulário: vazio é cadastro novo,
                 com número é edição -->
            <input type="hidden" name="id" value="<?= $id > 0 ? $id : "" ?>">

            <div class="row g-3">
                <div class="col-12 col-md-7">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" required
                           value="<?= htmlspecialchars($titulo) ?>">
                </div>
                <div class="col-12 col-md-5">
                    <label for="autor" class="form-label">Autor(a) *</label>
                    <input type="text" name="autor" id="autor" class="form-control" required
                           value="<?= htmlspecialchars($autor) ?>">
                </div>

                <div class="col-12 col-md-5">
                    <label for="editora" class="form-label">Editora *</label>
                    <input type="text" name="editora" id="editora" class="form-control" required
                           value="<?= htmlspecialchars($editora) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label for="tipo" class="form-label">Tipo *</label>
                    <select name="tipo" id="tipo" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="Livro" <?= $tipo == "Livro" ? "selected" : "" ?>>Livro</option>
                        <option value="HQ" <?= $tipo == "HQ" ? "selected" : "" ?>>HQ</option>
                        <option value="Mangá" <?= $tipo == "Mangá" ? "selected" : "" ?>>Mangá</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label for="categoria_id" class="form-label">Categoria *</label>
                    <select name="categoria_id" id="categoria_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($categorias as $categoria) { ?>
                            <option value="<?= $categoria->id ?>" <?= $categoriaId == $categoria->id ? "selected" : "" ?>>
                                <?= htmlspecialchars($categoria->nome) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="preco" class="form-label">Preço (R$) *</label>
                    <input type="number" step="0.01" min="0" name="preco" id="preco" class="form-control" required
                           value="<?= $preco ?>">
                </div>
                <div class="col-6 col-md-4">
                    <label for="estoque" class="form-label">Estoque *</label>
                    <input type="number" min="0" name="estoque" id="estoque" class="form-control" required
                           value="<?= $estoque ?>">
                </div>
                <div class="col-6 col-md-4">
                    <label for="estoque_minimo" class="form-label">Estoque mínimo</label>
                    <input type="number" min="0" name="estoque_minimo" id="estoque_minimo" class="form-control"
                           value="<?= $estoqueMinimo ?>">
                    <span class="texto-mudo texto-mini">Abaixo desse número o estoque fica "Crítico".</span>
                </div>

                <div class="col-12">
                    <label for="sinopse" class="form-label">Sinopse</label>
                    <textarea name="sinopse" id="sinopse" class="form-control" rows="4"><?= htmlspecialchars($sinopse) ?></textarea>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="listar/produto" class="btn btn-suave">Cancelar</a>
                <button type="submit" class="btn btn-primario">Salvar</button>
            </div>
        </form>
    </div>
</div>
