-- Banco de dados do sistema Prosa & Traço (MariaDB / XAMPP)
--
-- Este script cria tudo de uma vez, na ordem abaixo:
--   1. tabelas + dados de exemplo
--   2. funções (FUNCTION)
--   3. triggers (BEFORE INSERT e BEFORE UPDATE)
--   4. views analíticas feitas com CTE (WITH)
--   5. view que centraliza dados de 5 tabelas
--   6. stored procedures de busca, filtro e paginação
--
-- Para importar: phpMyAdmin -> Importar -> escolher este arquivo

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `prosa_traco`;
CREATE DATABASE `prosa_traco` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `prosa_traco`;

-- ===== 1. TABELAS =====

-- categorias: os gêneros, usados por livros, HQs e mangás
CREATE TABLE `categorias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categorias` (`id`, `nome`) VALUES
(1, 'Ação'),
(2, 'Aventura'),
(3, 'Romance'),
(4, 'Terror'),
(5, 'Fantasia'),
(6, 'Ficção Científica'),
(7, 'Infantil'),
(8, 'Clássicos'),
(9, 'Shonen'),
(10, 'Seinen');

-- usuarios: quem entra no painel (a senha é gravada como hash)
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `perfil` ENUM('Administrador','Operador') NOT NULL DEFAULT 'Operador',
  `ativo` ENUM('Sim','Não') NOT NULL DEFAULT 'Sim',
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Senha de todos os usuários de exemplo: 123456
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`) VALUES
(1, 'Administrador', 'admin@prosaetraco.com', '$2y$12$xTn34JkSg57QfhBFGILBqOnlCUfrYigEEqweR3lPJJgURYXh9FzC6', 'Administrador', 'Sim'),
(2, 'Marina Oliveira', 'marina@prosaetraco.com', '$2y$12$xTn34JkSg57QfhBFGILBqOnlCUfrYigEEqweR3lPJJgURYXh9FzC6', 'Operador', 'Sim');

-- produtos: o catálogo de livros, HQs e mangás
CREATE TABLE `produtos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(150) NOT NULL,
  `autor` VARCHAR(150) NOT NULL,
  `editora` VARCHAR(100) NOT NULL,
  `tipo` ENUM('Livro','HQ','Mangá') NOT NULL,
  `categoria_id` INT(11) NOT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  `estoque` INT(11) NOT NULL DEFAULT 0,
  `estoque_minimo` INT(11) NOT NULL DEFAULT 5,
  `capa` VARCHAR(100) DEFAULT NULL,
  `sinopse` TEXT,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `idx_produtos_titulo` (`titulo`),
  CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produtos` (`id`, `titulo`, `autor`, `editora`, `tipo`, `categoria_id`, `preco`, `estoque`, `estoque_minimo`, `sinopse`) VALUES
(1, 'One Piece - Vol. 1', 'Eiichiro Oda', 'Panini', 'Mangá', 9, 24.90, 40, 8, 'Luffy parte em busca do tesouro One Piece para se tornar o Rei dos Piratas.'),
(2, 'Naruto - Vol. 1', 'Masashi Kishimoto', 'Panini', 'Mangá', 9, 22.90, 35, 8, 'Um jovem ninja busca reconhecimento e sonha em se tornar Hokage.'),
(3, 'Berserk - Vol. 1', 'Kentaro Miura', 'Panini', 'Mangá', 10, 34.90, 4, 6, 'Guts, um guerreiro solitário, enfrenta forças sombrias em um mundo medieval brutal.'),
(4, 'Death Note - Vol. 1', 'Tsugumi Ohba', 'Panini', 'Mangá', 4, 27.90, 22, 6, 'Light Yagami encontra um caderno sobrenatural capaz de matar quem tiver o nome escrito nele.'),
(5, 'Chainsaw Man - Vol. 1', 'Tatsuki Fujimoto', 'Panini', 'Mangá', 10, 29.90, 25, 6, 'Denji troca seu coração por uma motosserra e se torna um caçador de demônios.'),
(6, 'Turma da Mônica - Laços', 'Vitor Cafaggi', 'Panini', 'HQ', 7, 39.90, 20, 5, 'Versão jovem e emocionante dos personagens clássicos de Maurício de Sousa.'),
(7, 'Watchmen', 'Alan Moore', 'Panini', 'HQ', 6, 89.90, 3, 5, 'Um grupo de heróis aposentados investiga uma conspiração que ameaça o mundo.'),
(8, 'Batman: A Piada Mortal', 'Alan Moore', 'Panini', 'HQ', 1, 49.90, 15, 5, 'O Coringa tenta provar que qualquer pessoa pode enlouquecer após um dia ruim.'),
(9, 'Homem-Aranha: De Volta ao Lar', 'Vários Autores', 'Panini', 'HQ', 1, 44.90, 17, 5, 'Peter Parker equilibra a vida escolar com sua jornada como o novo Homem-Aranha.'),
(10, 'Turma da Mata - A Origem', 'Maurício de Sousa', 'MSP', 'HQ', 7, 34.90, 10, 5, 'A origem dos personagens da Turma da Mata em uma aventura ecológica.'),
(11, 'Harry Potter e a Pedra Filosofal', 'J.K. Rowling', 'Rocco', 'Livro', 5, 54.90, 30, 6, 'Um garoto descobre que é um bruxo e ingressa na Escola de Hogwarts.'),
(12, 'O Senhor dos Anéis: A Sociedade do Anel', 'J.R.R. Tolkien', 'HarperCollins', 'Livro', 5, 64.90, 20, 6, 'Frodo Bolseiro precisa destruir um anel amaldiçoado para salvar a Terra-média.'),
(13, 'It: A Coisa', 'Stephen King', 'Suma', 'Livro', 4, 59.90, 14, 5, 'Um grupo de amigos enfrenta uma entidade maligna que assombra a cidade de Derry.'),
(14, 'Dom Casmurro', 'Machado de Assis', 'Companhia das Letras', 'Livro', 8, 29.90, 25, 5, 'Bentinho narra sua obsessão e as dúvidas sobre a fidelidade de Capitu.'),
(15, 'Duna', 'Frank Herbert', 'Aleph', 'Livro', 6, 69.90, 16, 5, 'Paul Atreides se torna o líder de uma revolução no planeta desértico Arrakis.'),
(16, 'O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 'Agir', 'Livro', 7, 24.90, 40, 8, 'Um piloto perdido no deserto encontra um pequeno príncipe vindo de outro planeta.'),
(17, 'Vagabond - Vol. 1', 'Takehiko Inoue', 'Panini', 'Mangá', 8, 32.90, 2, 6, 'A jornada de Musashi Miyamoto rumo a se tornar o maior espadachim do Japão.'),
(18, 'A Revolução dos Bichos', 'George Orwell', 'Companhia das Letras', 'Livro', 8, 34.90, 22, 5, 'Animais de uma fazenda se rebelam contra os humanos em busca de igualdade.');

-- vendas: a "capa" da venda (cliente, data, total e status)
-- o usuario_id guarda quem registrou a venda no painel
CREATE TABLE `vendas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cliente_nome` VARCHAR(150) NOT NULL,
  `usuario_id` INT(11) DEFAULT NULL,
  `data_venda` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `status` ENUM('Concluída','Cancelada') NOT NULL DEFAULT 'Concluída',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_vendas_data` (`data_venda`),
  CONSTRAINT `fk_vendas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- O campo total é redundante de propósito (guarda o valor histórico da
-- venda). Os valores abaixo conferem exatamente com a soma dos itens.
INSERT INTO `vendas` (`id`, `cliente_nome`, `usuario_id`, `data_venda`, `total`, `status`) VALUES
(1,  'Carlos Andrade',     1, '2026-04-05 14:20:00', 74.70,  'Concluída'),
(2,  'Fernanda Lima',      1, '2026-04-18 10:05:00', 114.80, 'Concluída'),
(3,  'Rafael Souza',       2, '2026-05-02 16:40:00', 89.90,  'Concluída'),
(4,  'Juliana Costa',      1, '2026-05-14 11:15:00', 139.70, 'Concluída'),
(5,  'Marcos Vinícius',    2, '2026-05-25 09:30:00', 52.80,  'Concluída'),
(6,  'Beatriz Fernandes',  1, '2026-06-03 15:50:00', 94.70,  'Concluída'),
(7,  'Thiago Martins',     2, '2026-06-10 13:10:00', 124.70, 'Concluída'),
(8,  'Camila Ribeiro',     1, '2026-06-15 17:25:00', 59.80,  'Concluída'),
(9,  'Eduardo Nunes',      2, '2026-07-20 12:00:00', 73.70,  'Concluída'),
(10, 'Larissa Pires',      1, '2026-07-22 18:45:00', 32.90,  'Cancelada'),
(11, 'Gustavo Almeida',    1, '2026-07-28 10:35:00', 159.40, 'Concluída'),
(12, 'Patrícia Gomes',     2, '2026-08-04 14:05:00', 104.80, 'Concluída'),
(13, 'Renato Barbosa',     1, '2026-08-12 16:20:00', 84.80,  'Concluída'),
(14, 'Aline Duarte',       2, '2026-08-19 11:40:00', 127.70, 'Concluída'),
(15, 'Vinícius Rocha',     1, '2026-09-01 09:55:00', 77.70,  'Concluída');

-- venda_itens: os produtos de cada venda (uma linha por produto)
CREATE TABLE `venda_itens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `venda_id` INT(11) NOT NULL,
  `produto_id` INT(11) NOT NULL,
  `quantidade` INT(11) NOT NULL,
  `valor_unitario` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venda_id` (`venda_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `fk_itens_venda` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_itens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `venda_itens` (`venda_id`, `produto_id`, `quantidade`, `valor_unitario`) VALUES
