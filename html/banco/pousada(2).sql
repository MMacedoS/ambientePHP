-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 13/03/2023 às 19:28
-- Versão do servidor: 8.0.31
-- Versão do PHP: 8.0.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pousada`
--
CREATE DATABASE IF NOT EXISTS `pousada` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `pousada`;

DELIMITER $$
--
-- Procedimentos
--
DROP PROCEDURE IF EXISTS `atualizaEstoque`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `atualizaEstoque` (IN `quantidade` DECIMAL(7,2), IN `produto` INT)   BEGIN
    declare contador int(11);
 
    SELECT count(*) into contador FROM estoque WHERE produto_id = produto;
 
    IF contador > 0 THEN
        UPDATE estoque SET saldoAtual= saldoAtual + quantidade, saldoAnterior = saldoAtual + (quantidade * -1)
        WHERE produto_id = produto;
    ELSE
        INSERT INTO estoque (produto_id, saldoAtual, saldoAnterior) values (produto, quantidade, 0);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `atualizaValorProduto`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `atualizaValorProduto` (IN `valor` DECIMAL(7,2), IN `produto_id` INT)   BEGIN

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `apartamento`
--

DROP TABLE IF EXISTS `apartamento`;
CREATE TABLE `apartamento` (
  `id` int NOT NULL,
  `numero` int NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `status` int NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `apartamento`
--

INSERT INTO `apartamento` (`id`, `numero`, `descricao`, `tipo`, `status`, `created_at`) VALUES
(1, 102, 'ste', 'Standart', 1, NULL),
(2, 101, 'sdasd', 'Standart', 2, NULL),
(3, 103, 'aa', 'Standart', 2, NULL),
(4, 104, 'sdasd', 'Standart', 2, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracao`
--

