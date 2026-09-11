<?php
// Página inicial da loja.
//
// A lista de mais vendidos não é calculada aqui: ela vem pronta da view
// vw_ranking_produtos, criada no banco com CTE (WITH). O PHP só lê e mostra.

// se alguém abrir este arquivo direto pelo endereço não existe conexão
// com o banco, então ele não faz nada
if (!isset($pdo)) {
    exit;
}

$sql = "select p.*, c.nome as categoria
        from vw_ranking_produtos r
        inner join produtos p on p.id = r.produto_id
        inner join categorias c on c.id = p.categoria_id
        limit 8";

$consulta = $pdo->prepare($sql);
$consulta->execute();
$maisVendidos = $consulta->fetchAll(PDO::FETCH_OBJ);
?>

<!-- CHAMADA DO TOPO -->
<section class="capa-home text-center">
    <div class="container py-5">
        <h1 class="titulo-grande">Livros, HQs e mangás em um só lugar</h1>
        <p class="texto-mudo">Confira o catálogo da loja, com o estoque sempre atualizado.</p>

        <div class="mt-4">
            <a href="produtos" class="btn btn-primario btn-lg">Ver catálogo</a>
            <a href="produtos?tipo=Mang%C3%A1" class="btn btn-contorno btn-lg">Mangás</a>
            <a href="produtos?tipo=HQ" class="btn btn-contorno btn-lg">HQs</a>
        </div>
    </div>
</section>

<!-- MAIS VENDIDOS -->
<section class="container mt-5">
    <h3>Mais vendidos</h3>
    <p class="texto-mudo">Ranking calculado no banco de dados pela view vw_ranking_produtos.</p>

    <div class="row g-3 mt-2">
        <?php if (count($maisVendidos) == 0) { ?>
            <div class="col-12">
                <div class="aviso-vazio">
                    Nenhuma venda registrada ainda. Quando houver vendas, o ranking aparece aqui.
                </div>
            </div>
        <?php } ?>

        <?php foreach ($maisVendidos as $produto) {
            mostrarCardProduto($produto);
        } ?>
    </div>
</section>