(1, 1, 2, 24.90),  (1, 16, 1, 24.90),
(2, 12, 1, 64.90), (2, 8, 1, 49.90),
(3, 7, 1, 89.90),
(4, 11, 1, 54.90), (4, 13, 1, 59.90), (4, 16, 1, 24.90),
(5, 4, 1, 27.90),  (5, 16, 1, 24.90),
(6, 3, 1, 34.90),  (6, 5, 2, 29.90),
(7, 9, 1, 44.90),  (7, 6, 2, 39.90),
(8, 14, 2, 29.90),
(9, 2, 2, 22.90),  (9, 4, 1, 27.90),
(10, 17, 1, 32.90),
(11, 1, 4, 24.90), (11, 5, 1, 29.90), (11, 14, 1, 29.90),
(12, 15, 1, 69.90),(12, 18, 1, 34.90),
(13, 8, 1, 49.90), (13, 10, 1, 34.90),
(14, 12, 1, 64.90),(14, 6, 1, 39.90), (14, 2, 1, 22.90),
(15, 1, 2, 24.90), (15, 4, 1, 27.90);

-- ===== 2. FUNÇÕES =====
-- guardam contas que se repetem no sistema, para não reescrever o
-- mesmo pedaço de SQL em várias consultas

DROP FUNCTION IF EXISTS `fn_subtotal_item`;
DROP FUNCTION IF EXISTS `fn_faturamento_produto`;
DROP FUNCTION IF EXISTS `fn_situacao_estoque`;

