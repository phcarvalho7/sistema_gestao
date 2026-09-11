<?php
// Listagem de categorias.
//
// O LEFT JOIN com produtos serve para contar quantos produtos cada
// categoria tem. Esse número é importante porque categoria com produto
// vinculado não pode ser excluída.

if (!isset($pdo)) {
    exit;
}

$sql = "select c.id, c.nome, count(p.id) as total_produtos
        from categorias c
        left join produtos p on p.categoria_id = c.id
        group by c.id, c.nome
        order by c.nome";

$consulta = $pdo->prepare($sql);
$consulta->execute();
$categorias = $consulta->fetchAll(PDO::FETCH_OBJ);
?>

<div class="card">
    <div class="cabecalho-card">
        <div>
            <h5 class="mb-0">Categorias</h5>
            <span class="texto-mudo texto-mini"><?= count($categorias) ?> registro(s)</span>
        </div>
        <a href="cadastrar/categoria" class="btn btn-primario btn-sm">Nova categoria</a>
    </div>

    <div class="table-responsive">
        <table class="table tabela mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th class="text-center">Produtos</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categorias) == 0) { ?>
                    <tr>
                        <td colspan="4" class="text-center texto-mudo py-4">Nenhuma categoria cadastrada.</td>
                    </tr>
                <?php } ?>

                <?php foreach ($categorias as $categoria) { ?>
                    <tr>
                        <td class="texto-mudo"><?= $categoria->id ?></td>
                        <td><strong><?= htmlspecialchars($categoria->nome) ?></strong></td>
                        <td class="text-center"><?= $categoria->total_produtos ?></td>
                        <td class="text-end">
                            <a href="cadastrar/categoria/<?= $categoria->id ?>" class="btn btn-contorno btn-sm">Editar</a>
                            <a href="excluir/categoria/<?= $categoria->id ?>" class="btn btn-perigo btn-sm"
                               onclick="return confirm('Excluir a categoria <?= htmlspecialchars($categoria->nome, ENT_QUOTES) ?>?')">
                                Excluir
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="rodape-card">
        <span class="texto-mudo texto-mini">
            Categorias com produtos vinculados não podem ser excluídas.
        </span>
    </div>
</div>
