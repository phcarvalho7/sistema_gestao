<?php
    if (!isset($pagina)) exit;

    if (empty($id)) {
        echo "<script>alert('Registro inválido');location.href='../listar/categoria';</script>";
        exit;
    }

    // Verifica se existe algum produto usando esta categoria antes de excluir
    $sqlVerifica = "select count(*) as total from produtos where categoria_id = :id";
    $consultaVerifica = $pdo->prepare($sqlVerifica);
    $consultaVerifica->bindParam(":id", $id);
    $consultaVerifica->execute();
    $verificacao = $consultaVerifica->fetch(PDO::FETCH_OBJ);

    if ($verificacao->total > 0) {
        echo "<script>alert('Não é possível excluir: existem {$verificacao->total} produto(s) cadastrados nesta categoria.');location.href='listar/categoria';</script>";
        exit;
    }

    $sql = "delete from categorias where id = :id limit 1";
    $consulta = $pdo->prepare($sql);
    $consulta->bindParam(":id", $id);

    if ($consulta->execute()) {
        echo "<script>alert('Categoria excluída com sucesso');location.href='listar/categoria';</script>";
    } else {
        echo "<script>alert('Erro ao excluir a categoria');location.href='listar/categoria';</script>";
    }