DELIMITER $$

-- Calcula o subtotal de um item (quantidade x valor unitário)
-- sempre em valores positivos. Usada pelas views e procedures.
CREATE FUNCTION `fn_subtotal_item`(
    p_quantidade INT,
    p_valor_unitario DECIMAL(10,2)
)
RETURNS DECIMAL(12,2)
DETERMINISTIC
BEGIN
    RETURN ABS(IFNULL(p_quantidade, 0)) * ABS(IFNULL(p_valor_unitario, 0));
END$$

-- Retorna o faturamento acumulado de um produto específico.
-- Substitui um bloco de JOIN + SUM que seria repetido em várias telas.
CREATE FUNCTION `fn_faturamento_produto`(p_produto_id INT)
RETURNS DECIMAL(12,2)
READS SQL DATA
BEGIN
    DECLARE v_faturamento DECIMAL(12,2);

    SELECT IFNULL(SUM(fn_subtotal_item(vi.quantidade, vi.valor_unitario)), 0)
      INTO v_faturamento
      FROM venda_itens vi
      INNER JOIN vendas v ON v.id = vi.venda_id
     WHERE vi.produto_id = p_produto_id
       AND v.status = 'Concluída';

    RETURN v_faturamento;
END$$

-- Classifica a situação do estoque de um produto em uma única palavra.
-- Reaproveitada na listagem do painel, na loja e na dashboard.
CREATE FUNCTION `fn_situacao_estoque`(
    p_estoque INT,
    p_estoque_minimo INT
)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    IF IFNULL(p_estoque, 0) <= 0 THEN
        RETURN 'Esgotado';
    ELSEIF IFNULL(p_estoque, 0) <= IFNULL(p_estoque_minimo, 5) THEN
        RETURN 'Crítico';
    ELSE
        RETURN 'Normal';
    END IF;
