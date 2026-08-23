<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Dashboard de Vendas</h3>
    <span class="text-muted small">Dados consumidos via API (PHP) e processados em TypeScript</span>
</div>

<div id="dashboard-vazio" class="alert alert-warning d-none">
    Nenhum dado registrado. Cadastre vendas para visualizar os indicadores.
</div>

<div id="dashboard-conteudo">
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Faturamento Total</h6>
                    <h3 id="faturamento-total">--</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Itens Vendidos</h6>
                    <h3 id="itens-vendidos">--</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total de Vendas</h6>
                    <h3 id="total-vendas">--</h3>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Faturamento por Tipo de Produto</h5>
    <div id="faturamento-por-tipo" class="row g-3">
        <p class="text-muted">Carregando...</p>
    </div>
</div>

<script src="js/dist/dashboard.js"></script>
