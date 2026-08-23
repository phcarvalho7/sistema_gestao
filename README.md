# Livraria Nerd - Sistema de Gestão de Vendas

Sistema de gestão de vendas para uma livraria especializada em **Livros, HQs e Mangás**, desenvolvido como projeto acadêmico (Banco de Dados Avançado, Desenvolvimento Web Avançado, Lógica Avançada e Tech Forge).

## Tecnologias utilizadas

- **PHP 8** com PDO (sem frameworks)
- **MariaDB** (CTEs, Views analíticas e Triggers)
- **Bootstrap 5.3** (layout responsivo)
- **TypeScript** (consumo da API e lógica da dashboard)

## Estrutura do projeto

```
sistema_gestao/
├── admin/              -> Painel administrativo (login, CRUDs e dashboard)
│   ├── cadastrar/       -> Formulários de inclusão/edição
│   ├── listar/          -> Listagens
│   ├── salvar/          -> Processamento dos formulários
│   ├── excluir/         -> Exclusão de registros
│   ├── pages/           -> Login e páginas internas
│   ├── js/dist/         -> JavaScript compilado a partir do TypeScript
│   └── index.php        -> Controlador do painel (roteamento + sessão)
├── apis/
│   └── dashboard.php    -> API que entrega os dados brutos de vendas em JSON
├── pages/               -> Páginas da loja pública (catálogo)
├── database/
│   └── livraria_nerd.sql -> Script completo do banco (tabelas, views, trigger e dados)
├── ts/
│   └── dashboard.ts     -> Código-fonte TypeScript da dashboard
├── config.php           -> Conexão PDO com o banco
├── tsconfig.json
└── package.json
```

## Como rodar o projeto (XAMPP)

1. Copie a pasta do projeto para `htdocs` (ex.: `C:\xampp\htdocs\sistema_gestao`).
2. Inicie o **Apache** e o **MySQL/MariaDB** no XAMPP.
3. No phpMyAdmin, crie o banco importando o arquivo `database/livraria_nerd.sql` (ele já cria o banco `livraria_nerd`, as tabelas, a trigger, as views e os dados de exemplo).
4. Confirme os dados de acesso em `config.php` (por padrão: host `localhost`, usuário `root`, senha em branco - padrão do XAMPP).
5. Acesse a loja pública em `http://localhost/sistema_gestao/`.
6. Acesse o painel administrativo em `http://localhost/sistema_gestao/admin/`.
   - **Login:** admin@livrarianerd.com
   - **Senha:** 123456

## Como compilar o TypeScript

O código-fonte fica em `ts/dashboard.ts` e é compilado para `admin/js/dist/dashboard.js` (o projeto já é entregue com o `.js` compilado, mas o comando abaixo deve ser rodado sempre que o `.ts` for alterado):

```bash
npm install
npm run build
```

Para recompilar automaticamente a cada alteração durante o desenvolvimento:

```bash
npm run watch
```

## O que já foi implementado nesta base

**Banco de Dados**
- Views analíticas construídas com **CTE (WITH)** que consolidam os dados brutos de vendas: `vw_faturamento_mensal` (faturamento por mês) e `vw_faturamento_por_tipo` (faturamento por tipo de produto e categoria).
- Trigger `BEFORE UPDATE` na tabela `produtos` que padroniza preço e estoque para sempre positivos (usa `ABS()` caso um valor negativo seja enviado).

**Desenvolvimento Web**
- Interface construída com Bootstrap (navbar, cards, badges, tabelas, modais de confirmação via `confirm()`), com boa usabilidade tanto na loja pública quanto no painel.
- Estrutura de pastas separada por responsabilidade (cadastrar / listar / salvar / excluir), preparando o projeto para os próximos sprints.

**Lógica Avançada (TypeScript)**
- A API (`apis/dashboard.php`) entrega o **array bruto** de itens vendidos (sem nenhuma soma feita no PHP).
- Todo o cálculo é feito em `ts/dashboard.ts` usando **`.reduce()`**: faturamento total, quantidade de itens vendidos, contagem de vendas únicas e faturamento agrupado por tipo (Livro / HQ / Mangá).
- Tratamento de cenários de exceção: quando não há vendas, a dashboard exibe a mensagem "Nenhum dado registrado" em vez de quebrar; valores não numéricos são tratados para nunca gerar `NaN` na tela.

**Tech Forge**
- Consumo da API via `fetch` com `async/await` e `try/catch`.
- Manipulação segura do DOM (toda leitura de elemento verifica `if (elemento)` antes de usá-lo - sem uso do operador `!`).
- Código organizado em funções pequenas e com responsabilidade única (buscar dados, calcular, formatar e renderizar são funções separadas).

## O que fica para os próximos sprints

- CRUD completo de Produtos e Categorias com upload de capa.
- Regras de negócio adicionais para exclusão (algumas já implementadas como base: não é possível excluir uma categoria com produtos vinculados, nem um produto que já tenha vendas).
- Tipagem completa de contratos de interface, uso de `.filter()` e `.map()` e algoritmos de ranking (produto mais vendido) no TypeScript.
- Stored Procedures para busca, filtros e paginação da dashboard.

## Acesso de teste

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | admin@livrarianerd.com | 123456 |