END$$

DELIMITER ;

-- ===== 3. TRIGGERS =====
-- rodam sozinhos quando alguém grava na tabela produtos

DROP TRIGGER IF EXISTS `trg_produtos_valores_positivos`;
DROP TRIGGER IF EXISTS `trg_produtos_valores_positivos_insert`;

DELIMITER $$

-- Exigência da rubrica: BEFORE UPDATE padronizando valores positivos.
CREATE TRIGGER `trg_produtos_valores_positivos`
BEFORE UPDATE ON `produtos`
FOR EACH ROW
BEGIN
    IF NEW.preco < 0 THEN
        SET NEW.preco = ABS(NEW.preco);
    END IF;

    IF NEW.estoque < 0 THEN
        SET NEW.estoque = ABS(NEW.estoque);
    END IF;

    IF NEW.estoque_minimo < 0 THEN
        SET NEW.estoque_minimo = ABS(NEW.estoque_minimo);
    END IF;
END$$

-- Mesma regra na inclusão, para o dado nunca nascer negativo.
CREATE TRIGGER `trg_produtos_valores_positivos_insert`
BEFORE INSERT ON `produtos`
FOR EACH ROW
BEGIN
    IF NEW.preco < 0 THEN
        SET NEW.preco = ABS(NEW.preco);
    END IF;

    IF NEW.estoque < 0 THEN
        SET NEW.estoque = ABS(NEW.estoque);
    END IF;

    IF NEW.estoque_minimo < 0 THEN
        SET NEW.estoque_minimo = ABS(NEW.estoque_minimo);
    END IF;
END$$

DELIMITER ;

-- ===== 4. VIEWS ANALÍTICAS (com CTE) =====
-- limpam e agrupam os dados brutos de vendas, já prontos para a API
-- e para as telas

-- Faturamento consolidado por mês
DROP VIEW IF EXISTS `vw_faturamento_mensal`;
CREATE VIEW `vw_faturamento_mensal` AS
WITH `itens_validos` AS (
    SELECT
        v.id AS venda_id,
        v.data_venda,
        vi.quantidade,
        fn_subtotal_item(vi.quantidade, vi.valor_unitario) AS subtotal
    FROM vendas v
    INNER JOIN venda_itens vi ON vi.venda_id = v.id
    WHERE v.status = 'Concluída'
)
SELECT
    DATE_FORMAT(data_venda, '%Y-%m')      AS mes_referencia,
    COUNT(DISTINCT venda_id)              AS total_vendas,
    SUM(quantidade)                       AS total_itens_vendidos,
    SUM(subtotal)                         AS faturamento_total,
    ROUND(SUM(subtotal) / COUNT(DISTINCT venda_id), 2) AS ticket_medio
FROM `itens_validos`
GROUP BY DATE_FORMAT(data_venda, '%Y-%m')
ORDER BY mes_referencia;

