<?php
    if (!isset($pagina)) exit;

    if (!$_POST) {
        echo "<script>alert('Requisição inválida');history.back();</script>";
        exit;
    }

    $clienteNome = trim($_POST["cliente_nome"] ?? NULL);
    $produtoIds = $_POST["produto_id"] ?? [];
    $quantidades = $_POST["quantidade"] ?? [];

    if (empty($clienteNome) || empty($produtoIds)) {
        echo "<script>alert('Preencha o cliente e ao menos um item');history.back();</script>";
        exit;
    }

    try {
        $pdo->beginTransaction();

        $total = 0;
        $itensValidados = [];

        // Valida estoque de cada item antes de gravar qualquer coisa
        foreach ($produtoIds as $indice => $produtoId) {
            $quantidade = (int) ($quantidades[$indice] ?? 0);

            if (empty($produtoId) || $quantidade <= 0) {
                continue;
            }

            $sqlProduto = "select * from produtos where id = :id limit 1 for update";
            $consultaProduto = $pdo->prepare($sqlProduto);
            $consultaProduto->bindParam(":id", $produtoId);
            $consultaProduto->execute();
            $produto = $consultaProduto->fetch(PDO::FETCH_OBJ);

            if (empty($produto)) {
                throw new Exception("Produto inválido selecionado.");
            }

            if ($quantidade > $produto->estoque) {
                throw new Exception("Estoque insuficiente para \"{$produto->titulo}\" (disponível: {$produto->estoque}).");
            }

            $subtotal = $quantidade * $produto->preco;
            $total += $subtotal;

            $itensValidados[] = [
                "produto_id" => $produto->id,
                "quantidade" => $quantidade,
                "valor_unitario" => $produto->preco
            ];
        }

        if (empty($itensValidados)) {
            throw new Exception("Nenhum item válido informado.");
        }

        $sqlVenda = "insert into vendas (id, cliente_nome, data_venda, total, status)
        values (NULL, :cliente_nome, NOW(), :total, 'Concluída')";
        $consultaVenda = $pdo->prepare($sqlVenda);
        $consultaVenda->bindParam(":cliente_nome", $clienteNome);
        $consultaVenda->bindParam(":total", $total);
        $consultaVenda->execute();

        $vendaId = $pdo->lastInsertId();

        $sqlItem = "insert into venda_itens (venda_id, produto_id, quantidade, valor_unitario)
        values (:venda_id, :produto_id, :quantidade, :valor_unitario)";
        $consultaItem = $pdo->prepare($sqlItem);

        $sqlEstoque = "update produtos set estoque = estoque - :quantidade where id = :id";
        $consultaEstoque = $pdo->prepare($sqlEstoque);

        foreach ($itensValidados as $item) {
            $consultaItem->bindParam(":venda_id", $vendaId);
            $consultaItem->bindParam(":produto_id", $item["produto_id"]);
            $consultaItem->bindParam(":quantidade", $item["quantidade"]);
            $consultaItem->bindParam(":valor_unitario", $item["valor_unitario"]);
            $consultaItem->execute();

            $consultaEstoque->bindParam(":quantidade", $item["quantidade"]);
            $consultaEstoque->bindParam(":id", $item["produto_id"]);
            $consultaEstoque->execute();
        }

        $pdo->commit();

        echo "<script>alert('Venda registrada com sucesso');location.href='listar/venda';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensagem = addslashes($e->getMessage());
        echo "<script>alert('Não foi possível registrar a venda: {$mensagem}');history.back();</script>";
    }
