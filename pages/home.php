<div class="hero text-center text-white">
    <div class="container py-5">
        <h1 class="display-5 fw-bold">Bem-vindo à Livraria Nerd</h1>
        <p class="lead">Livros, HQs e Mangás para todos os gostos, em um só lugar.</p>
        <a href="produtos" class="btn btn-warning btn-lg mt-2">Ver catálogo completo</a>
    </div>
</div>

<div class="container my-5">
    <h3 class="mb-4">Destaques</h3>
    <div class="row g-3">
        <?php
            $sql = "select p.*, c.nome as categoria
            from produtos p
            inner join categorias c on c.id = p.categoria_id
            order by p.criado_em desc
            limit 8";
            $consulta = $pdo->prepare($sql);
            $consulta->execute();

            $dadosProdutos = $consulta->fetchAll(PDO::FETCH_OBJ);

            foreach ($dadosProdutos as $produto) {
                renderizarCardProduto($produto);
            }
        ?>
    </div>
</div>
