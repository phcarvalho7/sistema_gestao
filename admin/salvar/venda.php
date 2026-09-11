<?php
// Grava a venda no banco e mexe no estoque dos produtos.
//
// Tudo acontece dentro de uma TRANSAÇÃO: ou todas as gravações dão
// certo, ou nenhuma é salva (rollBack). Sem isso o estoque poderia
// ficar errado se acontecesse um erro no meio do caminho.
//
// As regras são:
// - venda Concluída desconta o estoque
// - venda Cancelada não desconta (e devolve, se ela já existia)
// - não é possível vender mais do que existe em estoque

if (!isset($pdo)) {
    exit;
}

// ----- 1. dados do formulário -----
$idVenda = 0;

if (isset($_POST["id"]) && $_POST["id"] != "") {
    $idVenda = (int) $_POST["id"];
}

$clienteNome = trim($_POST["cliente_nome"]);
$status = "Concluída";

if ($_POST["status"] == "Cancelada") {
    $status = "Cancelada";
}

$produtoIds = $_POST["produto_id"];
$quantidades = $_POST["quantidade"];

$voltarPara = "cadastrar/venda";

if ($idVenda > 0) {
    $voltarPara = "cadastrar/venda/" . $idVenda;
}

if ($clienteNome == "") {
    redirecionarCom($voltarPara, "warning", "Informe o nome do cliente.");
}

// ----- 2. monta a lista de itens, ignorando as linhas em branco -----
$itens = array();

for ($linha = 0; $linha < count($produtoIds); $linha++) {
    $produtoId = (int) $produtoIds[$linha];
    $quantidade = (int) $quantidades[$linha];

    if ($produtoId > 0 && $quantidade > 0) {
        $itens[] = array("produto_id" => $produtoId, "quantidade" => $quantidade);
    }
}

if (count($itens) == 0) {
    redirecionarCom($voltarPara, "warning", "Escolha ao menos um produto e informe a quantidade.");
}

// ----- 3. grava tudo dentro de uma transação -----
try {
    $pdo->beginTransaction();

    // 3.1 edição: desfaz o que a venda antiga tinha feito
    if ($idVenda > 0) {
        $consultaAntiga = $pdo->prepare("select status from vendas where id = :id limit 1");
        $consultaAntiga->bindValue(":id", $idVenda, PDO::PARAM_INT);
        $consultaAntiga->execute();
        $vendaAntiga = $consultaAntiga->fetch(PDO::FETCH_OBJ);

        if (!$vendaAntiga) {
            throw new Exception("A venda que você tentou editar não existe mais.");
        }

        // se ela estava concluída o estoque foi descontado na época,
        // então devolvo os itens antigos antes de aplicar os novos
        if ($vendaAntiga->status == "Concluída") {
            $consultaItensAntigos = $pdo->prepare(
                "select produto_id, quantidade from venda_itens where venda_id = :id"
            );
            $consultaItensAntigos->bindValue(":id", $idVenda, PDO::PARAM_INT);
            $consultaItensAntigos->execute();
            $itensAntigos = $consultaItensAntigos->fetchAll(PDO::FETCH_OBJ);

            $devolver = $pdo->prepare(
                "update produtos set estoque = estoque + :quantidade where id = :id"
            );

            foreach ($itensAntigos as $itemAntigo) {
                $devolver->bindValue(":quantidade", $itemAntigo->quantidade, PDO::PARAM_INT);
                $devolver->bindValue(":id", $itemAntigo->produto_id, PDO::PARAM_INT);
                $devolver->execute();
            }
        }

        // apaga os itens antigos, porque os novos são gravados abaixo
        $apagarItens = $pdo->prepare("delete from venda_itens where venda_id = :id");
        $apagarItens->bindValue(":id", $idVenda, PDO::PARAM_INT);
        $apagarItens->execute();
    }

    // 3.2 confere o estoque e calcula o total da venda
    $total = 0;
    $consultaProduto = $pdo->prepare("select titulo, preco, estoque from produtos where id = :id limit 1");

    for ($indice = 0; $indice < count($itens); $indice++) {
        $consultaProduto->bindValue(":id", $itens[$indice]["produto_id"], PDO::PARAM_INT);
        $consultaProduto->execute();
        $produto = $consultaProduto->fetch(PDO::FETCH_OBJ);

        if (!$produto) {
            throw new Exception("Um dos produtos escolhidos não existe mais.");
        }

        if ($status == "Concluída" && $itens[$indice]["quantidade"] > $produto->estoque) {
            throw new Exception(
                "Estoque insuficiente de \"{$produto->titulo}\": " .
                "pedido {$itens[$indice]["quantidade"]}, disponível {$produto->estoque}."
            );
        }

        // guardo o preço atual do produto: é o valor que fica gravado
        // na venda, mesmo que o preço mude depois
        $itens[$indice]["preco"] = $produto->preco;

        $total = $total + ($itens[$indice]["quantidade"] * $produto->preco);
    }

    // 3.3 grava a venda (INSERT ou UPDATE)
    if ($idVenda == 0) {
        $consultaVenda = $pdo->prepare(
            "insert into vendas (cliente_nome, usuario_id, data_venda, total, status)
             values (:cliente, :usuario, NOW(), :total, :status)"
        );
        $consultaVenda->bindValue(":cliente", $clienteNome);
        $consultaVenda->bindValue(":usuario", $_SESSION["usuario"]["id"], PDO::PARAM_INT);
        $consultaVenda->bindValue(":total", $total);
        $consultaVenda->bindValue(":status", $status);
        $consultaVenda->execute();

        // lastInsertId devolve o id que o banco criou para a venda
        $idVenda = $pdo->lastInsertId();
    } else {
        $consultaVenda = $pdo->prepare(
            "update vendas set cliente_nome = :cliente, total = :total, status = :status
             where id = :id limit 1"
        );
        $consultaVenda->bindValue(":cliente", $clienteNome);
        $consultaVenda->bindValue(":total", $total);
        $consultaVenda->bindValue(":status", $status);
        $consultaVenda->bindValue(":id", $idVenda, PDO::PARAM_INT);
        $consultaVenda->execute();
    }

    // 3.4 grava os itens e desconta o estoque
    $inserirItem = $pdo->prepare(
        "insert into venda_itens (venda_id, produto_id, quantidade, valor_unitario)
         values (:venda, :produto, :quantidade, :preco)"
    );

    $descontar = $pdo->prepare(
        "update produtos set estoque = estoque - :quantidade where id = :id"
    );

    foreach ($itens as $item) {
        $inserirItem->bindValue(":venda", $idVenda, PDO::PARAM_INT);
        $inserirItem->bindValue(":produto", $item["produto_id"], PDO::PARAM_INT);
        $inserirItem->bindValue(":quantidade", $item["quantidade"], PDO::PARAM_INT);
        $inserirItem->bindValue(":preco", $item["preco"]);
        $inserirItem->execute();

        if ($status == "Concluída") {
            $descontar->bindValue(":quantidade", $item["quantidade"], PDO::PARAM_INT);
            $descontar->bindValue(":id", $item["produto_id"], PDO::PARAM_INT);
            $descontar->execute();
        }
    }

    // nada deu errado: confirma tudo de uma vez
    $pdo->commit();

    redirecionarCom(
        "listar/venda",
        "success",
        "Venda #{$idVenda} salva com sucesso - " . formatarMoeda($total) . " ({$status})."
    );
} catch (Exception $erro) {
    // deu erro em algum passo: desfaz tudo
    $pdo->rollBack();

    redirecionarCom($voltarPara, "danger", "A venda não foi salva. " . $erro->getMessage());
}
