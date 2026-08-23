<?php
    if (!isset($pagina)) exit;

    if (!empty($id)) {
        $sql = "select * from produtos where id = :id limit 1";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":id", $id);
        $consulta->execute();

        $dadosCadastro = $consulta->fetch(PDO::FETCH_OBJ);
    }

    $id = $dadosCadastro->id ?? NULL;
    $titulo = $dadosCadastro->titulo ?? NULL;
    $autor = $dadosCadastro->autor ?? NULL;
    $editora = $dadosCadastro->editora ?? NULL;
    $tipo = $dadosCadastro->tipo ?? NULL;
    $categoria_id = $dadosCadastro->categoria_id ?? NULL;
    $preco = $dadosCadastro->preco ?? NULL;
    $estoque = $dadosCadastro->estoque ?? NULL;
    $sinopse = $dadosCadastro->sinopse ?? NULL;
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Cadastro de Produto</h5>
        <div>
            <a href="cadastrar/produto" class="btn btn-success btn-sm">Novo Registro</a>
            <a href="listar/produto" class="btn btn-primary btn-sm">Listar Registros</a>
        </div>
    </div>
    <div class="card-body">
        <form name="formCadastro" method="post" action="salvar/produto">
            <div class="row g-3">
                <div class="col-12 col-md-1">
                    <label for="id" class="form-label">ID:</label>
                    <input type="text" name="id" id="id" class="form-control" value="<?= $id ?>" readonly>
                </div>
                <div class="col-12 col-md-6">
                    <label for="titulo" class="form-label">Título:</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" required value="<?= htmlspecialchars($titulo ?? "") ?>">
                </div>
                <div class="col-12 col-md-5">
                    <label for="autor" class="form-label">Autor(a):</label>
                    <input type="text" name="autor" id="autor" class="form-control" required value="<?= htmlspecialchars($autor ?? "") ?>">
                </div>

                <div class="col-12 col-md-4">
                    <label for="editora" class="form-label">Editora:</label>
                    <input type="text" name="editora" id="editora" class="form-control" required value="<?= htmlspecialchars($editora ?? "") ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label for="tipo" class="form-label">Tipo:</label>
                    <select name="tipo" id="tipo" class="form-control" required>
                        <option value=""></option>
                        <?php foreach (["Livro", "HQ", "Mangá"] as $tipoOpcao) { ?>
                            <option value="<?= $tipoOpcao ?>" <?= ($tipo == $tipoOpcao) ? "selected" : "" ?>>
                                <?= $tipoOpcao ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label for="categoria_id" class="form-label">Categoria:</label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value=""></option>
                        <?php
                            $sqlCategoria = "select * from categorias order by nome";
                            $consultaCategoria = $pdo->prepare($sqlCategoria);
                            $consultaCategoria->execute();

                            while ($dadosCategoria = $consultaCategoria->fetch(PDO::FETCH_OBJ)) {
                                ?>
                                <option value="<?= $dadosCategoria->id ?>" <?= ($categoria_id == $dadosCategoria->id) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($dadosCategoria->nome) ?>
                                </option>
                                <?php
                            }
                        ?>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="preco" class="form-label">Preço (R$):</label>
                    <input type="number" step="0.01" min="0" name="preco" id="preco" class="form-control" required value="<?= $preco ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label for="estoque" class="form-label">Estoque:</label>
                    <input type="number" min="0" name="estoque" id="estoque" class="form-control" required value="<?= $estoque ?>">
                </div>

                <div class="col-12">
                    <label for="sinopse" class="form-label">Sinopse:</label>
                    <textarea name="sinopse" id="sinopse" class="form-control" rows="4"><?= htmlspecialchars($sinopse ?? "") ?></textarea>
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-success float-end">Salvar Registro</button>
        </form>
    </div>
</div>
