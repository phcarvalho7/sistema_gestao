-- ============================================================
-- Livraria Nerd - Sistema de Gestão de Vendas
-- Banco de Dados: livraria_nerd
-- SGBD: MariaDB (compatível com o ambiente XAMPP)
-- ============================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `livraria_nerd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `livraria_nerd`;

-- ------------------------------------------------------------
-- Tabela: categorias
-- Gêneros que podem ser usados por Livros, HQs e Mangás
-- ------------------------------------------------------------
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

-- ------------------------------------------------------------
-- Tabela: usuarios
-- Login do painel administrativo
-- ------------------------------------------------------------
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `ativo` ENUM('Sim','Não') NOT NULL DEFAULT 'Sim',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Senha: 123456
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ativo`) VALUES
(1, 'Administrador', 'admin@livrarianerd.com', '$2y$12$xTn34JkSg57QfhBFGILBqOnlCUfrYigEEqweR3lPJJgURYXh9FzC6', 'Sim');

-- ------------------------------------------------------------
-- Tabela: produtos
-- Catálogo de Livros, HQs e Mangás
-- ------------------------------------------------------------
CREATE TABLE `produtos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(150) NOT NULL,
  `autor` VARCHAR(150) NOT NULL,
  `editora` VARCHAR(100) NOT NULL,
  `tipo` ENUM('Livro','HQ','Mangá') NOT NULL,
  `categoria_id` INT(11) NOT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  `estoque` INT(11) NOT NULL DEFAULT 0,
  `capa` VARCHAR(100) DEFAULT NULL,
  `sinopse` TEXT,
  `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produtos` (`id`, `titulo`, `autor`, `editora`, `tipo`, `categoria_id`, `preco`, `estoque`, `sinopse`) VALUES
(1, 'One Piece - Vol. 1', 'Eiichiro Oda', 'Panini', 'Mangá', 9, 24.90, 40, 'Luffy parte em busca do tesouro One Piece para se tornar o Rei dos Piratas.'),
(2, 'Naruto - Vol. 1', 'Masashi Kishimoto', 'Panini', 'Mangá', 9, 22.90, 35, 'Um jovem ninja busca reconhecimento e sonha em se tornar Hokage.'),
(3, 'Berserk - Vol. 1', 'Kentaro Miura', 'Panini', 'Mangá', 10, 34.90, 18, 'Guts, um guerreiro solitário, enfrenta forças sombrias em um mundo medieval brutal.'),
(4, 'Death Note - Vol. 1', 'Tsugumi Ohba', 'Panini', 'Mangá', 4, 27.90, 22, 'Light Yagami encontra um caderno sobrenatural capaz de matar quem tiver o nome escrito nele.'),
(5, 'Chainsaw Man - Vol. 1', 'Tatsuki Fujimoto', 'Panini', 'Mangá', 10, 29.90, 25, 'Denji troca seu coração por uma motosserra e se torna um caçador de demônios.'),
(6, 'Turma da Mônica - Laços', 'Vitor Cafaggi', 'Panini', 'HQ', 7, 39.90, 20, 'Versão jovem e emocionante dos personagens clássicos de Maurício de Sousa.'),
(7, 'Watchmen', 'Alan Moore', 'Panini', 'HQ', 6, 89.90, 12, 'Um grupo de heróis aposentados investiga uma conspiração que ameaça o mundo.'),
(8, 'Batman: A Piada Mortal', 'Alan Moore', 'Panini', 'HQ', 1, 49.90, 15, 'O Coringa tenta provar que qualquer pessoa pode enlouquecer após um dia ruim.'),
(9, 'Homem-Aranha: De Volta ao Lar', 'Vários Autores', 'Panini', 'HQ', 1, 44.90, 17, 'Peter Parker equilibra a vida escolar com sua jornada como o novo Homem-Aranha.'),
(10, 'Turma da Mata - A Origem', 'Maurício de Sousa', 'MSP', 'HQ', 7, 34.90, 10, 'A origem dos personagens da Turma da Mata em uma aventura ecológica.'),
(11, 'Harry Potter e a Pedra Filosofal', 'J.K. Rowling', 'Rocco', 'Livro', 5, 54.90, 30, 'Um garoto descobre que é um bruxo e ingressa na Escola de Hogwarts.'),
(12, 'O Senhor dos Anéis: A Sociedade do Anel', 'J.R.R. Tolkien', 'HarperCollins', 'Livro', 5, 64.90, 20, 'Frodo Bolseiro precisa destruir um anel amaldiçoado para salvar a Terra-média.'),
(13, 'It: A Coisa', 'Stephen King', 'Suma', 'Livro', 4, 59.90, 14, 'Um grupo de amigos enfrenta uma entidade maligna que assombra a cidade de Derry.'),
(14, 'Dom Casmurro', 'Machado de Assis', 'Companhia das Letras', 'Livro', 8, 29.90, 25, 'Bentinho narra sua obsessão e as dúvidas sobre a fidelidade de Capitu.'),
(15, 'Duna', 'Frank Herbert', 'Aleph', 'Livro', 6, 69.90, 16, 'Paul Atreides se torna o líder de uma revolução no planeta desértico Arrakis.'),
(16, 'O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 'Agir', 'Livro', 7, 24.90, 40, 'Um piloto perdido no deserto encontra um pequeno príncipe vindo de outro planeta.'),
(17, 'Vagabond - Vol. 1', 'Takehiko Inoue', 'Panini', 'Mangá', 8, 32.90, 12, 'A jornada de Musashi Miyamoto rumo a se tornar o maior espadachim do Japão.'),
(18, 'A Revolução dos Bichos', 'George Orwell', 'Companhia das Letras', 'Livro', 8, 34.90, 22, 'Animais de uma fazenda se rebelam contra os humanos em busca de igualdade.');

-- ------------------------------------------------------------
-- Tabela: vendas
-- ------------------------------------------------------------
CREATE TABLE `vendas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cliente_nome` VARCHAR(150) NOT NULL,
  `data_venda` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `status` ENUM('Concluída','Cancelada') NOT NULL DEFAULT 'Concluída',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendas` (`id`, `cliente_nome`, `data_venda`, `total`, `status`) VALUES