DROP TABLE IF EXISTS `configuracao`;
CREATE TABLE `configuracao` (
  `id` int NOT NULL,
  `parametro` varchar(45) DEFAULT NULL,
  `valor` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `configuracao`
--

INSERT INTO `configuracao` (`id`, `parametro`, `valor`) VALUES
(1, 'gerar_diaria', '2023-03-14 17:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `consumo`
--

DROP TABLE IF EXISTS `consumo`;
CREATE TABLE `consumo` (
  `id` int NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `valorUnitario` decimal(7,2) NOT NULL,
  `quantidade` decimal(7,2) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `produto_id` int NOT NULL,
  `reserva_id` int NOT NULL,
  `funcionario` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `consumo`
--

INSERT INTO `consumo` (`id`, `descricao`, `valorUnitario`, `quantidade`, `status`, `created_at`, `updated_at`, `produto_id`, `reserva_id`, `funcionario`) VALUES
(71, 'diaria', '250.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 3, 123),
(72, 'diaria', '520.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 6, 123),
(73, 'diaria', '500.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 7, 123),
(74, 'diaria', '250.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 3, 123),
(75, 'diaria', '520.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 6, 123),
(76, 'diaria', '500.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 7, 123),
(77, 'diaria', '250.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 3, 123),
(78, 'diaria', '520.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 6, 123),
(79, 'diaria', '500.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 7, 123),
(80, 'diaria', '250.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 3, 123),
(81, 'diaria', '520.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 6, 123),
(82, 'diaria', '500.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 7, 123),
(83, 'diaria', '250.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 3, 123),
(84, 'diaria', '520.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 6, 123),
(85, 'diaria', '500.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 7, 123),
(86, 'diaria', '250.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 3, 123),
(87, 'diaria', '520.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 6, 123),
(88, 'diaria', '500.00', '1.00', 1, '2023-03-12 02:16:09', '2023-03-12 02:16:09', 1, 7, 123),
(89, 'diaria', '250.00', '1.00', 1, '2023-03-12 03:04:10', '2023-03-12 03:04:10', 1, 3, 123),
(90, 'diaria', '520.00', '1.00', 1, '2023-03-12 03:04:10', '2023-03-12 03:04:10', 1, 6, 123),
(91, 'diaria', '500.00', '1.00', 1, '2023-03-12 03:04:10', '2023-03-12 03:04:10', 1, 7, 123),
(92, 'diaria', '250.00', '1.00', 1, '2023-03-12 16:01:44', '2023-03-12 16:01:44', 1, 9, 123),
(93, 'diaria', '300.00', '1.00', 1, '2023-03-12 17:14:10', '2023-03-12 17:14:10', 1, 10, 123),
(94, 'Refrigerante', '5.00', '1.00', 1, '2023-03-12 21:16:26', '2023-03-12 21:16:26', 3, 6, 123),
(96, 'Cerveja Heiniken', '12.00', '5.00', 1, '2023-03-13 00:31:07', '2023-03-13 00:31:07', 4, 6, 123),
(97, 'diaria', '520.00', '1.00', 1, '2023-03-13 03:01:34', '2023-03-13 03:01:34', 1, 6, 123),
(98, 'diaria', '250.00', '1.00', 1, '2023-03-13 03:01:34', '2023-03-13 03:01:34', 1, 9, 123),
(99, 'diaria', '300.00', '1.00', 1, '2023-03-13 03:01:34', '2023-03-13 03:01:34', 1, 10, 123);

--
-- Acionadores `consumo`
--
DROP TRIGGER IF EXISTS `consumo_AFTER_DELETE`;
DELIMITER $$
CREATE TRIGGER `consumo_AFTER_DELETE` AFTER DELETE ON `consumo` FOR EACH ROW BEGIN
IF(old.descricao != 'diaria' && old.descricao != 'pacote') THEN
	call atualizaEstoque(old.quantidade,old.produto_id);
END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `consumo_AFTER_INSERT`;
DELIMITER $$
CREATE TRIGGER `consumo_AFTER_INSERT` AFTER INSERT ON `consumo` FOR EACH ROW BEGIN
IF(new.descricao != 'diaria' && new.descricao != 'pacote') THEN
    call atualizaEstoque(new.quantidade * -1, new.produto_id);
END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `consumo_AFTER_update`;
DELIMITER $$
CREATE TRIGGER `consumo_AFTER_update` AFTER UPDATE ON `consumo` FOR EACH ROW BEGIN

IF(new.descricao != 'diaria' && new.descricao != 'pacote') THEN
call atualizaEstoque(old.quantidade - new.quantidade, new.produto_id);

END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa`
--

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE `empresa` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(45) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa_has_hospede`
--

DROP TABLE IF EXISTS `empresa_has_hospede`;
CREATE TABLE `empresa_has_hospede` (
  `Empresa_id` int NOT NULL,
  `hospede_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada`
--

DROP TABLE IF EXISTS `entrada`;
CREATE TABLE `entrada` (
  `id` int NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `funcionario` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pagamento_id` int DEFAULT NULL,
  `venda_id` int DEFAULT NULL,
  `tipoPagamento` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `entrada`
--

INSERT INTO `entrada` (`id`, `descricao`, `valor`, `funcionario`, `created_at`, `updated_at`, `pagamento_id`, `venda_id`, `tipoPagamento`) VALUES
(58, '11', '1.00', 123, '2023-03-12 00:39:18', '2023-03-13 03:39:22', 59, NULL, '4'),
(59, 'Pagamento referente a reserva 6', '228.00', 123, '2023-03-12 00:00:00', '2023-03-13 03:39:27', 60, NULL, '2'),
(63, 'Pagamento referente a reserva 3', '1442.00', 123, '2023-03-12 00:39:35', '2023-03-13 03:39:38', 64, NULL, '2'),
(64, 'Pagamento referente a reserva 7', '3500.00', 123, '2023-03-12 00:00:00', '2023-03-13 03:39:42', 65, NULL, '2'),
(65, 'Pagamento referente a reserva 3', '308.00', 123, '2023-03-12 00:00:00', '2023-03-13 03:40:19', 66, NULL, '2'),
(69, 'Pagamento referente a reserva 6', '500.00', 123, '2023-03-12 00:00:00', '2023-03-13 03:40:22', 70, NULL, '1'),
(70, 'Pagamento referente a reserva 5', '100.00', 123, '2023-03-12 00:00:00', '2023-03-13 03:40:24', 71, NULL, '4');

--
-- Acionadores `entrada`
--
DROP TRIGGER IF EXISTS `entrada_AFTER_DELETE`;
DELIMITER $$
CREATE TRIGGER `entrada_AFTER_DELETE` BEFORE DELETE ON `entrada` FOR EACH ROW BEGIN
 delete from movimento where entrada_id = old.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `entrada_AFTER_INSERT`;
DELIMITER $$
CREATE TRIGGER `entrada_AFTER_INSERT` AFTER INSERT ON `entrada` FOR EACH ROW BEGIN
insert into movimento set descricao = new.descricao, valor = new.valor, tipo = new.tipoPagamento, entrada_id = new.id; 
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `entrada_AFTER_UPDATE`;
DELIMITER $$
CREATE TRIGGER `entrada_AFTER_UPDATE` AFTER UPDATE ON `entrada` FOR EACH ROW BEGIN
update movimento set descricao = new.descricao, valor = new.valor, 
tipo = new.tipoPagamento where  entrada_id = new.id; 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `entradaestoque`
--

DROP TABLE IF EXISTS `entradaestoque`;
CREATE TABLE `entradaestoque` (
  `id` int NOT NULL,
  `quantidade` decimal(7,2) NOT NULL,
  `fornecedor` varchar(45) NOT NULL,
  `valorCompra` decimal(7,2) NOT NULL,
  `valorVendaUnitario` decimal(7,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `produto_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `entradaestoque`
--

INSERT INTO `entradaestoque` (`id`, `quantidade`, `fornecedor`, `valorCompra`, `valorVendaUnitario`, `created_at`, `updated_at`, `produto_id`) VALUES
(7, '100.00', 'teste', '350.00', '5.00', '2023-03-12 17:13:52', '2023-03-12 17:13:52', 3),
(9, '200.00', 'hei', '500.00', NULL, '2023-03-13 00:34:56', '2023-03-13 00:34:56', 4);

--
-- Acionadores `entradaestoque`
--
DROP TRIGGER IF EXISTS `entradaEstoque_AFTER_DELETE`;
DELIMITER $$
CREATE TRIGGER `entradaEstoque_AFTER_DELETE` AFTER DELETE ON `entradaestoque` FOR EACH ROW BEGIN
call atualizaEstoque(old.quantidade * -1, old.produto_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `entradaEstoque_AFTER_INSERT`;
DELIMITER $$
CREATE TRIGGER `entradaEstoque_AFTER_INSERT` AFTER INSERT ON `entradaestoque` FOR EACH ROW BEGIN
call atualizaEstoque(new.quantidade, new.produto_id);
call atualizaValorProduto(new.valorVendaUnitario, new.produto_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `entradaEstoque_AFTER_UPDATE`;
DELIMITER $$
CREATE TRIGGER `entradaEstoque_AFTER_UPDATE` AFTER UPDATE ON `entradaestoque` FOR EACH ROW BEGIN
call atualizaEstoque(new.quantidade - old.quantidade, new.produto_id);
call atualizaValorProduto(new.valorVendaUnitario);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

DROP TABLE IF EXISTS `estoque`;
CREATE TABLE `estoque` (
  `id` int NOT NULL,
  `saldoAtual` decimal(7,2) NOT NULL,
  `saldoAnterior` decimal(7,2) NOT NULL,
  `produto_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `saldoAtual`, `saldoAnterior`, `produto_id`) VALUES
(4, '99.00', '1099.00', 3),
(5, '195.00', '-5.00', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `hospede`
--

DROP TABLE IF EXISTS `hospede`;
CREATE TABLE `hospede` (
  `id` int NOT NULL,
  `nome` varchar(45) NOT NULL,
  `cpf` varchar(45) NOT NULL,
  `endereco` varchar(45) NOT NULL,
  `telefone` varchar(45) DEFAULT NULL,
  `tipo` int NOT NULL,
  `status` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `hospede`
--

INSERT INTO `hospede` (`id`, `nome`, `cpf`, `endereco`, `telefone`, `tipo`, `status`, `email`, `created_at`, `updated_at`) VALUES
(1, 'mauricio Macedo da Silva', '05987122121', 'sdsdsd4s545', '75992872929', 1, 1, 'macedo.macedo@gmail.com', NULL, '2023-03-04 19:11:36'),
(2, 'Marcos do Santos ', '54545454545', 'acssad', '5454545', 2, 1, 'macedo@gamaiç.com', '2023-03-04 19:14:31', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimento`
--

DROP TABLE IF EXISTS `movimento`;
CREATE TABLE `movimento` (
  `id` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `tipo` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int NOT NULL DEFAULT '1',
  `entrada_id` int DEFAULT NULL,
  `saida_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `movimento`
--

INSERT INTO `movimento` (`id`, `descricao`, `valor`, `tipo`, `created_at`, `updated_at`, `status`, `entrada_id`, `saida_id`) VALUES
(19, '11', '1.00', 4, '2023-03-11 22:51:58', '2023-03-11 22:51:58', 1, 58, NULL),
(20, 'Pagamento referente a reserva 6', '228.00', 2, '2023-03-11 23:02:30', '2023-03-11 23:02:30', 1, 59, NULL),
(24, 'Pagamento referente a reserva 3', '1442.00', 2, '2023-03-11 23:20:45', '2023-03-11 23:20:45', 1, 63, NULL),
(25, 'Pagamento referente a reserva 7', '3500.00', 2, '2023-03-12 14:35:04', '2023-03-12 14:35:04', 1, 64, NULL),
(26, 'Pagamento referente a reserva 3', '308.00', 2, '2023-03-12 17:07:03', '2023-03-12 17:07:03', 1, 65, NULL),
(30, 'Pagamento referente a reserva 6', '500.00', 1, '2023-03-12 18:11:15', '2023-03-12 18:11:15', 1, 69, NULL),
(31, 'Pagamento referente a reserva 5', '100.00', 4, '2023-03-12 18:13:05', '2023-03-12 18:13:05', 1, 70, NULL),
(32, 'asdasdsadsadsadsadas', '5000.00', 1, '2023-03-13 04:24:02', '2023-03-13 04:35:27', 1, NULL, 1),
(33, 'sdfsfsdafas asdsadsa', '250.00', 1, '2023-03-13 04:37:00', '2023-03-13 04:37:00', 1, NULL, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento`
--

DROP TABLE IF EXISTS `pagamento`;
CREATE TABLE `pagamento` (
  `id` int NOT NULL,
  `dataPagamento` date DEFAULT NULL,
  `valorPagamento` decimal(7,2) DEFAULT NULL,
  `statusPagamento` int DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `reserva_id` int NOT NULL,
  `tipoPagamento` int NOT NULL,
  `descricao` varchar(45) DEFAULT NULL,
  `funcionario` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `pagamento`
--

INSERT INTO `pagamento` (`id`, `dataPagamento`, `valorPagamento`, `statusPagamento`, `created_at`, `updated_at`, `reserva_id`, `tipoPagamento`, `descricao`, `funcionario`) VALUES
(59, '2023-03-11', '1.00', 1, '2023-03-11 22:51:58', '2023-03-11 22:51:58', 6, 4, '11', 123),
(60, '2023-03-11', '228.00', 1, '2023-03-11 23:02:30', '2023-03-11 23:02:30', 6, 2, 'Pagamento referente a reserva 6', 123),
(64, '2023-03-11', '1442.00', 1, '2023-03-11 23:20:45', '2023-03-11 23:20:45', 3, 2, 'Pagamento referente a reserva 3', 123),
(65, '2023-03-12', '3500.00', 1, '2023-03-12 14:35:04', '2023-03-12 14:35:04', 7, 2, 'Pagamento referente a reserva 7', 123),
(66, '2023-03-12', '308.00', 1, '2023-03-12 17:07:03', '2023-03-12 17:07:03', 3, 2, 'Pagamento referente a reserva 3', 123),
(70, '2023-03-12', '500.00', 1, '2023-03-12 18:11:15', '2023-03-12 18:11:15', 6, 1, 'Pagamento referente a reserva 6', 123),
(71, '2023-03-12', '100.00', 1, '2023-03-12 18:13:05', '2023-03-12 18:13:05', 5, 4, 'Pagamento referente a reserva 5', 123);

--
-- Acionadores `pagamento`
--
DROP TRIGGER IF EXISTS `pagamento_AFTER_DELETE`;
DELIMITER $$
CREATE TRIGGER `pagamento_AFTER_DELETE` BEFORE DELETE ON `pagamento` FOR EACH ROW BEGIN
 delete from entrada where pagamento_id = old.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `pagamento_AFTER_INSERT`;
DELIMITER $$
CREATE TRIGGER `pagamento_AFTER_INSERT` AFTER INSERT ON `pagamento` FOR EACH ROW BEGIN
insert into entrada set descricao = new.descricao, 
valor = new.valorPagamento, funcionario = new.funcionario, 
tipoPagamento = new.tipoPagamento, pagamento_id = new.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `pagamento_AFTER_UPDATE`;
DELIMITER $$
CREATE TRIGGER `pagamento_AFTER_UPDATE` AFTER UPDATE ON `pagamento` FOR EACH ROW BEGIN
  update entrada set descricao = new.descricao, 
valor = new.valorPagamento, funcionario = new.funcionario, tipoPagamento = new.tipoPagamento
where pagamento_id = new.id; 
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoes`
--

DROP TABLE IF EXISTS `permissoes`;
CREATE TABLE `permissoes` (
  `id` int NOT NULL,
  `permissao` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

DROP TABLE IF EXISTS `produto`;
CREATE TABLE `produto` (
  `id` int NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id`, `descricao`, `tipo`, `valor`, `status`, `created_at`, `updated_at`) VALUES
(1, 'diaria', 'hospedagem', '0.00', 1, NULL, NULL),
(2, 'pacote', 'hospedagem', '0.00', 1, NULL, NULL),
(3, 'Refrigerante', 'consumo', '5.00', 1, NULL, NULL),
(4, 'Cerveja Heiniken', 'consumo', '12.00', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `reserva`
--

DROP TABLE IF EXISTS `reserva`;
CREATE TABLE `reserva` (
  `id` int NOT NULL,
  `dataReserva` date NOT NULL,
  `dataEntrada` date NOT NULL,
  `dataSaida` date NOT NULL,
  `status` int NOT NULL,
  `obs` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tipo` int NOT NULL,
  `apartamento_id` int NOT NULL,
  `hospede_id` int NOT NULL,
  `funcionario` int DEFAULT NULL,
  `valor` decimal(7,2) NOT NULL,
  `gerarDiaria` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `reserva`
--

INSERT INTO `reserva` (`id`, `dataReserva`, `dataEntrada`, `dataSaida`, `status`, `obs`, `created_at`, `updated_at`, `tipo`, `apartamento_id`, `hospede_id`, `funcionario`, `valor`, `gerarDiaria`) VALUES
(3, '2023-03-06', '2023-03-06', '2023-03-12', 4, 'sasdasdsad', NULL, NULL, 1, 1, 1, 123, '250.00', '2023-03-13 17:00:00'),
(4, '2023-03-05', '2023-03-25', '2023-03-31', 1, 'qualquer coisa', NULL, NULL, 1, 3, 2, 123, '200.00', '2023-03-06 21:08:37'),
(5, '2023-03-12', '2023-04-05', '2023-04-08', 2, 'asdasdasd', NULL, NULL, 1, 1, 1, 123, '250.00', '2023-03-06 21:08:37'),
(6, '2023-03-06', '2023-03-06', '2023-03-23', 3, 'aaasss', NULL, NULL, 1, 2, 1, 123, '520.00', '2023-03-14 17:00:00'),
(7, '2023-03-05', '2023-03-06', '2023-03-08', 4, 'asas', NULL, NULL, 1, 3, 1, 123, '500.00', '2023-03-13 17:00:00'),
(9, '2023-03-12', '2023-03-12', '2023-03-15', 3, 'sdsdsd', NULL, NULL, 1, 3, 1, 123, '250.00', '2023-03-14 17:00:00'),
(10, '2023-03-12', '2023-03-12', '2023-03-15', 3, 'sdsd', NULL, NULL, 1, 4, 1, 123, '300.00', '2023-03-14 17:00:00'),
(11, '2023-03-12', '2023-04-01', '2023-04-08', 1, 'SS', NULL, NULL, 1, 2, 1, 123, '200.00', '2023-03-12 23:23:20'),
(12, '2023-03-12', '2023-04-01', '2023-04-08', 1, 'A', NULL, NULL, 1, 3, 1, 123, '200.00', '2023-03-12 23:24:32'),
(13, '2023-03-12', '2023-04-01', '2023-04-08', 1, 'A', NULL, NULL, 1, 4, 1, 123, '200.00', '2023-03-12 23:25:28'),
(14, '2023-03-12', '2023-04-08', '2023-04-15', 1, 'A', NULL, NULL, 1, 1, 1, 123, '200.00', '2023-03-12 23:26:03'),
(15, '2023-03-12', '2023-04-08', '2023-04-15', 1, 'A', NULL, NULL, 1, 2, 1, 123, '250.00', '2023-03-12 23:26:31'),
(16, '2023-03-12', '2023-04-08', '2023-04-15', 1, 'A', NULL, NULL, 1, 3, 1, 123, '250.00', '2023-03-12 23:27:00'),
(17, '2023-03-12', '2023-04-08', '2023-04-15', 1, 'A', NULL, NULL, 1, 4, 1, 123, '200.00', '2023-03-12 23:27:37'),
(18, '2023-03-12', '2023-04-15', '2023-04-22', 1, 'A', NULL, NULL, 1, 1, 1, 123, '250.00', '2023-03-12 23:28:02'),
(19, '2023-03-12', '2023-04-15', '2023-04-22', 1, 'A', NULL, NULL, 1, 2, 1, 123, '250.00', '2023-03-12 23:28:24'),
(20, '2023-03-12', '2023-04-15', '2023-04-22', 1, 'A', NULL, NULL, 1, 3, 1, 123, '300.00', '2023-03-12 23:28:45'),
(21, '2023-03-12', '2023-04-15', '2023-04-22', 1, 'A', NULL, NULL, 1, 4, 1, 123, '200.00', '2023-03-12 23:29:11'),
(22, '2023-03-12', '2023-04-22', '2023-04-29', 1, 'A', NULL, NULL, 1, 1, 1, 123, '500.00', '2023-03-12 23:30:46'),
(23, '2023-03-12', '2023-04-22', '2023-04-29', 1, 'A', NULL, NULL, 1, 2, 1, 123, '500.00', '2023-03-12 23:31:14'),
(24, '2023-03-12', '2023-04-22', '2023-04-29', 1, 'A', NULL, NULL, 1, 3, 1, 123, '210.00', '2023-03-12 23:31:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `saida`
--

DROP TABLE IF EXISTS `saida`;
CREATE TABLE `saida` (
  `id` int NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `funcionario` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tipo` varchar(45) DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `tipoPagamento` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `saida`
--

INSERT INTO `saida` (`id`, `descricao`, `valor`, `funcionario`, `created_at`, `updated_at`, `tipo`, `arquivo`, `tipoPagamento`) VALUES
(1, 'asdasdsadsadsadsadas', '5000.00', NULL, '2023-03-13 04:24:02', '2023-03-13 04:24:02', '2', NULL, '1'),
(2, 'sdfsfsdafas asdsadsa', '250.00', NULL, '2023-03-13 04:37:00', '2023-03-13 04:37:00', '1', NULL, '1');

--
-- Acionadores `saida`
--
DROP TRIGGER IF EXISTS `saida_AFTER_DELETE`;
DELIMITER $$
CREATE TRIGGER `saida_AFTER_DELETE` AFTER DELETE ON `saida` FOR EACH ROW BEGIN
 delete from movimento where saida_id = old.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `saida_AFTER_INSERT`;
DELIMITER $$
CREATE TRIGGER `saida_AFTER_INSERT` AFTER INSERT ON `saida` FOR EACH ROW BEGIN
insert into movimento set descricao = new.descricao, valor = new.valor,
 tipo = new.tipoPagamento , saida_id = new.id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `painel` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `painel`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Mauricio Macedo ', 'admin@admin.com', '202cb962ac59075b964b07152d234b70', 'Administrador', '2023-03-03 19:55:16', '2023-03-03 19:55:16', 1),
(2, 'mauricio Macedo da Silva', 'macedo@macedo.com', '2a2e9a58102784ca18e2605a4e727b5f', 'Administrador', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_has_permissoes`
--

DROP TABLE IF EXISTS `usuarios_has_permissoes`;
CREATE TABLE `usuarios_has_permissoes` (
  `usuarios_id` int NOT NULL,
  `permissoes_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda`
--

DROP TABLE IF EXISTS `venda`;
CREATE TABLE `venda` (
  `id` int NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `quantidade` decimal(7,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` int NOT NULL,
  `funcionario` int NOT NULL,
  `produto_id` int NOT NULL,
  `tipoPagamento` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Acionadores `venda`
--
DROP TRIGGER IF EXISTS `venda_AFTER_DELETE`;
DELIMITER $$
CREATE TRIGGER `venda_AFTER_DELETE` AFTER DELETE ON `venda` FOR EACH ROW BEGIN
  delete from entrada where venda_id = old.id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `venda_AFTER_INSERT`;
DELIMITER $$
CREATE TRIGGER `venda_AFTER_INSERT` AFTER INSERT ON `venda` FOR EACH ROW BEGIN
 insert into entrada set descricao ="venda efetuada",tipoPagamento = new.tipoPagamento, venda_id = new.id, valor = new.valor * new.quantidade, funcionario = new.funcionario;
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `apartamento`
--
ALTER TABLE `apartamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `configuracao`
--
ALTER TABLE `configuracao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `consumo`
--
ALTER TABLE `consumo`
  ADD PRIMARY KEY (`id`,`produto_id`),
  ADD KEY `fk_consumo_produto1_idx` (`produto_id`),
  ADD KEY `fk_consumo_reserva1_idx` (`reserva_id`);

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `empresa_has_hospede`
--
ALTER TABLE `empresa_has_hospede`
  ADD PRIMARY KEY (`Empresa_id`,`hospede_id`);

--
-- Índices de tabela `entrada`
--
ALTER TABLE `entrada`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_entrada_pagamento1_idx` (`pagamento_id`),
  ADD KEY `fk_entrada_venda1_idx` (`venda_id`);

--
-- Índices de tabela `entradaestoque`
--
ALTER TABLE `entradaestoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_entradaEstoque_produto1_idx` (`produto_id`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_estoque_produto1_idx` (`produto_id`);

--
-- Índices de tabela `hospede`
--
ALTER TABLE `hospede`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movimento`
--
ALTER TABLE `movimento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_movimento_entrada1_idx` (`entrada_id`),
  ADD KEY `fk_movimento_saida1_idx` (`saida_id`);

--
-- Índices de tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pagamento_reserva1_idx` (`reserva_id`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reserva_apartamento1_idx` (`apartamento_id`),
  ADD KEY `fk_reserva_hospede1_idx` (`hospede_id`);

--
-- Índices de tabela `saida`
--
ALTER TABLE `saida`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios_has_permissoes`
--
ALTER TABLE `usuarios_has_permissoes`
  ADD PRIMARY KEY (`usuarios_id`,`permissoes_id`);

--
-- Índices de tabela `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_venda_produto1_idx` (`produto_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `apartamento`
--
ALTER TABLE `apartamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `configuracao`
--
ALTER TABLE `configuracao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `consumo`
--
ALTER TABLE `consumo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entrada`
--
ALTER TABLE `entrada`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `entradaestoque`
--
ALTER TABLE `entradaestoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `hospede`
--
ALTER TABLE `hospede`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `movimento`
--
ALTER TABLE `movimento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `saida`
--
ALTER TABLE `saida`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `venda`
--
ALTER TABLE `venda`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `consumo`
--
ALTER TABLE `consumo`
  ADD CONSTRAINT `fk_consumo_produto1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`),
  ADD CONSTRAINT `fk_consumo_reserva1` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Restrições para tabelas `entrada`
--
ALTER TABLE `entrada`
  ADD CONSTRAINT `fk_entrada_pagamento1` FOREIGN KEY (`pagamento_id`) REFERENCES `pagamento` (`id`),
  ADD CONSTRAINT `fk_entrada_venda1` FOREIGN KEY (`venda_id`) REFERENCES `venda` (`id`);

--
-- Restrições para tabelas `entradaestoque`
--
ALTER TABLE `entradaestoque`
  ADD CONSTRAINT `fk_entradaEstoque_produto1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_produto1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);

--
-- Restrições para tabelas `movimento`
--
ALTER TABLE `movimento`
  ADD CONSTRAINT `fk_movimento_entrada1` FOREIGN KEY (`entrada_id`) REFERENCES `entrada` (`id`),
  ADD CONSTRAINT `fk_movimento_saida1` FOREIGN KEY (`saida_id`) REFERENCES `saida` (`id`);

--
-- Restrições para tabelas `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `fk_pagamento_reserva1` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`);

--
-- Restrições para tabelas `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reserva_apartamento1` FOREIGN KEY (`apartamento_id`) REFERENCES `apartamento` (`id`),
  ADD CONSTRAINT `fk_reserva_hospede1` FOREIGN KEY (`hospede_id`) REFERENCES `hospede` (`id`);

--
-- Restrições para tabelas `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `fk_venda_produto1` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
