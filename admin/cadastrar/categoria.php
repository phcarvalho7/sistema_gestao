<?php
    if (!isset($pagina)) exit;

    if (!empty($id)) {
        $sql = "select * from categorias where id = :id limit 1";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":id", $id);
        $consulta->execute();

        $dadosCadastro = $consulta->fetch(PDO::FETCH_OBJ);
    }

    $id = $dadosCadastro->id ?? NULL;
    $nome = $dadosCadastro->nome ?? NULL;
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Cadastro de Categoria</h5>
        <div>
            <a href="cadastrar/categoria" class="btn btn-success btn-sm">Novo Registro</a>
            <a href="listar/categoria" class="btn btn-primary btn-sm">Listar Registros</a>
        </div>
    </div>
    <div class="card-body">
        <form name="formCadastro" method="post" action="salvar/categoria">
            <div class="row g-3">
                <div class="col-12 col-md-2">
                    <label for="id" class="form-label">ID:</label>
                    <input type="text" name="id" id="id" class="form-control" value="<?= $id ?>" readonly>
                </div>
                <div class="col-12 col-md-10">
                    <label for="nome" class="form-label">Nome da Categoria:</label>
                    <input type="text" name="nome" id="nome" class="form-control" required value="<?= htmlspecialchars($nome ?? "") ?>">
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-success float-end">Salvar Registro</button>
        </form>
    </div>
</div>
