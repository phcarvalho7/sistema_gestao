<?php
    // API que alimenta a dashboard administrativa.
    // Entrega os dados BRUTOS (item a item) para que o front-end em
    // TypeScript faça os cálculos (reduce) e trate os cenários de exceção.
    header('Content-Type: application/json; charset=utf-8');

    require "../config.php";

    $sql = "select
                v.id as venda_id,
                v.data_venda,
                v.cliente_nome,
                p.id as produto_id,
                p.titulo,
                p.tipo,
                c.nome as categoria,
                vi.quantidade,
                vi.valor_unitario
            from vendas v
            inner join venda_itens vi on vi.venda_id = v.id
            inner join produtos p on p.id = vi.produto_id
            inner join categorias c on c.id = p.categoria_id
            where v.status = 'Concluída'
            order by v.data_venda desc";

    $consulta = $pdo->prepare($sql);
    $consulta->execute();

    $dadosVendas = $consulta->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($dadosVendas);
