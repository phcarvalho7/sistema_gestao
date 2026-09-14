<?php
// Exclui uma venda.
//
// REGRA DE NEGÓCIO: se a venda estava concluída, o estoque dos itens
// volta para os produtos. Uso transação aqui também, para o estoque
// nunca ficar diferente do que foi realmente vendido.
//
// Os itens da venda são apagados sozinhos pelo banco, por causa do
// ON DELETE CASCADE da chave estrangeira.

if (!isset($pdo)) {
    exit;
}

if ($id == 0) {
    redirecionarCom("listar/venda", "warning", "Registro inválido.");
}

try {
    $pdo->beginTransaction();

    $consulta = $pdo->prepare("select status from vendas where id = :id limit 1");
    $consulta->bindValue(":id", $id, PDO::PARAM_INT);
    $consulta->execute();
    $venda = $consulta->fetch(PDO::FETCH_OBJ);

    if (!$venda) {
        throw new Exception("Esta venda já não existe mais.");
    }

    // devolve o estoque somente se a venda estava concluída
    if ($venda->status == "Concluída") {
        $consultaItens = $pdo->prepare(
            "select produto_id, quantidade from venda_itens where venda_id = :id"
        );
        $consultaItens->bindValue(":id", $id, PDO::PARAM_INT);
        $consultaItens->execute();
        $itens = $consultaItens->fetchAll(PDO::FETCH_OBJ);

        $devolver = $pdo->prepare("update produtos set estoque = estoque + :quantidade where id = :id");

        foreach ($itens as $item) {
            $devolver->bindValue(":quantidade", $item->quantidade, PDO::PARAM_INT);
            $devolver->bindValue(":id", $item->produto_id, PDO::PARAM_INT);
            $devolver->execute();
        }
    }

    $excluir = $pdo->prepare("delete from vendas where id = :id limit 1");
    $excluir->bindValue(":id", $id, PDO::PARAM_INT);
    $excluir->execute();

    $pdo->commit();

    redirecionarCom("listar/venda", "success", "Venda #{$id} excluída e estoque atualizado.");
} catch (Exception $erro) {
    $pdo->rollBack();

    redirecionarCom("listar/venda", "danger", "Não foi possível excluir a venda. " . $erro->getMessage());
}