-- Faturamento consolidado por tipo de produto e categoria
DROP VIEW IF EXISTS `vw_faturamento_por_tipo`;
CREATE VIEW `vw_faturamento_por_tipo` AS
WITH `itens_validos` AS (
    SELECT
        vi.produto_id,
        vi.quantidade,
        fn_subtotal_item(vi.quantidade, vi.valor_unitario) AS subtotal
    FROM vendas v
    INNER JOIN venda_itens vi ON vi.venda_id = v.id
    WHERE v.status = 'Concluída'
)
SELECT
    p.tipo,
    c.nome                  AS categoria,
    SUM(iv.quantidade)      AS unidades_vendidas,
    SUM(iv.subtotal)        AS faturamento_total
FROM `itens_validos` iv
INNER JOIN produtos p   ON p.id = iv.produto_id
INNER JOIN categorias c ON c.id = p.categoria_id
GROUP BY p.tipo, c.nome
ORDER BY faturamento_total DESC;

-- Ranking de produtos: usa duas CTEs em sequência para primeiro
-- consolidar e depois classificar os campeões de venda.
DROP VIEW IF EXISTS `vw_ranking_produtos`;
CREATE VIEW `vw_ranking_produtos` AS
WITH `itens_validos` AS (
    SELECT
        vi.produto_id,
        vi.quantidade,
        fn_subtotal_item(vi.quantidade, vi.valor_unitario) AS subtotal
    FROM vendas v
    INNER JOIN venda_itens vi ON vi.venda_id = v.id
    WHERE v.status = 'Concluída'
),
`consolidado` AS (
    SELECT
        produto_id,
        SUM(quantidade) AS unidades_vendidas,
        SUM(subtotal)   AS faturamento_total
    FROM `itens_validos`
    GROUP BY produto_id
)
SELECT
    p.id                    AS produto_id,
    p.titulo,
    p.tipo,
    c.nome                  AS categoria,
    cs.unidades_vendidas,
    cs.faturamento_total
FROM `consolidado` cs
INNER JOIN produtos p   ON p.id = cs.produto_id
INNER JOIN categorias c ON c.id = p.categoria_id
ORDER BY cs.unidades_vendidas DESC, cs.faturamento_total DESC;

-- ===== 5. VIEW CENTRALIZADORA =====
-- junta em um lugar só as informações que estão espalhadas em 5
-- tabelas: vendas + venda_itens + produtos + categorias + usuarios
-- é a fonte usada pelas procedures e pela API
DROP VIEW IF EXISTS `vw_painel_geral`;
CREATE VIEW `vw_painel_geral` AS
SELECT
    v.id                    AS venda_id,
    v.data_venda,
    v.status                AS status_venda,
    v.cliente_nome,
    u.nome                  AS vendedor,
    p.id                    AS produto_id,
    p.titulo,
    p.autor,
    p.tipo,
    p.estoque,
    p.estoque_minimo,
    fn_situacao_estoque(p.estoque, p.estoque_minimo) AS situacao_estoque,
    c.nome                  AS categoria,
    vi.quantidade,
    vi.valor_unitario,
    fn_subtotal_item(vi.quantidade, vi.valor_unitario) AS subtotal
FROM vendas v
INNER JOIN venda_itens vi ON vi.venda_id = v.id
INNER JOIN produtos p     ON p.id = vi.produto_id
INNER JOIN categorias c   ON c.id = p.categoria_id
LEFT  JOIN usuarios u     ON u.id = v.usuario_id;

-- ===== 6. STORED PROCEDURES =====
-- fazem a busca, o filtro e a paginação dentro do banco, para o PHP
-- só precisar de uma chamada CALL

DROP PROCEDURE IF EXISTS `sp_dashboard_itens`;
DROP PROCEDURE IF EXISTS `sp_dashboard_totais`;
DROP PROCEDURE IF EXISTS `sp_listar_produtos`;

DELIMITER $$

