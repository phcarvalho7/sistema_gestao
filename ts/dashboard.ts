/**
 * Dashboard de Vendas - Livraria Nerd
 *
 * Este arquivo consome a API em PHP (apis/dashboard.php), que devolve o
 * ARRAY BRUTO de itens vendidos (sem nenhuma agregação feita no banco).
 * Todo o processamento - somas, agrupamentos e formatação - é feito aqui
 * em TypeScript, usando reduce() sobre os dados recebidos.
 */

// Formato exato de cada item retornado pela API em PHP.
interface ItemVendaAPI {
    venda_id: number;
    data_venda: string;
    cliente_nome: string;
    produto_id: number;
    titulo: string;
    tipo: string;
    categoria: string;
    quantidade: string;
    valor_unitario: string;
}

// Estrutura consolidada usada para renderizar a dashboard.
interface ResumoDashboard {
    faturamentoTotal: number;
    totalItensVendidos: number;
    totalVendas: number;
    faturamentoPorTipo: Record<string, number>;
}

const URL_API_DASHBOARD = "../apis/dashboard.php";

/**
 * Busca os dados de vendas na API em PHP.
 * Usa fetch + async/await e trata falhas de rede ou do banco com try/catch,
 * para que a apresentação não quebre caso o servidor esteja fora do ar.
 */
async function buscarDadosVendas(): Promise<ItemVendaAPI[]> {
    try {
        const resposta = await fetch(URL_API_DASHBOARD);

        if (!resposta.ok) {
            throw new Error(`Falha ao consultar a API (HTTP ${resposta.status})`);
        }

        const dados = await resposta.json();

        return Array.isArray(dados) ? dados : [];
    } catch (erro) {
        console.error("Erro ao buscar dados da dashboard:", erro);
        return [];
    }
}

/**
 * Converte um valor vindo do PHP (às vezes string) para number,
 * evitando NaN caso o dado venha vazio ou inválido.
 */
function paraNumero(valor: string | number): number {
    const numero = Number(valor);
    return Number.isNaN(numero) ? 0 : numero;
}

/**
 * Soma o faturamento total (quantidade x valor unitário) usando reduce.
 */
function calcularFaturamentoTotal(itens: ItemVendaAPI[]): number {
    return itens.reduce((totalAcumulado, item) => {
        const subtotal = paraNumero(item.quantidade) * paraNumero(item.valor_unitario);
        return totalAcumulado + subtotal;
    }, 0);
}

/**
 * Soma a quantidade total de itens vendidos usando reduce.
 */
function calcularTotalItensVendidos(itens: ItemVendaAPI[]): number {
    return itens.reduce((totalAcumulado, item) => {
        return totalAcumulado + paraNumero(item.quantidade);
    }, 0);
}

/**
 * Conta o número de vendas (pedidos) distintos usando reduce,
 * já que um mesmo venda_id pode aparecer em várias linhas (um item por linha).
 */
function contarVendasUnicas(itens: ItemVendaAPI[]): number {
    const idsUnicos = itens.reduce((acumulados: number[], item) => {
        if (!acumulados.includes(item.venda_id)) {
            acumulados.push(item.venda_id);
        }
        return acumulados;
    }, []);

    return idsUnicos.length;
}

/**
 * Agrupa o faturamento por tipo de produto (Livro, HQ, Mangá) usando reduce.
 */
function calcularFaturamentoPorTipo(itens: ItemVendaAPI[]): Record<string, number> {
    return itens.reduce((acumulado: Record<string, number>, item) => {
        const subtotal = paraNumero(item.quantidade) * paraNumero(item.valor_unitario);
        const tipo = item.tipo || "Outro";

        acumulado[tipo] = (acumulado[tipo] || 0) + subtotal;

        return acumulado;
    }, {});
}

/**
 * Consolida todos os cálculos em um único objeto de resumo.
 */
function montarResumoDashboard(itens: ItemVendaAPI[]): ResumoDashboard {
    return {
        faturamentoTotal: calcularFaturamentoTotal(itens),
        totalItensVendidos: calcularTotalItensVendidos(itens),
        totalVendas: contarVendasUnicas(itens),
        faturamentoPorTipo: calcularFaturamentoPorTipo(itens)
    };
}

/**
 * Formata um número no padrão de moeda brasileiro (R$).
 */
function formatarMoeda(valor: number): string {
    return valor.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

/**
 * Atualiza o texto de um elemento pelo id, sem quebrar caso o
 * elemento não exista no HTML (manipulação segura do DOM, sem uso de "!").
 */
function atualizarTexto(idElemento: string, texto: string): void {
    const elemento = document.getElementById(idElemento);

    if (elemento) {
        elemento.textContent = texto;
    }
}

/**
 * Alterna a exibição entre o aviso de "sem dados" e o conteúdo da dashboard.
 */
function alternarEstadoVazio(estaVazio: boolean): void {
    const avisoVazio = document.getElementById("dashboard-vazio");
    const conteudo = document.getElementById("dashboard-conteudo");

    if (avisoVazio) {
        avisoVazio.classList.toggle("d-none", !estaVazio);
    }

    if (conteudo) {
        conteudo.classList.toggle("d-none", estaVazio);
    }
}

/**
 * Renderiza os cartões de faturamento por tipo de produto.
 */
function renderizarFaturamentoPorTipo(faturamentoPorTipo: Record<string, number>): void {
    const container = document.getElementById("faturamento-por-tipo");

    if (!container) {
        return;
    }

    container.innerHTML = "";

    const tipos = Object.keys(faturamentoPorTipo);

    if (tipos.length === 0) {
        container.innerHTML = '<p class="text-muted">Nenhum dado registrado.</p>';
        return;
    }

    tipos.forEach((tipo) => {
        const card = document.createElement("div");
        card.className = "col-12 col-md-4";
        card.innerHTML = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">${tipo}</h6>
                    <h4 class="mb-0">${formatarMoeda(faturamentoPorTipo[tipo])}</h4>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

/**
 * Renderiza todo o resumo da dashboard na tela.
 * Trata o cenário de exceção em que não há nenhuma venda registrada.
 */
function renderizarDashboard(itens: ItemVendaAPI[]): void {
    if (!itens || itens.length === 0) {
        alternarEstadoVazio(true);
        return;
    }

    alternarEstadoVazio(false);

    const resumo = montarResumoDashboard(itens);

    atualizarTexto("faturamento-total", formatarMoeda(resumo.faturamentoTotal));
    atualizarTexto("itens-vendidos", String(resumo.totalItensVendidos));
    atualizarTexto("total-vendas", String(resumo.totalVendas));

    renderizarFaturamentoPorTipo(resumo.faturamentoPorTipo);
}

/**
 * Ponto de entrada: busca os dados e manda renderizar a dashboard.
 */
async function iniciarDashboard(): Promise<void> {
    const itens = await buscarDadosVendas();
    renderizarDashboard(itens);
}

document.addEventListener("DOMContentLoaded", () => {
    iniciarDashboard();
});
