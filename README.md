# Prosa & Traço - Sistema de Gestão de Vendas

Sistema para uma livraria de **livros, HQs e mangás**, com duas partes:

- **Loja** - catálogo público, busca e página do produto;
- **Painel** - login, três CRUDs completos e uma dashboard de vendas.

Projeto acadêmico das disciplinas de Banco de Dados Avançado,
Desenvolvimento Web Avançado, Lógica Avançada e Tech Forge.

---

## Tecnologias

| Camada | O que foi usado |
|---|---|
| Banco de dados | MariaDB - 5 tabelas, 4 views (com CTE), 3 functions, 3 procedures, 2 triggers |
| Back-end | PHP 8 com PDO, sem framework |
| API | PHP devolvendo JSON a partir de chamadas `CALL` |
| Front-end | Bootstrap 5 + CSS próprio |
| Dashboard | TypeScript (modo `strict`), compilado para JavaScript |

---

## Como rodar no XAMPP

1. Copie a pasta `sistema_gestao` para dentro de `htdocs`
   (ex.: `C:\xampp\htdocs\sistema_gestao`).
2. No XAMPP, inicie o **Apache** e o **MySQL**.
3. Abra o phpMyAdmin → aba **Importar** → escolha
   `database/prosa_traco.sql` → **Executar**.
4. Acesse:
   - Loja: <http://localhost/sistema_gestao/>
   - Painel: <http://localhost/sistema_gestao/admin/>

> O sistema usa endereços amigáveis pelo `.htaccess`, então o
> **mod_rewrite** do Apache precisa estar ativo (no XAMPP já vem).

### Acesso ao painel

| E-mail | Senha |
|---|---|
| admin@prosaetraco.com | 123456 |
| marina@prosaetraco.com | 123456 |

### Recompilar o TypeScript (opcional)

O JavaScript já vem compilado. Só é necessário rodar isto se você
alterar o arquivo `ts/dashboard.ts`:

```bash
npm install
npm run build
```

---

## Estrutura

```
sistema_gestao/
├── index.php                 -> controlador da loja
├── config.php                -> conexão com o banco
├── funcoes.php               -> funções compartilhadas
├── .htaccess                 -> endereços amigáveis
├── templates/                -> topo e rodapé da loja
├── pages/                    -> telas da loja
├── css/style.css
├── apis/dashboard.php        -> API em JSON
├── ts/dashboard.ts           -> código-fonte da dashboard
├── database/prosa_traco.sql  -> o banco completo
├── EXPLICACAO.md             -> explicação completa do código e do banco
└── admin/
    ├── index.php             -> login + roteamento do painel
    ├── functions.php
    ├── templates/            -> menu lateral e barra de cima
    ├── pages/                -> login, dashboard e erro
    ├── listar/               -> produto, categoria, venda
    ├── cadastrar/            -> formulários (cadastro e edição)
    ├── salvar/               -> gravação no banco
    ├── excluir/              -> exclusão com regras de negócio
    ├── css/style.css
    └── js/dist/dashboard.js  -> TypeScript compilado
```

---

## Ordem dos commits

O projeto foi entregue em quatro partes, e cada uma funciona sozinha:

| Parte | O que entra |
|---|---|
| 1 | Banco de dados + documentação |
| 2 | Loja pública (catálogo, busca, produto) |
| 3 | Painel: login e os 3 CRUDs |
| 4 | API em JSON + dashboard em TypeScript |

A explicação de tudo (banco, PHP, API e TypeScript) está no arquivo
**`EXPLICACAO.md`**.

---

## Onde está cada item das disciplinas

**Banco de Dados Avançado** - `database/prosa_traco.sql`

| Item | Onde |
|---|---|
| Views analíticas com CTE | `vw_faturamento_mensal`, `vw_faturamento_por_tipo`, `vw_ranking_produtos` |
| View centralizando várias tabelas | `vw_painel_geral` (5 tabelas) |
| Stored procedures com busca, filtro e paginação | `sp_dashboard_itens`, `sp_dashboard_totais`, `sp_listar_produtos` |
| Function reutilizável | `fn_subtotal_item`, `fn_faturamento_produto`, `fn_situacao_estoque` |
| Trigger BEFORE UPDATE | `trg_produtos_valores_positivos` |

**Desenvolvimento Web Avançado**

| Item | Onde |
|---|---|
| Template para facilitar a manutenção | `templates/` e `admin/templates/` |
| Estrutura de pastas definida | `listar` / `cadastrar` / `salvar` / `excluir` |
| 3 CRUDs completos | Produtos, Categorias e Vendas |
| Regras de exclusão com mensagem clara | `admin/excluir/*.php` |
| Componentes do Bootstrap | menu, dropdown, cards, tabelas, alerts, paginação, formulários |

**Lógica Avançada** - `ts/dashboard.ts`

| Item | Onde |
|---|---|
| Tipagem e contratos de interface | `ItemVendaApi`, `RespostaApi`, `ItemVenda` |
| `reduce` | `somarFaturamento`, `somarQuantidade`, `montarRanking` |
| `filter` | `filtrarPorTipo`, `filtrarPorMes` |
| `map` | `converterItens`, `faturamentoPorMes` |
| Ranking com objeto de contagem | `montarRanking` |
| Tratamento de exceções | `try/catch`, `paraNumero`, blocos de "sem dados" |

**Tech Forge**

| Item | Onde |
|---|---|
| API com `fetch` + `async/await` + `try/catch` | `carregarDashboard` |
| Integração XAMPP + compilação do TypeScript | `npm run build` |
| Manipulação segura do DOM | `escreverTexto`, `escreverHtml` (sempre com `if (elemento)`) |
| Código organizado em funções pequenas | seções numeradas de 1 a 11 do `dashboard.ts` |
