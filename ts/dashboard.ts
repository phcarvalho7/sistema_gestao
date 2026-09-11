// Dashboard de vendas - Prosa & Traço
//
// O caminho do dado é este:
//   MariaDB (stored procedures)
//     -> apis/dashboard.php   (só devolve o JSON, sem calcular)
//     -> este arquivo         (fetch + map + filter + reduce)
//     -> tela
//
// Ou seja: TODA a conta que aparece na dashboard é feita aqui.
// Compilado para admin/js/dist/dashboard.js com "npm run build".

// ----- 1. interfaces: o formato do JSON que o PHP manda -----

// Um item vendido, como vem da API.
// Os campos numéricos chegam como texto ("24.90"), porque é assim
// que o PDO devolve os números do MySQL. Por isso todos eles são
// convertidos antes de qualquer conta.
interface ItemVendaApi {
    venda_id: string;
    data_venda: string;
    cliente_nome: string;
    vendedor: string;
    produto_id: string;
    titulo: string;
    tipo: string;
    categoria: string;
    quantidade: string;
    valor_unitario: string;
}

// A resposta completa da API.
interface RespostaApi {
    sucesso: boolean;
    total_itens: number;
    total_vendas: number;
    itens: ItemVendaApi[];
}

// ----- 2. interfaces: o formato usado aqui dentro -----

// Item já convertido para número, com o subtotal calculado.
interface ItemVenda {
    vendaId: number;
    produtoId: number;
    data: string;
    titulo: string;
    tipo: string;
    categoria: string;
    quantidade: number;
    subtotal: number;
}

// Os quatro números grandes do topo da tela.
interface Resumo {
    faturamento: number;
    itensVendidos: number;
    totalVendas: number;
    ticketMedio: number;
}

// Uma barra do gráfico (um mês ou um tipo de produto).
interface Barra {
    rotulo: string;
    valor: number;
}

// Uma linha do ranking de produtos.
interface ProdutoRanking {
    titulo: string;
    tipo: string;
    unidades: number;
    faturamento: number;
}

// Objeto de contagem usado no ranking: a chave é o id do produto e
// o valor é o total acumulado daquele produto.
// Ex.: { "1": { titulo: "One Piece", unidades: 8, ... } }
interface ContagemProdutos {
    [chave: string]: ProdutoRanking;
}

// ----- 3. constantes e estado da tela -----

const URL_API: string = "../apis/dashboard.php";

const MESES: string[] = [
    "jan", "fev", "mar", "abr", "mai", "jun",
    "jul", "ago", "set", "out", "nov", "dez"
];

// Guarda os itens que vieram da API e o tipo escolhido nos botões.
// Quando o usuário clica em "Mangás", nada é buscado de novo: a
// tela é redesenhada filtrando esta lista.
let itensCarregados: ItemVenda[] = [];
let tipoSelecionado: string = "";

// ----- 4. conversão e formatação -----

// Converte texto em número com segurança.
// Se vier vazio ou inválido, devolve 0 - assim a tela nunca
// mostra "NaN".
function paraNumero(valor: string): number {
    const numero: number = Number(valor);

    if (isNaN(numero)) {
        return 0;
    }

    return numero;
}

