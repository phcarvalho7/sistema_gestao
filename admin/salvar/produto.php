<?php
    if (!isset($pagina)) exit;

    if ($_POST) {
        $id = trim($_POST["id"] ?? NULL);
        $titulo = trim($_POST["titulo"] ?? NULL);
        $autor = trim($_POST["autor"] ?? NULL);
        $editora = trim($_POST["editora"] ?? NULL);
        $tipo = trim($_POST["tipo"] ?? NULL);
        $categoria_id = trim($_POST["categoria_id"] ?? NULL);
        $preco = trim($_POST["preco"] ?? NULL);
        $estoque = trim($_POST["estoque"] ?? NULL);
        $sinopse = trim($_POST["sinopse"] ?? NULL);

        if (empty($titulo) || empty($autor) || empty($tipo) || empty($categoria_id)) {
            echo "<script>alert('Preencha todos os campos obrigatórios');history.back();</script>";
            exit;
        }

        // Padroniza para valores sempre positivos antes de gravar
        $preco = abs((float) $preco);
        $estoque = abs((int) $estoque);

        if (empty($id)) {
            $sql = "insert into produtos (id, titulo, autor, editora, tipo, categoria_id, preco, estoque, sinopse)
            values (NULL, :titulo, :autor, :editora, :tipo, :categoria_id, :preco, :estoque, :sinopse)";
        } else {
            $sql = "update produtos set
            titulo = :titulo, autor = :autor, editora = :editora, tipo = :tipo,
            categoria_id = :categoria_id, preco = :preco, estoque = :estoque, sinopse = :sinopse
            where id = :id limit 1";
        }

        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":titulo", $titulo);
        $consulta->bindParam(":autor", $autor);
        $consulta->bindParam(":editora", $editora);
        $consulta->bindParam(":tipo", $tipo);
        $consulta->bindParam(":categoria_id", $categoria_id);
        $consulta->bindParam(":preco", $preco);
        $consulta->bindParam(":estoque", $estoque);
        $consulta->bindParam(":sinopse", $sinopse);

        if (!empty($id)) {
            $consulta->bindParam(":id", $id);
        }

        if ($consulta->execute()) {
            echo "<script>alert('Produto salvo com sucesso');location.href='listar/produto';</script>";
        } else {
            echo "<script>alert('Erro ao salvar o produto');history.back();</script>";
        }
    } else {
        echo "<script>alert('Requisição inválida');history.back();</script>";
    }