-- Entrega o ARRAY BRUTO de itens vendidos (uma linha por item),
-- já com busca, filtro de tipo, filtro de período e paginação.
-- Nenhuma soma é feita aqui: os cálculos ficam no TypeScript.
CREATE PROCEDURE `sp_dashboard_itens`(
    IN p_busca        VARCHAR(150),
    IN p_tipo         VARCHAR(20),
    IN p_data_inicio  DATE,
    IN p_data_fim     DATE,
    IN p_limite       INT,
    IN p_offset       INT
)
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        SET p_limite = 500;
    END IF;

    IF p_offset IS NULL OR p_offset < 0 THEN
        SET p_offset = 0;
    END IF;

    SELECT
        venda_id,
        DATE_FORMAT(data_venda, '%Y-%m-%d') AS data_venda,
        cliente_nome,
        vendedor,
        produto_id,
        titulo,
        tipo,
        categoria,
        quantidade,
        valor_unitario
    FROM vw_painel_geral
    WHERE status_venda = 'Concluída'
      AND (p_busca IS NULL OR p_busca = '' OR titulo LIKE CONCAT('%', p_busca, '%') OR cliente_nome LIKE CONCAT('%', p_busca, '%'))
      AND (p_tipo IS NULL OR p_tipo = '' OR tipo = p_tipo)
      AND (p_data_inicio IS NULL OR DATE(data_venda) >= p_data_inicio)
      AND (p_data_fim IS NULL OR DATE(data_venda) <= p_data_fim)
    ORDER BY data_venda DESC, venda_id DESC
    LIMIT p_limite OFFSET p_offset;
END$$

-- Devolve, por parâmetros OUT, quantos registros existem para o
-- filtro aplicado. É o que permite montar a paginação na tela.
CREATE PROCEDURE `sp_dashboard_totais`(
    IN  p_busca        VARCHAR(150),
    IN  p_tipo         VARCHAR(20),
    IN  p_data_inicio  DATE,
    IN  p_data_fim     DATE,
    OUT p_total_itens  INT,
    OUT p_total_vendas INT
)
BEGIN
    SELECT COUNT(*), COUNT(DISTINCT venda_id)
      INTO p_total_itens, p_total_vendas
      FROM vw_painel_geral
     WHERE status_venda = 'Concluída'
       AND (p_busca IS NULL OR p_busca = '' OR titulo LIKE CONCAT('%', p_busca, '%') OR cliente_nome LIKE CONCAT('%', p_busca, '%'))
       AND (p_tipo IS NULL OR p_tipo = '' OR tipo = p_tipo)
       AND (p_data_inicio IS NULL OR DATE(data_venda) >= p_data_inicio)
       AND (p_data_fim IS NULL OR DATE(data_venda) <= p_data_fim);
END$$

-- Busca + filtros + paginação da listagem de produtos do painel,
-- reaproveitando a função de faturamento por produto.
CREATE PROCEDURE `sp_listar_produtos`(
    IN p_busca        VARCHAR(150),
    IN p_tipo         VARCHAR(20),
    IN p_categoria_id INT,
    IN p_limite       INT,
    IN p_offset       INT
)
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        SET p_limite = 10;
    END IF;

    IF p_offset IS NULL OR p_offset < 0 THEN
        SET p_offset = 0;
    END IF;

    SELECT
        p.id,
        p.titulo,
        p.autor,
        p.editora,
        p.tipo,
        p.preco,
        p.estoque,
        p.estoque_minimo,
        c.nome AS categoria,
        fn_situacao_estoque(p.estoque, p.estoque_minimo) AS situacao_estoque,
        fn_faturamento_produto(p.id) AS faturamento
    FROM produtos p
    INNER JOIN categorias c ON c.id = p.categoria_id
    WHERE (p_busca IS NULL OR p_busca = '' OR p.titulo LIKE CONCAT('%', p_busca, '%') OR p.autor LIKE CONCAT('%', p_busca, '%'))
      AND (p_tipo IS NULL OR p_tipo = '' OR p.tipo = p_tipo)
      AND (p_categoria_id IS NULL OR p_categoria_id = 0 OR p.categoria_id = p_categoria_id)
    ORDER BY p.titulo
    LIMIT p_limite OFFSET p_offset;
END$$

DELIMITER ;