// Formata um valor como dinheiro: 1379.2 vira "R$ 1.379,20".
function formatarMoeda(valor: number): string {
    return valor.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

// Transforma "2026-08" em "ago/26".
function formatarMes(mes: string): string {
    const partes: string[] = mes.split("-");
    const numeroMes: number = paraNumero(partes[1]);
    const nome: string = MESES[numeroMes - 1];

    return nome + "/" + partes[0].substring(2);
}

// Troca os caracteres especiais do HTML.
// Serve para o título de um produto nunca ser interpretado como
// código quando é colocado na tela.
function escaparTexto(texto: string): string {
    return texto.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

// Calcula quanto uma parte representa do total, em porcentagem.
function porcentagem(parte: number, total: number): number {
    if (total <= 0) {
        return 0;
    }

    return (parte / total) * 100;
}

// ----- 5. map: transforma os dados brutos da API -----

// Usa map() para transformar cada item da API em um item da
// dashboard: converte os textos em números e já calcula o subtotal
// (quantidade x valor unitário).
function converterItens(itensApi: ItemVendaApi[]): ItemVenda[] {
    return itensApi.map(function (item: ItemVendaApi): ItemVenda {
        const quantidade: number = paraNumero(item.quantidade);
        const valorUnitario: number = paraNumero(item.valor_unitario);

        return {
            vendaId: paraNumero(item.venda_id),
            produtoId: paraNumero(item.produto_id),
            data: item.data_venda,
            titulo: item.titulo,
            tipo: item.tipo,
            categoria: item.categoria,
            quantidade: quantidade,
            subtotal: quantidade * valorUnitario
        };
    });
}

// ----- 6. filter: separa os dados por tipo e por mês -----

// Deixa apenas os itens de um tipo (Livro, HQ ou Mangá).
function filtrarPorTipo(itens: ItemVenda[], tipo: string): ItemVenda[] {
    if (tipo === "") {
        return itens;
    }

    return itens.filter(function (item: ItemVenda): boolean {
        return item.tipo === tipo;
    });
}

// Deixa apenas os itens de um mês, no formato "2026-08".
function filtrarPorMes(itens: ItemVenda[], mes: string): ItemVenda[] {
    return itens.filter(function (item: ItemVenda): boolean {
        return item.data.substring(0, 7) === mes;
    });
}

// ----- 7. reduce: as contas da dashboard -----

// Soma o faturamento (soma de todos os subtotais).
function somarFaturamento(itens: ItemVenda[]): number {
    return itens.reduce(function (total: number, item: ItemVenda): number {
        return total + item.subtotal;
    }, 0);
}

// Soma quantas unidades foram vendidas.
function somarQuantidade(itens: ItemVenda[]): number {
    return itens.reduce(function (total: number, item: ItemVenda): number {
        return total + item.quantidade;
    }, 0);
}

// Conta quantas vendas diferentes existem na lista.
// É preciso contar assim porque uma venda com 3 produtos aparece
// em 3 linhas, todas com o mesmo venda_id.
function contarVendas(itens: ItemVenda[]): number {
    const encontrados: number[] = [];

    itens.forEach(function (item: ItemVenda): void {
        if (encontrados.indexOf(item.vendaId) === -1) {
            encontrados.push(item.vendaId);
        }
    });

    return encontrados.length;
}

// Junta os quatro números principais em um só objeto.
function calcularResumo(itens: ItemVenda[]): Resumo {
    const faturamento: number = somarFaturamento(itens);
    const totalVendas: number = contarVendas(itens);
    let ticketMedio: number = 0;

    if (totalVendas > 0) {
        ticketMedio = faturamento / totalVendas;
    }

    return {
        faturamento: faturamento,
        itensVendidos: somarQuantidade(itens),
        totalVendas: totalVendas,
        ticketMedio: ticketMedio
    };
}

// Lista os meses que aparecem nos dados, em ordem.
function listarMeses(itens: ItemVenda[]): string[] {
    const meses: string[] = [];

    itens.forEach(function (item: ItemVenda): void {
        const mes: string = item.data.substring(0, 7);

        if (meses.indexOf(mes) === -1) {
            meses.push(mes);
        }
    });

    return meses.sort();
}

// Faturamento de cada mês, pronto para virar barra no gráfico.
function faturamentoPorMes(itens: ItemVenda[]): Barra[] {
    return listarMeses(itens).map(function (mes: string): Barra {
        return {
            rotulo: formatarMes(mes),
            valor: somarFaturamento(filtrarPorMes(itens, mes))
        };
    });
}

// Faturamento de cada tipo de produto (Livro, HQ e Mangá).
function faturamentoPorTipo(itens: ItemVenda[]): Barra[] {
    const tipos: string[] = ["Livro", "HQ", "Mangá"];
    const barras: Barra[] = [];

    tipos.forEach(function (tipo: string): void {
        const doTipo: ItemVenda[] = filtrarPorTipo(itens, tipo);

        if (doTipo.length > 0) {
            barras.push({ rotulo: tipo, valor: somarFaturamento(doTipo) });
        }
    });

    return barras;
}

// RANKING DOS MAIS VENDIDOS
//
// aqui está o algoritmo de frequência: o reduce vai montando um objeto
// de contagem em que a CHAVE é o id do produto. Cada vez que o produto
// aparece, a quantidade dele é somada. Depois o objeto vira lista e é
// ordenado.
function montarRanking(itens: ItemVenda[]): ProdutoRanking[] {
    const contagem: ContagemProdutos = itens.reduce(
        function (acumulado: ContagemProdutos, item: ItemVenda): ContagemProdutos {
            // A chave é o id do produto: é assim que o mesmo produto,
            // vendido em vendas diferentes, cai no mesmo lugar.
            const chave: string = String(item.produtoId);

            if (acumulado[chave] === undefined) {
                // Primeira vez que este produto aparece
                acumulado[chave] = {
                    titulo: item.titulo,
                    tipo: item.tipo,
                    unidades: item.quantidade,
                    faturamento: item.subtotal
                };
            } else {
                // Já apareceu antes: soma no que já estava lá
                acumulado[chave].unidades = acumulado[chave].unidades + item.quantidade;
                acumulado[chave].faturamento = acumulado[chave].faturamento + item.subtotal;
            }

            return acumulado;
        },
        {}
    );

    // Object.keys pega todas as chaves do objeto; o map troca cada
    // chave pelo produto correspondente.
    const lista: ProdutoRanking[] = Object.keys(contagem).map(
        function (chave: string): ProdutoRanking {
            return contagem[chave];
        }
    );

    // Ordena do que vendeu mais unidades para o que vendeu menos.
    lista.sort(function (a: ProdutoRanking, b: ProdutoRanking): number {
        return b.unidades - a.unidades;
    });

    return lista;
}

// ----- 8. funções que mexem na tela -----
// todas conferem se o elemento existe antes de usar

function escreverTexto(id: string, texto: string): void {
    const elemento: HTMLElement | null = document.getElementById(id);

    if (elemento) {
        elemento.textContent = texto;
    }
}

function escreverHtml(id: string, html: string): void {
    const elemento: HTMLElement | null = document.getElementById(id);

    if (elemento) {
        elemento.innerHTML = html;
    }
}

function mostrarOuEsconder(id: string, mostrar: boolean): void {
    const elemento: HTMLElement | null = document.getElementById(id);

    if (elemento) {
        if (mostrar) {
            elemento.classList.remove("d-none");
        } else {
            elemento.classList.add("d-none");
        }
    }
}

// ----- 9. desenho dos blocos -----

// Monta o HTML de uma lista de barras (usada duas vezes na tela).
function montarBarras(barras: Barra[]): string {
    if (barras.length === 0) {
        return '<p class="texto-mudo texto-mini mb-0">Nenhum dado no período.</p>';
    }

    // A maior barra ocupa 100% da largura; as outras são proporcionais.
    let maior: number = 0;

    barras.forEach(function (barra: Barra): void {
        if (barra.valor > maior) {
            maior = barra.valor;
        }
    });

    let html: string = "";

    barras.forEach(function (barra: Barra): void {
        const largura: number = porcentagem(barra.valor, maior);

        html = html +
            '<div>' +
                '<div class="barra-linha">' +
                    '<span>' + escaparTexto(barra.rotulo) + '</span>' +
                    '<strong>' + formatarMoeda(barra.valor) + '</strong>' +
                '</div>' +
                '<div class="barra-fundo">' +
                    '<div class="barra-valor" style="width: ' + largura.toFixed(1) + '%"></div>' +
                '</div>' +
            '</div>';
    });

    return html;
}

// Monta o HTML do ranking (os 5 primeiros).
function montarRankingHtml(itens: ItemVenda[]): string {
    const ranking: ProdutoRanking[] = montarRanking(itens);

    if (ranking.length === 0) {
        return '<p class="texto-mudo texto-mini mb-0">Nenhum produto vendido no período.</p>';
    }

    let html: string = '<table class="table tabela mb-0"><thead><tr>' +
        '<th>#</th><th>Produto</th><th>Tipo</th>' +
        '<th class="text-center">Unidades</th><th class="text-end">Faturamento</th>' +
        '</tr></thead><tbody>';

    for (let posicao = 0; posicao < ranking.length && posicao < 5; posicao++) {
        const produto: ProdutoRanking = ranking[posicao];

        html = html +
            '<tr>' +
                '<td class="texto-mudo">' + (posicao + 1) + '</td>' +
                '<td><strong>' + escaparTexto(produto.titulo) + '</strong></td>' +
                '<td class="texto-mudo">' + escaparTexto(produto.tipo) + '</td>' +
                '<td class="text-center">' + produto.unidades + '</td>' +
                '<td class="text-end">' + formatarMoeda(produto.faturamento) + '</td>' +
            '</tr>';
    }

    return html + '</tbody></table>';
}

// Redesenha a tela inteira usando os itens já carregados e o tipo
// escolhido nos botões.
function desenharDashboard(): void {
    const itens: ItemVenda[] = filtrarPorTipo(itensCarregados, tipoSelecionado);

    // Situação especial: não existem vendas para esse filtro.
    if (itens.length === 0) {
        mostrarOuEsconder("dashboard-vazio", true);
        mostrarOuEsconder("dashboard-conteudo", false);
        return;
    }

    mostrarOuEsconder("dashboard-vazio", false);
    mostrarOuEsconder("dashboard-conteudo", true);

    const resumo: Resumo = calcularResumo(itens);

    escreverTexto("kpi-faturamento", formatarMoeda(resumo.faturamento));
    escreverTexto("kpi-ticket", formatarMoeda(resumo.ticketMedio));
    escreverTexto("kpi-itens", String(resumo.itensVendidos));
    escreverTexto("kpi-vendas", String(resumo.totalVendas));

    escreverHtml("grafico-meses", montarBarras(faturamentoPorMes(itens)));
    escreverHtml("grafico-tipos", montarBarras(faturamentoPorTipo(itens)));
    escreverHtml("ranking", montarRankingHtml(itens));
}

// ----- 10. busca os dados na API -----

// Monta o endereço da API com os filtros da tela.
// O período é convertido em uma data inicial.
function montarEndereco(): string {
    let endereco: string = URL_API + "?limite=1000";

    const campoBusca: HTMLElement | null = document.getElementById("filtro-busca");
    const campoPeriodo: HTMLElement | null = document.getElementById("filtro-periodo");

    if (campoBusca) {
        const busca: string = (campoBusca as HTMLInputElement).value;

        if (busca !== "") {
            endereco = endereco + "&busca=" + encodeURIComponent(busca);
        }
    }

    if (campoPeriodo) {
        const dias: number = paraNumero((campoPeriodo as HTMLSelectElement).value);

        if (dias > 0) {
            const data: Date = new Date();
            data.setDate(data.getDate() - dias);
            endereco = endereco + "&inicio=" + data.toISOString().substring(0, 10);
        }
    }

    return endereco;
}

// Busca os dados na API e manda desenhar a tela.
//
// async/await deixa o código com cara de sequência, mesmo sendo
// assíncrono. O try/catch garante que a tela não quebra se a API
// estiver fora do ar ou o banco der erro.
async function carregarDashboard(): Promise<void> {
    mostrarOuEsconder("dashboard-carregando", true);
    mostrarOuEsconder("dashboard-erro", false);

    try {
        const resposta: Response = await fetch(montarEndereco());

        if (!resposta.ok) {
            throw new Error("A API respondeu com erro (código " + resposta.status + ").");
        }

        const dados: RespostaApi = await resposta.json() as RespostaApi;

        itensCarregados = converterItens(dados.itens);

        desenharDashboard();

        escreverTexto(
            "dashboard-atualizado",
            "Atualizado às " + new Date().toLocaleTimeString("pt-BR") +
            " - " + dados.total_itens + " itens no filtro"
        );
    } catch (erro) {
        // Qualquer problema cai aqui: rede, JSON inválido ou erro do banco.
        console.error("Erro na dashboard:", erro);

        mostrarOuEsconder("dashboard-erro", true);
        mostrarOuEsconder("dashboard-conteudo", false);
        mostrarOuEsconder("dashboard-vazio", false);
        escreverTexto("dashboard-atualizado", "Não foi possível atualizar.");
    }

    mostrarOuEsconder("dashboard-carregando", false);
}

// ----- 11. liga os botões da tela -----

function ligarBotoes(): void {
    const botaoAplicar: HTMLElement | null = document.getElementById("btn-aplicar");

    if (botaoAplicar) {
        botaoAplicar.addEventListener("click", function (): void {
            carregarDashboard();
        });
    }

    // Botões de tipo: só refiltram o que já está carregado.
    const botoesTipo = document.querySelectorAll(".btn-tipo");

    for (let indice = 0; indice < botoesTipo.length; indice++) {
        const botao: HTMLElement = botoesTipo[indice] as HTMLElement;

        botao.addEventListener("click", function (): void {
            const tipo: string | null = botao.getAttribute("data-tipo");

            if (tipo === null) {
                return;
            }

            tipoSelecionado = tipo;

            // Marca o botão clicado e desmarca os outros
            for (let outro = 0; outro < botoesTipo.length; outro++) {
                botoesTipo[outro].classList.remove("btn-primario");
                botoesTipo[outro].classList.add("btn-contorno");
            }

            botao.classList.remove("btn-contorno");
            botao.classList.add("btn-primario");

            desenharDashboard();
        });
    }
}

// Quando a página termina de carregar, liga os botões e busca os dados.
document.addEventListener("DOMContentLoaded", function (): void {
    ligarBotoes();
    carregarDashboard();
});