(1, 'Carlos Andrade', '2026-06-05 14:20:00', 74.80, 'Concluída'),
(2, 'Fernanda Lima', '2026-06-18 10:05:00', 119.80, 'Concluída'),
(3, 'Rafael Souza', '2026-07-02 16:40:00', 89.90, 'Concluída'),
(4, 'Juliana Costa', '2026-07-14 11:15:00', 149.70, 'Concluída'),
(5, 'Marcos Vinícius', '2026-07-25 09:30:00', 47.80, 'Concluída'),
(6, 'Beatriz Fernandes', '2026-08-03 15:50:00', 94.80, 'Concluída'),
(7, 'Thiago Martins', '2026-08-10 13:10:00', 124.70, 'Concluída'),
(8, 'Camila Ribeiro', '2026-08-15 17:25:00', 59.90, 'Concluída'),
(9, 'Eduardo Nunes', '2026-08-20 12:00:00', 79.80, 'Concluída'),
(10, 'Larissa Pires', '2026-08-22 18:45:00', 32.90, 'Cancelada');

-- ------------------------------------------------------------
-- Tabela: venda_itens
-- ------------------------------------------------------------
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
(1, 1, 2, 24.90), (1, 16, 1, 24.90),
(2, 12, 1, 64.90), (2, 8, 1, 49.90),
(3, 7, 1, 89.90),
(4, 11, 1, 54.90), (4, 13, 1, 59.90), (4, 16, 1, 24.90),
(5, 4, 1, 27.90), (5, 16, 1, 24.90) ,
(6, 3, 1, 34.90), (6, 5, 2, 29.90),
(7, 9, 1, 44.90), (7, 6, 2, 39.90),
(8, 14, 2, 29.90),
(9, 2, 2, 22.90), (9, 4, 1, 27.90) ,
(10, 17, 1, 32.90);

-- ------------------------------------------------------------
-- TRIGGER
-- Padroniza a inserção de valores positivos em preço e estoque
-- sempre que um produto for atualizado (BEFORE UPDATE).
-- ------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_produtos_valores_positivos`;

DELIMITER $$
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
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- VIEWS ANALÍTICAS (com CTE / WITH)
-- Consolidam os dados brutos de vendas em informações
-- prontas para a dashboard, evitando cálculos repetidos.
-- ------------------------------------------------------------

-- Faturamento consolidado por mês
DROP VIEW IF EXISTS `vw_faturamento_mensal`;
CREATE VIEW `vw_faturamento_mensal` AS
WITH `itens_validos` AS (
    SELECT
        v.id AS venda_id,
        v.data_venda,
        vi.quantidade,
        vi.valor_unitario,
        (vi.quantidade * vi.valor_unitario) AS subtotal
    FROM vendas v
    INNER JOIN venda_itens vi ON vi.venda_id = v.id
    WHERE v.status = 'Concluída'
)
SELECT
    DATE_FORMAT(data_venda, '%Y-%m') AS mes_referencia,
    COUNT(DISTINCT venda_id) AS total_vendas,
    SUM(quantidade) AS total_itens_vendidos,
    SUM(subtotal) AS faturamento_total
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
        vi.valor_unitario,
        (vi.quantidade * vi.valor_unitario) AS subtotal
    FROM vendas v
    INNER JOIN venda_itens vi ON vi.venda_id = v.id
    WHERE v.status = 'Concluída'
)
SELECT
    p.tipo,
    c.nome AS categoria,
    SUM(iv.quantidade) AS unidades_vendidas,
    SUM(iv.subtotal) AS faturamento_total
FROM `itens_validos` iv
INNER JOIN produtos p ON p.id = iv.produto_id
INNER JOIN categorias c ON c.id = p.categoria_id
GROUP BY p.tipo, c.nome
ORDER BY faturamento_total DESC;
