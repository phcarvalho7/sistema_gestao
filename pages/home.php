<?php
// Dashboard de vendas (tela inicial do painel).
//
// IMPORTANTE: este arquivo é só o esqueleto da tela. Ele não faz
// consulta nenhuma e não calcula nada. Todos os espaços em branco
// (os id="...") são preenchidos pelo js/dist/dashboard.js, que é o
// TypeScript compilado.

if (!isset($pdo)) {
    exit;
}
?>

<!-- filtros -->
<div class="card p-3 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-5">
            <label for="filtro-busca" class="form-label">Buscar por produto ou cliente</label>
            <input type="search" id="filtro-busca" class="form-control">
        </div>
        <div class="col-6 col-md-3">
            <label for="filtro-periodo" class="form-label">Período</label>
            <select id="filtro-periodo" class="form-select">
                <option value="0">Todo o histórico</option>
                <option value="30">Últimos 30 dias</option>
                <option value="90">Últimos 90 dias</option>
                <option value="180">Últimos 6 meses</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <button type="button" id="btn-aplicar" class="btn btn-primario w-100">Aplicar</button>
        </div>
        <div class="col-12 col-md-2 text-md-end">
            <span class="texto-mudo texto-mini" id="dashboard-atualizado">Carregando...</span>
        </div>
    </div>

    <!-- estes botões não buscam nada no servidor: o TypeScript filtra a
         lista que já está na memória (função filtrarPorTipo) -->
    <div class="d-flex flex-wrap gap-2 mt-3">
        <button type="button" class="btn btn-sm btn-primario btn-tipo" data-tipo="">Todos os tipos</button>
        <button type="button" class="btn btn-sm btn-contorno btn-tipo" data-tipo="Livro">Livros</button>
        <button type="button" class="btn btn-sm btn-contorno btn-tipo" data-tipo="HQ">HQs</button>
        <button type="button" class="btn btn-sm btn-contorno btn-tipo" data-tipo="Mangá">Mangás</button>
    </div>
</div>

<!-- avisos de situação -->
<div id="dashboard-carregando" class="card p-5 text-center mb-4">
    <span class="texto-mudo">Consultando o banco de dados...</span>
</div>

<div id="dashboard-erro" class="alert alert-danger d-none">
    Não foi possível carregar os dados da dashboard. Verifique se o MySQL está ligado.
</div>

<div id="dashboard-vazio" class="card p-5 text-center d-none mb-4">
    <h6>Nenhum dado registrado</h6>
    <p class="texto-mudo texto-mini mb-3">
        Não há vendas concluídas para esse filtro. Registre uma venda ou aumente o período.
    </p>
    <div>
        <a href="cadastrar/venda" class="btn btn-primario btn-sm">Registrar venda</a>
    </div>
</div>

<!-- conteúdo da dashboard -->
<div id="dashboard-conteudo" class="d-none">

    <!-- os quatro números principais -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card p-3">
                <span class="texto-mudo texto-mini">Faturamento</span>
                <span class="numero-grande" id="kpi-faturamento">-</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3">
                <span class="texto-mudo texto-mini">Ticket médio</span>
                <span class="numero-grande" id="kpi-ticket">-</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3">
                <span class="texto-mudo texto-mini">Itens vendidos</span>
                <span class="numero-grande" id="kpi-itens">-</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card p-3">
                <span class="texto-mudo texto-mini">Vendas</span>
                <span class="numero-grande" id="kpi-vendas">-</span>
            </div>
        </div>
    </div>

    <!-- gráficos de barras -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card p-3 h-100">
                <h6>Faturamento por mês</h6>
                <p class="texto-mudo texto-mini">Agrupado no TypeScript a partir dos itens da API.</p>
                <div class="lista-barras" id="grafico-meses"></div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card p-3 h-100">
                <h6>Faturamento por tipo</h6>
                <p class="texto-mudo texto-mini">Livros, HQs e mangás.</p>
                <div class="lista-barras" id="grafico-tipos"></div>
            </div>
        </div>
    </div>

    <!-- ranking -->
    <div class="card p-3">
        <h6>Produtos mais vendidos</h6>
        <p class="texto-mudo texto-mini">
            Ranking montado com um objeto de contagem (chave = id do produto).
        </p>
        <div id="ranking"></div>
    </div>
</div>

<!-- JavaScript gerado a partir de ts/dashboard.ts -->
<script src="js/dist/dashboard.js"></script>
