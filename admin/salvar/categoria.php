<?php
    if (!isset($pagina)) exit;

    if ($_POST) {
        $id = trim($_POST["id"] ?? NULL);
        $nome = trim($_POST["nome"] ?? NULL);

        if (empty($nome)) {
            echo "<script>alert('Preencha o nome da categoria');history.back();</script>";
        } else {
            if (empty($id)) {
                $sql = "insert into categorias (id, nome) values (NULL, :nome)";
                $consulta = $pdo->prepare($sql);
                $consulta->bindParam(":nome", $nome);
            } else {
                $sql = "update categorias set nome = :nome where id = :id limit 1";
                $consulta = $pdo->prepare($sql);
                $consulta->bindParam(":nome", $nome);
                $consulta->bindParam(":id", $id);
            }

            if ($consulta->execute()) {
                echo "<script>alert('Registro salvo com sucesso');location.href='listar/categoria';</script>";
            } else {
                echo "<script>alert('Erro ao salvar o registro');history.back();</script>";
            }
        }
    } else {
        echo "<script>alert('Requisição inválida');history.back();</script>";
    }
