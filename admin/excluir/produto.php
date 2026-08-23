<?php
    if (!isset($pagina)) exit;

    if (empty($id)) {
        echo "<script>alert('Registro inválido');location.href='../listar/produto';</script>";
        exit;
    }

    // Verifica se o produto já possui vendas registradas
    $sqlVerifica = "select count(*) as total from venda_itens where produto_id = :id";
    $consultaVerifica = $pdo->prepare($sqlVerifica);
    $consultaVerifica->bindParam(":id", $id);
    $consultaVerifica->execute();
    $verificacao = $consultaVerifica->fetch(PDO::FETCH_OBJ);

    if ($verificacao->total > 0) {
        echo "<script>alert('Não é possível excluir: este produto já possui {$verificacao->total} venda(s) registrada(s).');location.href='listar/produto';</script>";
        exit;
    }

    $sql = "delete from produtos where id = :id limit 1";
    $consulta = $pdo->prepare($sql);
    $consulta->bindParam(":id", $id);

    if ($consulta->execute()) {
        echo "<script>alert('Produto excluído com sucesso');location.href='listar/produto';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o produto');location.href='listar/produto';</script>";
    }
