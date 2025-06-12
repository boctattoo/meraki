-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 12/06/2025 às 18:11
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `microl68_meraki`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_original` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `turma_original` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsavel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone_responsavel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_status` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `codigo_original`, `turma_original`, `idade`, `telefone`, `responsavel`, `telefone_responsavel`, `status`, `data_status`, `email`, `data_nascimento`, `turma_id`, `data_cadastro`, `curso_id`) VALUES
(1, 'Alessandro Ricardo Junqueira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, '1982-04-11', NULL, '2025-05-31 15:26:03', NULL),
(2, 'Alexandre Henrique Severino Cansado', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(3, 'Alice Domingues Pereira de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(4, 'Aline Ribeiro de Lima', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(5, 'Amanda Andressa dos Santos', NULL, NULL, NULL, '(14)99779-8323', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(6, 'Amilton Paulino do Nascimento', NULL, NULL, NULL, '14 99648-4302', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(8, 'Ana Carolina Cardoso Firmino', NULL, NULL, NULL, '1499019223', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(9, 'Ana Carolina Ferreira da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(10, 'Ana Júlia da Paixão', NULL, NULL, NULL, '14997457347', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(11, 'Ana Karoline da Silva', NULL, NULL, NULL, '89994095965', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(12, 'Ana Laura Navarro Ribeiro', NULL, NULL, NULL, '(14)99737-3618', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(13, 'Ana Lívia Moura e Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(14, 'Ana Livia Trize Nascimento', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(15, 'Ana Luisa de Almeida', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(16, 'Ana Luiza Navarro Ribeiro', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(17, 'Ana Luiza Pereira Morais', NULL, NULL, NULL, '1499156476 e 1499125', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(18, 'Ana Paula Beijo Freire', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(19, 'Andrea Alexandra do Nascimento', NULL, NULL, NULL, '(14) 981315366', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(20, 'Andrea Paola Chacin', NULL, NULL, NULL, '(14)99113-8493', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(21, 'Andressa dos Santos Nascimento', NULL, NULL, NULL, '14998223214', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(22, 'Anna Laura Nackabar Camara', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(23, 'Antonio Carlos Pereira dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(24, 'Arthur Bartsch  Parmegiani', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(25, 'Arthur Bartsch Parmegiani', NULL, NULL, NULL, '(14)99173-6203', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(26, 'Arthur Miguel de Oliveira Andrade Ignacio', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(27, 'Arthur Torres de Mattos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(28, 'Arthur Vinicius Rodrigues Garcia', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(29, 'Beatriz Almeida dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(30, 'Beatriz Domingues da Luz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(31, 'Beatriz Faria Vanderley', NULL, NULL, NULL, '(14)98158-5866', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(32, 'Beatriz Ferreira Carlos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(33, 'Beatriz Helena de Jesus Guilherme', NULL, NULL, NULL, '14 996152391', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(34, 'Beatriz Palaro de Freitas', NULL, NULL, NULL, '14998228661', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(36, 'Beatriz Rodrigues da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(37, 'Bruno Dutra Vieira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(38, 'Caio Murilo Alves Ferreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(39, 'Camila Cristina Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(40, 'Camille Vitoria de Lima Bartolomeu', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(41, 'Carlos Alberto Ferreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(42, 'Clarete Aparecida Sene', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(43, 'Claudinor Pedroso', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(44, 'Consuelo Jessica Basile Barbosa', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(45, 'Daniel Anderson de Santos', NULL, NULL, NULL, '14989409563', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(46, 'Daniel Henrique Mendes de Moraes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(47, 'Davi Lucas Lopes Martins', NULL, NULL, NULL, '14991667176', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(48, 'Diogo Airton Pereira Falcão', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(49, 'Eduardo Augusto Rodrigues Serra', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(50, 'Eduardo Augusto Theodoro da Silva ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(51, 'Eduardo Libório Villaça Zogheib', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(52, 'Elizabeth de Oliveira Xavier', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(53, 'Enzo Simoes Marrangao', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(54, 'Enzo Victor dos Santos Ramos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(55, 'Erica Mota', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(56, 'Fábio Henrique Caetano de Martins Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(58, 'Fabiola Diniz da Silva', NULL, NULL, NULL, '(14) 99637-3550  Mãe', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(59, 'Felipe Inacio de Paula', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(60, 'Felipe Rodrigues Vianna dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(61, 'Flávia Rafaela dos Santos Ribeiro da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(62, 'Francine Reimi Honda', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(63, 'Francisco Miguel Da Cruz Tomaz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(64, 'Francisco Sebastião Leite da Silva Filho', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(65, 'Gabriel Andrade Galvani', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(66, 'Gabriel Florentino dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(67, 'Gabriel Galdino de Alcantara', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(68, 'Gabriel Garcia Avarenga Camargo ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(69, 'Gabriel Gomes dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(70, 'Gabriel Henrique dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(71, 'Gabriela Carminato Machado', NULL, NULL, NULL, '14 991943232', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(72, 'Gabriela Criscione Monchelato', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(73, 'Gabriela Pizzolio Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(74, 'Gabrielly Mauaya Castro de Oliveira', NULL, NULL, NULL, '14 988387244', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(75, 'Gabrielly Ribeiro Farias', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(76, 'Giancarlos Artero Gabriel', NULL, NULL, NULL, '14997978582', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(77, 'Gilson Coelho da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(78, 'Guilherme Henrique de Farias', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(79, 'Guilherme Silveira Antonio de Castro', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(80, 'Gustavo Terto Simplicio', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(81, 'Heber Rodrigues Gutierres', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(82, 'Henzo Mendonça Farias', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(83, 'Higor Nogueira de Morais Peres', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(84, 'Hilana Silva dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(85, 'Igor Pereira Cesar', NULL, NULL, NULL, '(14)99126-2864', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(86, 'Ingrid Lohany da Silva Lima', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(87, 'Isabel Kolbec', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(88, 'Isabel Kolbeck', NULL, NULL, NULL, '(14) 998020165', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(89, 'Isabela Ortiz da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(90, 'Isabelle Beijo Freire', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(91, 'Isabelly Rodrigues do Vale', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(92, 'Isabelly Vitoria Suite', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(93, 'Jane Cristina Fernandes Pereira', NULL, NULL, NULL, '1432036438 e 1499881', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(94, 'Jason Pereira dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(95, 'Jennifer Caroliny Napoziano', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(96, 'João Henrique Eduardo de Oliveira', NULL, NULL, NULL, '14999069964', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(97, 'João Pedro de Oliveira Andrade', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(98, 'João Pedro Lima Ferreira Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(99, 'João Victor Cavalcanti', NULL, NULL, NULL, '14998607122', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(100, 'João Victor Correa Nascimento', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(101, 'João Vitor Loureiro', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(102, 'Joice Ferreira Barbosa', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(103, 'Jonas Quintiliano', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(104, 'Jonatas Aragão Almeida', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(105, 'Jorge André Santana Marques', NULL, NULL, NULL, '(14)99869-4493', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(106, 'José Carlos de Souza', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(107, 'José Carlos de Souza 1', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(108, 'José Carlos de Souza 2', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(109, 'José Francisco Fernandes da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(110, 'Julia Aparecida dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(111, 'Julia Ferreira Borges', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(112, 'Julia Oliveira Lopes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(113, 'Juliane da Silva Oliveira', NULL, NULL, NULL, '(14) 998477603', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(114, 'Karolaine Leonel dos Santos', NULL, NULL, NULL, '(14) 996430458', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(115, 'Katia Arruda Brante', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(116, 'Kauan de jesus Oliveira', NULL, NULL, NULL, '(14) 99706-0103', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(117, 'Kauan Prado Marques', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(118, 'Kayssa Vitoria Garcia Stequer Stecher', NULL, NULL, NULL, '(14)99889-8675', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(119, 'Kellen Cristina da Silva Anorato', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(120, 'Kelly Hapuque Fernandes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(121, 'Kerollyn Yasmin Medrade de Carvalho', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(122, 'Kethney Cristine Correa Cunha', NULL, NULL, NULL, '(14)99864-3531', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(123, 'Kiara Pereira Gonçalves de Oliveira', NULL, NULL, NULL, '(14) 997953326', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(124, 'Laila Yone Luiz Victorino', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(125, 'Laisyanne Dantas Viana', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(126, 'Larissa Rodrigues Delchiano', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(127, 'Laura Batista Novais de Oliveira Lopes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(128, 'Laura Cassaro Fernandes ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(129, 'Laura Teresa Pereira de Oliveira', NULL, NULL, NULL, '14997249346', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(130, 'Laysa de Paula Carvalho', NULL, NULL, NULL, '(14)98171-2250', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(131, 'Leonardo Camargo de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(132, 'Leonardo dos Reis Sanches', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(133, 'Leonardo Francisco Lucheiz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(134, 'Leonardo Henrique Garcia Domingos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(135, 'Leonardo Shirazawa de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(136, 'Leticia Bottari Lopes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(137, 'Leticia Carolina Marcondes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(138, 'Leticia Titz Sene Alegranci', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(139, 'Lilian Pereira Gomes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(140, 'Livia Pereira Lopes Moreno', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(141, 'Lorena Charlois', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(142, 'Lorena Luiza Miranda da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(143, 'Lorena Vitoria Bento Torres', NULL, NULL, NULL, '(14)99627-6771', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(144, 'Lorens Issau Sueta Makino', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(145, 'Luana Beatriz Marques ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(146, 'Luana da Silva Xavier', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(147, 'Luana Tauani Rodrigues', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(148, 'Lucas Arantes de Oliveira de Sá', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(149, 'Lucas Diego Santos Anacleto da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(150, 'Lucas Gabriel Canhete Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(151, 'Lucas Henrique Medeiros da Silva Trifilio', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(152, 'Lucas Keizo Sakai Takata', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(153, 'Luis Felippe Rubens de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(154, 'Luis Gustavo Gomes de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(155, 'Luis Henrique Benicio', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(156, 'Luiz Felipe Martins', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(157, 'Luiz Fernando Anorato da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(158, 'Luiz Gustavo Prudêncio', NULL, NULL, NULL, '(14)996045563', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(159, 'Luiz Gustavo Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(160, 'Manuela Fernandes Sudré', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(161, 'Manuela Vitória Regini', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(162, 'Marcelo Antunes Bento', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(163, 'Marcelo Henrique Prado Junior', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(164, 'Marco Antonio Moreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(165, 'Marcos Rafael Ubda Cavichione', NULL, NULL, NULL, '14991615088 e 149980', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(166, 'Marcos Vinicius Damazio de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(167, 'Maria Candida Garcia', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(168, 'Maria Clara Cardoso Martires', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(170, 'Maria Clara de Camargo Costa', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(171, 'Maria Clara Martins', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(172, 'Maria Clara Moraes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(173, 'Maria Clara Pinheiro Montanari', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(174, 'Maria Clara Pinheiro Montari', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(175, 'Maria Eduarda Baldiuno', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(176, 'Maria Eduarda Batista de Oliveira', NULL, NULL, NULL, '(12)99707-3211', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(177, 'Maria Eduarda Crispim de Amorim', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(178, 'Maria Eduarda dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(179, 'Maria Eduarda dos Santos Cardoso', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(180, 'Maria Eduarda Moreira de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(181, 'Maria Eduarda Rodrigues Gimenes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(182, 'Maria Gabrielly Assis Xavier', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(183, 'Maria Klara Storino Scalpi Espelho', NULL, NULL, NULL, '1499674-4205', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(184, 'Maria Klara Storino Scalpi Espilho', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(185, 'Maria Noêmia Gomes de Melo Magalhaes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(187, 'Mariana Pereira de Moura', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(188, 'Marleide Duarte de Araujo', NULL, NULL, NULL, '14988410025', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(189, 'Mateus Novelli Mauricio', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(190, 'Matheus Fernades dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(191, 'Matheus Fernandes dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(192, 'Matheus Henrique Batista Moreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(193, 'Mayara Crsitina Adalberto', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(194, 'Melissa Oliveira Forte', NULL, NULL, NULL, '14991263936', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(195, 'Michelle Lima Ferreira Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(196, 'Miguel Martins Marinho', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(197, 'Miguel Souza Soares', NULL, NULL, NULL, '14997943839', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(198, 'Mikaely Maria da Silva', NULL, NULL, NULL, '(14)99178875', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(199, 'Milena dos Santos Martins', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(200, 'Murilo Henrique Cunha Bassi ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(201, 'Murylo Guimarães Teles Figueredo', NULL, NULL, NULL, '14991302853', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(202, 'Nadia Roseli dos Santos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(203, 'Nataly Cabral Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(204, 'Nathalia Alves Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(205, 'Nathanael Ferreira ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(206, 'Nicolas Franceschetti Cerigatto', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(207, 'Nicole Aparecida Moreira de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(208, 'Nicollas Santos Mukai', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(209, 'Nicollas Thomaz da Silva', NULL, NULL, NULL, '14 98183-0364', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(210, 'Nicoly Guimarães Sampaio', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(211, 'Nikolas Augusto da Silva Goés', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(212, 'Noa Endo Murioka', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(213, 'Paola Sabrina da Silva Contador', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(214, 'Patricia Coutinho', NULL, NULL, NULL, '13981161638', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(215, 'Patricia Helena', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(216, 'Patricia Silva França Ferreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(217, 'Pedro de Lima Rodrigues', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(218, 'Pedro Henrico Guedes Leme Montovani', NULL, NULL, NULL, '14998365406', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(219, 'Pedro Henrique Correia', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(220, 'Pedro Henrique Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(221, 'Pedro Henrique Rodrigues Lofano', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(222, 'Pedro Henrique Rossini da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(223, 'Pietro Luca Campos', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(224, 'Priscila Aparecida de Almeida', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(225, 'Rafael Scarparo', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(226, 'Raiany Jesus de Oliveira', NULL, NULL, NULL, '(14)99706-0103', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(227, 'Ramira Alves Martins', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(228, 'Raphael Fernando Egydio Alves ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(229, 'Raphaela Vieira Giacomo Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(230, 'Raul Alves Proença', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(231, 'Raul Moraes Moda', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(232, 'Rayssa Candido Mainini', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(233, 'Rebeca Mirella de Almeida', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(234, 'Rebeca Rayssa Colasso', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(235, 'Regiane Nunes dos Santos', NULL, NULL, NULL, '14991091412', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(236, 'Renan do Nascimento Gonçalves', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(237, 'Richarlyson José da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(238, 'Riquelme dos Santos Sobral', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(239, 'Robson de Lima Rodrigues ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(240, 'Robson Henrique de Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(241, 'Rosangela Aparecida de Oliveira Garcia', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(242, 'Rosangela Maria Saracini', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(243, 'Rosilene Aparecida Fagundes ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(244, 'Ruan Lucas do Nascimento Lisboa', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(245, 'Ryan Arruda Brante', NULL, NULL, NULL, '(14)96197-9102', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(246, 'Sabrina Alexandra Pereira Gomes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(247, 'Samuel Anselmo França Ferreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(248, 'Samuel Henrique Colasso', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(249, 'Samuel Santos da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(250, 'Sara Danieli Parizoto', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(251, 'Sarah Pereira Finoti', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(252, 'Savio Augusto Pacheco Garcia', NULL, NULL, NULL, '14996572696', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(253, 'Sheron Angel Lopes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(254, 'Simone Torres', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(255, 'Sirlei Gomes De Oliveira Alves', NULL, NULL, NULL, '14 99726 8206', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(256, 'Sirlene Beserra de Melo', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(257, 'Tanilde de Jesus Oliveira Muniz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(258, 'Thais Caroline de Almeida Moura', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(259, 'Thales Guilherme Colasso Gasparelo', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(260, 'Thallis Augusto Verati da Costa', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(261, 'Thamyres Vitória Oliveira Muniz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(262, 'Thayla Maria Nogueira Morais', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(263, 'Thiago Santamarina', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(264, 'Tieme Ferreira Vaz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(265, 'Valdemir Fernandes de Souza', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(266, 'Valentina de Carvalho Ayala Gomes', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(267, 'Victor Caio Fernandes da Conceição', NULL, NULL, NULL, '(14)98839-6846', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(268, 'Victor Gabriel Martins', NULL, NULL, NULL, '(14) 99763-0436', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(269, 'Victor Hugo Bortolatto Mendonça', NULL, NULL, NULL, '14 99168 9051', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(270, 'Victor Hugo da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(271, 'Victor Luis Pereira da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(272, 'Victoria Teodoro de Pontes Ruiz', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(273, 'Vitor Gabriel Farias', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(274, 'Vitor Marinello Mergi', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(275, 'Vitória Gabriele de Lima Moreira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(276, 'Vitória Kauâny Silva Santos', NULL, NULL, NULL, '14997761411', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(277, 'Viviane Cecília dos Reis', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(278, 'Wesley Gabriel dos Santos Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(279, 'Wyllian Oliveira J. da Silva', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(280, 'Yago Santos Oliveira ', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(281, 'Yasmim Vitoria Cunha Bassi', NULL, NULL, NULL, NULL, NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(282, 'Yasmin Marcondes Marques', NULL, NULL, NULL, '(14)99778-0590', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(283, 'Yuri Miguel Vernier da Silva', NULL, NULL, NULL, '14991791969', NULL, NULL, 'ativo', NULL, NULL, NULL, NULL, '2025-05-31 15:26:03', NULL),
(795, 'João da Silva', NULL, NULL, NULL, '(14) 99999-1234', 'Maria da Silva', NULL, NULL, NULL, NULL, '2004-05-10', NULL, '2025-06-01 18:13:09', 0),
(796, 'teste01', NULL, NULL, NULL, '(14) 99999-1234', 'Maria da Silva', NULL, 'ativo', NULL, NULL, '2004-05-10', NULL, '2025-06-05 12:18:06', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_backup`
--

CREATE TABLE `alunos_backup` (
  `id` int(11) NOT NULL DEFAULT '0',
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `codigo_original` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `turma_original` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsavel` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefone_responsavel` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_turmas`
--

CREATE TABLE `alunos_turmas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `data_atribuicao` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `alunos_turmas`
--

INSERT INTO `alunos_turmas` (`id`, `aluno_id`, `turma_id`, `data_atribuicao`, `ativo`) VALUES
(662, 264, 36, '2025-06-01', 1),
(663, 264, 40, '2025-06-01', 1),
(664, 264, 29, '2025-06-01', 1),
(665, 264, 33, '2025-06-01', 1),
(666, 48, 29, '2025-06-02', 1),
(667, 111, 29, '2025-06-02', 1),
(668, 73, 29, '2025-06-02', 1),
(669, 168, 29, '2025-06-02', 1),
(670, 42, 29, '2025-06-02', 1),
(671, 144, 29, '2025-06-02', 1),
(672, 64, 29, '2025-06-02', 1),
(673, 166, 29, '2025-06-02', 1),
(674, 796, 29, '2025-06-05', 1),
(675, 1, 40, '2025-06-05', 1),
(676, 1, 34, '2025-06-05', 1),
(677, 3, 45, '2025-06-05', 1),
(678, 4, 40, '2025-06-05', 1),
(679, 4, 32, '2025-06-05', 1),
(680, 4, 33, '2025-06-05', 1),
(681, 5, 50, '2025-06-05', 1),
(682, 6, 32, '2025-06-05', 1),
(683, 6, 35, '2025-06-05', 1),
(684, 8, 30, '2025-06-05', 1),
(685, 9, 30, '2025-06-05', 1),
(686, 10, 47, '2025-06-05', 1),
(687, 12, 50, '2025-06-05', 1),
(688, 14, 50, '2025-06-05', 1),
(689, 16, 32, '2025-06-05', 1),
(690, 17, 32, '2025-06-05', 1),
(691, 18, 37, '2025-06-05', 1),
(692, 19, 36, '2025-06-05', 1),
(693, 20, 40, '2025-06-05', 1),
(694, 21, 48, '2025-06-05', 1),
(695, 22, 50, '2025-06-05', 1),
(696, 24, 45, '2025-06-05', 1),
(697, 25, 55, '2025-06-05', 1),
(698, 26, 31, '2025-06-05', 1),
(699, 27, 48, '2025-06-05', 1),
(700, 28, 36, '2025-06-05', 1),
(701, 29, 34, '2025-06-05', 1),
(702, 30, 45, '2025-06-05', 1),
(703, 31, 45, '2025-06-05', 1),
(704, 32, 50, '2025-06-05', 1),
(705, 33, 39, '2025-06-05', 1),
(706, 34, 37, '2025-06-05', 1),
(707, 34, 30, '2025-06-05', 1),
(708, 36, 43, '2025-06-05', 1),
(709, 37, 44, '2025-06-05', 1),
(710, 38, 36, '2025-06-05', 1),
(711, 39, 43, '2025-06-05', 1),
(712, 40, 38, '2025-06-05', 1),
(713, 41, 31, '2025-06-05', 1),
(714, 43, 32, '2025-06-05', 1),
(715, 44, 32, '2025-06-05', 1),
(716, 46, 43, '2025-06-05', 1),
(717, 47, 45, '2025-06-05', 1),
(718, 45, 44, '2025-06-05', 1),
(719, 49, 44, '2025-06-05', 1),
(720, 50, 35, '2025-06-05', 1),
(721, 51, 36, '2025-06-05', 1),
(722, 52, 42, '2025-06-05', 1),
(723, 53, 38, '2025-06-05', 1),
(724, 54, 50, '2025-06-05', 1),
(725, 56, 45, '2025-06-05', 1),
(726, 58, 45, '2025-06-05', 1),
(727, 60, 50, '2025-06-05', 1),
(728, 61, 44, '2025-06-05', 1),
(729, 62, 50, '2025-06-05', 1),
(730, 63, 41, '2025-06-05', 1),
(731, 65, 39, '2025-06-05', 1),
(732, 66, 30, '2025-06-05', 1),
(733, 68, 55, '2025-06-05', 1),
(734, 68, 45, '2025-06-05', 1),
(735, 69, 41, '2025-06-05', 1),
(736, 70, 34, '2025-06-05', 1),
(737, 71, 44, '2025-06-05', 1),
(738, 72, 44, '2025-06-05', 1),
(739, 74, 44, '2025-06-05', 1),
(740, 75, 37, '2025-06-05', 1),
(741, 76, 40, '2025-06-05', 1),
(742, 77, 47, '2025-06-05', 1),
(743, 78, 50, '2025-06-05', 1),
(744, 79, 36, '2025-06-05', 1),
(745, 80, 48, '2025-06-05', 1),
(746, 82, 39, '2025-06-05', 1),
(747, 83, 45, '2025-06-05', 1),
(748, 84, 30, '2025-06-05', 1),
(749, 85, 35, '2025-06-05', 1),
(750, 86, 47, '2025-06-05', 1),
(751, 87, 39, '2025-06-05', 1),
(752, 87, 32, '2025-06-05', 1),
(753, 88, 39, '2025-06-05', 1),
(754, 89, 45, '2025-06-05', 1),
(755, 90, 50, '2025-06-05', 1),
(756, 91, 48, '2025-06-05', 1),
(757, 92, 48, '2025-06-05', 1),
(758, 94, 41, '2025-06-05', 1),
(759, 95, 32, '2025-06-05', 1),
(760, 96, 34, '2025-06-05', 1),
(761, 97, 31, '2025-06-05', 1),
(762, 98, 34, '2025-06-05', 1),
(763, 100, 34, '2025-06-05', 1),
(764, 99, 48, '2025-06-05', 1),
(765, 101, 48, '2025-06-05', 1),
(766, 104, 41, '2025-06-05', 1),
(767, 103, 41, '2025-06-05', 1),
(768, 105, 34, '2025-06-05', 1),
(769, 106, 29, '2025-06-05', 1),
(770, 107, 33, '2025-06-05', 1),
(771, 108, 40, '2025-06-05', 1),
(772, 109, 42, '2025-06-05', 1),
(773, 110, 44, '2025-06-05', 1),
(774, 112, 39, '2025-06-05', 1),
(775, 113, 50, '2025-06-05', 1),
(776, 114, 43, '2025-06-05', 1),
(777, 115, 35, '2025-06-05', 1),
(778, 116, 36, '2025-06-05', 1),
(779, 117, 52, '2025-06-05', 1),
(780, 118, 41, '2025-06-05', 1),
(781, 120, 47, '2025-06-05', 1),
(782, 121, 53, '2025-06-05', 1),
(783, 121, 38, '2025-06-05', 1),
(784, 122, 48, '2025-06-05', 1),
(785, 123, 45, '2025-06-05', 1),
(786, 124, 50, '2025-06-05', 1),
(787, 125, 37, '2025-06-05', 1),
(788, 126, 55, '2025-06-05', 1),
(789, 127, 42, '2025-06-05', 1),
(790, 128, 38, '2025-06-05', 1),
(791, 128, 34, '2025-06-05', 1),
(792, 129, 50, '2025-06-05', 1),
(793, 130, 38, '2025-06-05', 1),
(794, 131, 39, '2025-06-05', 1),
(795, 132, 33, '2025-06-05', 1),
(796, 133, 50, '2025-06-05', 1),
(797, 134, 31, '2025-06-05', 1),
(798, 135, 47, '2025-06-05', 1),
(799, 136, 39, '2025-06-05', 1),
(800, 137, 47, '2025-06-05', 1),
(801, 138, 45, '2025-06-05', 1),
(802, 139, 35, '2025-06-05', 1),
(803, 140, 38, '2025-06-05', 1),
(804, 141, 39, '2025-06-05', 1),
(805, 142, 34, '2025-06-05', 1),
(806, 143, 48, '2025-06-05', 1),
(807, 147, 43, '2025-06-05', 1),
(808, 148, 43, '2025-06-05', 1),
(809, 146, 36, '2025-06-05', 1),
(810, 149, 35, '2025-06-05', 1),
(811, 150, 48, '2025-06-05', 1),
(812, 151, 32, '2025-06-05', 1),
(813, 152, 41, '2025-06-05', 1),
(814, 59, 50, '2025-06-09', 1),
(815, 272, 31, '2025-06-09', 1),
(816, 242, 31, '2025-06-09', 1),
(817, 231, 33, '2025-06-09', 1),
(818, 219, 34, '2025-06-09', 1),
(819, 195, 34, '2025-06-09', 1),
(820, 161, 39, '2025-06-09', 1),
(821, 161, 45, '2025-06-09', 1),
(822, 160, 48, '2025-06-09', 1),
(823, 225, 34, '2025-06-09', 1),
(824, 233, 34, '2025-06-09', 1),
(825, 209, 34, '2025-06-09', 1),
(826, 190, 38, '2025-06-09', 1),
(827, 190, 34, '2025-06-09', 1),
(828, 164, 35, '2025-06-09', 1),
(829, 170, 35, '2025-06-09', 1),
(830, 245, 35, '2025-06-09', 1),
(831, 259, 47, '2025-06-09', 1),
(832, 228, 36, '2025-06-09', 1),
(833, 158, 41, '2025-06-11', 1),
(834, 159, 45, '2025-06-11', 1),
(835, 162, 39, '2025-06-11', 1),
(836, 163, 44, '2025-06-11', 1),
(837, 165, 32, '2025-06-11', 1),
(838, 167, 32, '2025-06-11', 1),
(839, 172, 44, '2025-06-11', 1),
(840, 171, 44, '2025-06-11', 1),
(841, 173, 44, '2025-06-11', 1),
(842, 174, 45, '2025-06-11', 1),
(843, 175, 36, '2025-06-11', 1),
(844, 176, 32, '2025-06-11', 1),
(845, 177, 44, '2025-06-11', 1),
(846, 178, 44, '2025-06-11', 1),
(847, 179, 44, '2025-06-11', 1),
(848, 180, 43, '2025-06-11', 1),
(849, 181, 38, '2025-06-11', 1),
(850, 182, 37, '2025-06-11', 1),
(851, 183, 55, '2025-06-11', 1),
(852, 184, 36, '2025-06-11', 1),
(853, 187, 41, '2025-06-11', 1),
(854, 189, 30, '2025-06-11', 1),
(855, 191, 37, '2025-06-11', 1),
(856, 191, 45, '2025-06-11', 1),
(857, 191, 34, '2025-06-11', 1),
(858, 192, 42, '2025-06-11', 1),
(859, 193, 48, '2025-06-11', 1),
(860, 194, 38, '2025-06-11', 1),
(861, 196, 39, '2025-06-11', 1),
(862, 197, 42, '2025-06-11', 1),
(863, 198, 45, '2025-06-11', 1),
(864, 199, 46, '2025-06-11', 1),
(865, 200, 44, '2025-06-11', 1),
(866, 201, 47, '2025-06-11', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `backup_alunos_original`
--

CREATE TABLE `backup_alunos_original` (
  `id` int(11) NOT NULL DEFAULT '0',
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `idade` int(11) DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `responsavel` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefone_responsavel` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `backup_alunos_original`
--

INSERT INTO `backup_alunos_original` (`id`, `nome`, `idade`, `telefone`, `responsavel`, `telefone_responsavel`, `status`) VALUES
(1, '45730', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(2, '45726', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(3, '45526', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(4, '45681', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(5, '45323', NULL, 'ONLINE', NULL, NULL, NULL),
(6, '45516', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(7, '45517', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(8, '45556', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(9, '45320', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(10, '45348', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(11, '45587', NULL, 'TRANCADOS', NULL, NULL, NULL),
(12, '45280', NULL, 'ONLINE', NULL, NULL, NULL),
(13, '45202', NULL, 'ING - QUI - 10:00 às', NULL, NULL, NULL),
(14, '45075', NULL, 'ONLINE', NULL, NULL, NULL),
(15, '45685', NULL, 'TRANCADOS', NULL, NULL, NULL),
(16, '45334', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(17, '45546', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(18, '45516', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(19, '44951', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(20, '45504', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(21, '45588', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(22, '45510', NULL, 'ONLINE', NULL, NULL, NULL),
(23, '45797', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(24, '45409', NULL, 'ING - Sáb - 08:00 às', NULL, NULL, NULL),
(25, '45479', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(26, '45677', NULL, 'Seg - 17:30 às 19:30', NULL, NULL, NULL),
(27, '45628', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(28, '45433', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(29, '45507', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(30, '45526', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(31, '45353', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(32, '45020', NULL, 'ONLINE', NULL, NULL, NULL),
(33, '45353', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(34, '45558', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(35, '45558', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(36, '45509', NULL, 'ONLINE t2', NULL, NULL, NULL),
(37, '45045', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(38, '45685', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(39, '45468', NULL, 'ONLINE t2', NULL, NULL, NULL),
(40, '45666', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(41, '45678', NULL, 'Seg - 17:30 às 19:30', NULL, NULL, NULL),
(42, '45779', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(43, '45561', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(44, '45561', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(45, '45625', NULL, 'TRANCADOS', NULL, NULL, NULL),
(46, '45091', NULL, 'ONLINE t2', NULL, NULL, NULL),
(47, '45621', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(48, '45637', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(49, '45491', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(50, '45779', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(51, '45149', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(52, '45694', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(53, '45723', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(54, '45385', NULL, 'ONLINE', NULL, NULL, NULL),
(55, '44974', NULL, 'TRANCADOS', NULL, NULL, NULL),
(56, '45736', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(57, '45462', NULL, 'ONLINE', NULL, NULL, NULL),
(58, '45168', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(59, '45566', NULL, 'TRANCADOS', NULL, NULL, NULL),
(60, '45575', NULL, 'ONLINE', NULL, NULL, NULL),
(61, '45710', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(62, '45671', NULL, 'ONLINE', NULL, NULL, NULL),
(63, '45491', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(64, '45694', NULL, 'Seg - 10:00 às 12:00', NULL, NULL, NULL),
(65, '45280', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(66, '45626', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(67, '45639', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(68, '45803', NULL, 'ING - SÁB - 08:00 às', NULL, NULL, NULL),
(69, '45475', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(70, '45499', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(71, '45353', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(72, '45726', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(73, '45068', NULL, 'Seg - 10:00 às 12:00', NULL, NULL, NULL),
(74, '45156', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(75, '45685', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(76, '44965', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(77, '45742', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(78, '45264', NULL, 'ONLINE', NULL, NULL, NULL),
(79, '45378', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(80, '45803', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(81, '45299', NULL, 'TRANCADOS', NULL, NULL, NULL),
(82, '45471', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(83, '45677', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(84, '45414', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(85, '45301', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(86, '45194', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(87, '45492', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(88, '44951', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(89, '45014', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(90, '45517', NULL, 'ONLINE', NULL, NULL, NULL),
(91, '45688', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(92, '45731', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(93, '45539', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(94, '45694', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(95, '44959', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(96, '45331', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(97, '45677', NULL, 'Seg - 17:30 às 19:30', NULL, NULL, NULL),
(98, '45791', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(99, '45107', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(100, '45703', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(101, '45780', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(102, '45717', NULL, 'TRANCADOS', NULL, NULL, NULL),
(103, '45517', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(104, '45731', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(105, '45503', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(106, '45797', NULL, 'Seg - 10:00 às 12:00', NULL, NULL, NULL),
(107, '45730', NULL, 'Ter - 10:00 às 12:00', NULL, NULL, NULL),
(108, '45730', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(109, '45755', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(110, '45692', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(111, '45705', NULL, 'Seg - 10:00 às 12:00', NULL, NULL, NULL),
(112, '45516', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(113, '44951', NULL, 'ONLINE', NULL, NULL, NULL),
(114, '44951', NULL, 'ONLINE t2', NULL, NULL, NULL),
(115, '45722', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(116, '45155', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(117, '45714', NULL, 'ING - SEG - 19:00 às', NULL, NULL, NULL),
(118, '45503', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(119, '45727', NULL, 'TRANCADOS', NULL, NULL, NULL),
(120, '45721', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(121, '45714', NULL, 'ING - TER - 15:30 às', NULL, NULL, NULL),
(122, '45204', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(123, '44951', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(124, '45084', NULL, 'ONLINE', NULL, NULL, NULL),
(125, '45716', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(126, '45111', NULL, 'ING - Sáb - 08:00 às', NULL, NULL, NULL),
(127, '45745', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(128, '45742', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(129, '44982', NULL, 'ONLINE', NULL, NULL, NULL),
(130, '45537', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(131, '45384', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(132, '45791', NULL, 'Ter - 10:00 às 12:00', NULL, NULL, NULL),
(133, '45665', NULL, 'ONLINE', NULL, NULL, NULL),
(134, '45675', NULL, 'Seg - 17:30 às 19:30', NULL, NULL, NULL),
(135, '45722', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(136, '45727', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(137, '45078', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(138, '45779', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(139, '45708', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(140, '45434', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(141, '45791', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(142, '45428', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(143, '45157', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(144, '45713', NULL, 'Seg - 10:00 às 12:00', NULL, NULL, NULL),
(145, '45696', NULL, 'TRANCADOS', NULL, NULL, NULL),
(146, '45079', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(147, '45640', NULL, 'ONLINE t2', NULL, NULL, NULL),
(148, '45453', NULL, 'ONLINE t2', NULL, NULL, NULL),
(149, '45521', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(150, '45524', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(151, '45681', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(152, '45332', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(153, '45462', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(154, '45603', NULL, 'TRANCADOS', NULL, NULL, NULL),
(155, '45666', NULL, 'ONLINE', NULL, NULL, NULL),
(156, '45665', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(157, '45727', NULL, 'TRANCADOS', NULL, NULL, NULL),
(158, '45124', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(159, '45078', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(160, '45398', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(161, '45433', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(162, '45637', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(163, '45631', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(164, '45771', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(165, '45509', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(166, '45702', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(167, '45384', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(168, '45299', NULL, 'ING - Sáb - 08:00 às', NULL, NULL, NULL),
(169, '45307', NULL, 'Seg - 10:00 às 12:00', NULL, NULL, NULL),
(170, '45715', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(171, '45016', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(172, '45640', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(173, '45048', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(174, '45603', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(175, '45370', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(176, '45334', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(177, '45201', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(178, '45675', NULL, 'TRANCADOS', NULL, NULL, NULL),
(179, '45075', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(180, '45495', NULL, 'ONLINE t2', NULL, NULL, NULL),
(181, '45470', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(182, '45521', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(183, '45374', NULL, 'ING - SÁB - 08:00 às', NULL, NULL, NULL),
(184, '45000', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(185, '45740', NULL, 'Ter - 10:00 às 12:00', NULL, NULL, NULL),
(186, '45740', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(187, '45533', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(188, '45618', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(189, '45740', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(190, '45586', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(191, '45586', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(192, '45323', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(193, '45477', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(194, '45339', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(195, '45717', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(196, '45595', NULL, 'Qua - 19:30 às 21:30', NULL, NULL, NULL),
(197, '45504', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(198, '45191', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(199, '45724', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(200, '45775', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(201, '44954', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(202, '45681', NULL, 'Qui - 10:00 às 12:00', NULL, NULL, NULL),
(203, '45467', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(204, '45486', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(205, '45722', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(206, '45565', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(207, '45496', NULL, 'TRANCADOS', NULL, NULL, NULL),
(208, '45400', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(209, '45565', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(210, '45394', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(211, '45625', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(212, '45414', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(213, '45016', NULL, 'ING - Sáb - 08:00 às', NULL, NULL, NULL),
(214, '45740', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(215, '45757', NULL, 'TRANCADOS', NULL, NULL, NULL),
(216, '45699', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(217, '45692', NULL, 'ONLINE t2', NULL, NULL, NULL),
(218, '45546', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(219, '45771', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(220, '45481', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(221, '45509', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(222, '45586', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(223, '45458', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(224, '45171', NULL, 'ONLINE', NULL, NULL, NULL),
(225, '45372', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(226, '45155', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(227, '45745', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(228, '45779', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(229, '45597', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(230, '45708', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(231, '45022', NULL, 'Ter - 10:00 às 12:00', NULL, NULL, NULL),
(232, '45714', NULL, 'ING - TER - 15:30 às', NULL, NULL, NULL),
(233, '45103', NULL, 'Ter - 15:30 às 17:30', NULL, NULL, NULL),
(234, '45713', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(235, '45609', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(236, '45045', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(237, '45295', NULL, 'ING - Ter - 15:30 às', NULL, NULL, NULL),
(238, '44973', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(239, '45744', NULL, 'ONLINE', NULL, NULL, NULL),
(240, '45474', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(241, '45726', NULL, 'Seg - 19:30 às 21:30', NULL, NULL, NULL),
(242, '45678', NULL, 'Seg - 17:30 às 19:30', NULL, NULL, NULL),
(243, '45769', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL),
(244, '45302', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(245, '45353', NULL, 'Ter - 19:30 às 21:30', NULL, NULL, NULL),
(246, '45479', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(247, '45693', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(248, '45713', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(249, '45112', NULL, 'Qua - 15:30 às 17:30', NULL, NULL, NULL),
(250, '45496', NULL, 'ONLINE t2', NULL, NULL, NULL),
(251, '45426', NULL, 'Qui - 15:30 às 17:30', NULL, NULL, NULL),
(252, '45575', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(253, '45490', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(254, '45479', NULL, 'ONLINE', NULL, NULL, NULL),
(255, '45398', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(256, '45772', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(257, '45721', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(258, '45667', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(259, '45394', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(260, '45639', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(261, '45721', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(262, '45681', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(263, '45664', NULL, 'TRANCADOS', NULL, NULL, NULL),
(264, '45531', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(265, '45409', NULL, 'TRANCADOS', NULL, NULL, NULL),
(266, '45349', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(267, '45261', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(268, '45098', NULL, 'Sab - 10:00 às 12:00', NULL, NULL, NULL),
(269, '45412', NULL, 'ING - Sáb - 08:00 às', NULL, NULL, NULL),
(270, '45604', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(271, '45803', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(272, '45724', NULL, 'ING - SÁB - 08:00 às', NULL, NULL, NULL),
(273, '45496', NULL, 'Qui - 19:30 às 21:30', NULL, NULL, NULL),
(274, '45703', NULL, 'Qui - 17:30 às 19:30', NULL, NULL, NULL),
(275, '45409', NULL, 'ING - Ter - 15:30 às', NULL, NULL, NULL),
(276, '45576', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(277, '45302', NULL, 'Qua - 08:00 às 10:00', NULL, NULL, NULL),
(278, '45391', NULL, 'Qua - 13:30 às 15:30', NULL, NULL, NULL),
(279, '45063', NULL, 'ONLINE t2', NULL, NULL, NULL),
(280, '45696', NULL, 'Sab - 13:00 às 15:00', NULL, NULL, NULL),
(281, '45775', NULL, 'Sab - 08:00 às 10:00', NULL, NULL, NULL),
(282, '45190', NULL, 'Sab - 15:00 às 17:00', NULL, NULL, NULL),
(283, '45341', NULL, 'Seg - 15:30 às 17:30', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `backup_alunos_turmas_original`
--

CREATE TABLE `backup_alunos_turmas_original` (
  `id` int(11) NOT NULL DEFAULT '0',
  `aluno_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `data_matricula` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contrato`
--

CREATE TABLE `contrato` (
  `id` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `tipo_pgto_id` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `modalidade_id` int(11) DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos`
--

CREATE TABLE `contratos` (
  `id` int(11) NOT NULL,
  `nome_aluno` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_nascimento_aluno` date DEFAULT NULL,
  `estado_civil` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profissao_aluno` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sexo` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endereco_aluno` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cep_aluno` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cidade_aluno` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefone_aluno` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cpf_cnpj_aluno` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome_responsavel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefone_responsavel` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome_pagador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_nascimento_pagador` date DEFAULT NULL,
  `endereco_pagador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cpf_cnpj_pagador` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profissao_pagador` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bairro_pagador` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cidade_pagador` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefone_pagador` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `celular_pagador` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valor_curso` decimal(10,2) DEFAULT NULL,
  `desconto_promocional` decimal(5,2) DEFAULT NULL,
  `valor_descontado` decimal(10,2) DEFAULT NULL,
  `material_didatico` decimal(10,2) DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `qtd_parcelas` int(11) DEFAULT NULL,
  `valor_parcela` decimal(10,2) DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `curso` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `turma` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duracao` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inicio_aulas` date DEFAULT NULL,
  `termino_aulas` date DEFAULT NULL,
  `dias_semana` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `horario` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `carga_horaria` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `turma_id` int(11) DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `consolidado` tinyint(1) DEFAULT '0',
  `aluno_id` int(11) DEFAULT NULL,
  `entrada` decimal(10,2) DEFAULT NULL,
  `parcela_integral` decimal(10,2) DEFAULT NULL,
  `desconto_pontualidade` decimal(10,2) DEFAULT NULL,
  `parcela_com_desconto` decimal(10,2) DEFAULT NULL,
  `parcela_material` decimal(10,2) DEFAULT NULL,
  `qtd_meses` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `contratos`
--

INSERT INTO `contratos` (`id`, `nome_aluno`, `data_nascimento_aluno`, `estado_civil`, `profissao_aluno`, `sexo`, `endereco_aluno`, `cep_aluno`, `cidade_aluno`, `telefone_aluno`, `cpf_cnpj_aluno`, `nome_responsavel`, `telefone_responsavel`, `nome_pagador`, `data_nascimento_pagador`, `endereco_pagador`, `cpf_cnpj_pagador`, `profissao_pagador`, `bairro_pagador`, `cidade_pagador`, `telefone_pagador`, `celular_pagador`, `valor_curso`, `desconto_promocional`, `valor_descontado`, `material_didatico`, `valor_total`, `qtd_parcelas`, `valor_parcela`, `data_vencimento`, `curso`, `turma`, `duracao`, `inicio_aulas`, `termino_aulas`, `dias_semana`, `horario`, `carga_horaria`, `criado_em`, `turma_id`, `curso_id`, `consolidado`, `aluno_id`, `entrada`, `parcela_integral`, `desconto_pontualidade`, `parcela_com_desconto`, `parcela_material`, `qtd_meses`) VALUES
(1, 'teste01', '2004-05-10', 'Solteiro', 'Estudante', 'Masculino', 'Rua das Flores, 123', '17000-000', 'Bauru', '(14) 99999-1234', '123.456.789-00', 'Maria da Silva', '(14) 98888-1234', 'Carlos da Silva', '1970-11-01', 'Av. Brasil, 321', '987.654.321-00', 'Autônomo', 'Jardim América', 'Bauru', '(14) 99111-2222', '(14) 99111-2222', 2000.00, 10.00, 1800.00, 150.00, 1950.00, 6, 325.00, '2025-06-10', 'Informática Profissional', 'Turma A', '6 meses', '2025-06-15', '2025-12-15', 'Segunda e Quarta', '14:00 - 16:00', '100h', '2025-05-29 21:16:54', NULL, NULL, 1, 796, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'teste02', '0000-00-00', 'solteiro', 'coiso', 'Masculino', 'coiso', '17010220', 'coiso', '11940103391', '29799944805', 'Maria', '11940103391', 'Roberto Silva', '1982-04-14', 'coiso', '29799944805', 'coiso', 'coiso', 'Bauru', '11940103391', '14999812381', 10.00, 20.00, 8.00, 10.00, 18.00, 36, 18.00, '0000-00-00', NULL, NULL, '42 meses', '2025-06-04', '2028-12-04', 'Quarta', 'Manhã', '318', '2025-06-02 21:23:11', 36, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'teste03', '0000-00-00', 'solteiro', 'coiso', 'Masculino', 'coiso', '17010220', 'coiso', '11940103391', '29799944805', 'Maria', '11940103391', 'Roberto Silva', '1982-04-14', 'coiso', '29799944805', 'coiso', 'coiso', 'Bauru', '11940103391', '14999812381', 250.00, 20.00, 8.00, 10.00, 18.00, 36, 18.00, '0000-00-00', NULL, NULL, '36 meses', '2025-06-07', '2028-06-07', 'Sábado', 'Manhã', '270', '2025-06-02 21:27:58', 44, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos_cursos`
--

CREATE TABLE `contratos_cursos` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `contratos_cursos`
--

INSERT INTO `contratos_cursos` (`id`, `contrato_id`, `curso_id`) VALUES
(1, 4, 12),
(2, 4, 11),
(3, 5, 13),
(4, 5, 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`) VALUES
(1, 'Web Designer'),
(2, 'Operador de Dados'),
(3, 'Marketing Digital'),
(4, 'Games & Aplicativos'),
(5, 'Profissional da Saúde'),
(6, 'Soft Skills'),
(7, 'Inglês'),
(8, 'Design Gráfico'),
(9, 'Técnico de TI'),
(10, 'Robótica'),
(11, 'Assistente Administrativo'),
(12, 'ADS'),
(13, 'Office Essencial'),
(14, 'Módulo Personalizado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `dias_semana`
--

CREATE TABLE `dias_semana` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `dias_semana`
--

INSERT INTO `dias_semana` (`id`, `nome`) VALUES
(1, 'Segunda'),
(2, 'Terça'),
(3, 'Quarta'),
(4, 'Quinta'),
(5, 'Sexta'),
(6, 'Sábado'),
(7, 'Domingo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `faq_meraki`
--

CREATE TABLE `faq_meraki` (
  `id` int(11) NOT NULL,
  `pergunta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resposta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `palavras_chave` text COLLATE utf8mb4_unicode_ci,
  `ativa` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `feriados`
--

CREATE TABLE `feriados` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('municipal','estadual','nacional') COLLATE utf8mb4_unicode_ci DEFAULT 'nacional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `feriados`
--

INSERT INTO `feriados` (`id`, `data`, `descricao`, `tipo`) VALUES
(1, '2025-01-01', 'Confraternização Universal', 'nacional'),
(2, '2025-04-18', 'Sexta-feira Santa', 'nacional'),
(3, '2025-04-21', 'Tiradentes', 'nacional'),
(4, '2025-05-01', 'Dia do Trabalho', 'nacional'),
(5, '2025-09-07', 'Independência do Brasil', 'nacional'),
(6, '2025-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(7, '2025-11-02', 'Finados', 'nacional'),
(8, '2025-11-15', 'Proclamação da República', 'nacional'),
(9, '2025-12-25', 'Natal', 'nacional'),
(10, '2025-07-09', 'Revolução Constitucionalista', 'estadual'),
(11, '2025-08-01', 'Aniversário de Bauru', 'municipal'),
(12, '2025-01-01', 'Confraternização Universal', 'nacional'),
(13, '2025-03-04', 'Carnaval', 'nacional'),
(14, '2025-04-18', 'Sexta-feira Santa', 'nacional'),
(15, '2025-04-20', 'Páscoa', 'nacional'),
(16, '2025-04-21', 'Tiradentes', 'nacional'),
(17, '2025-05-01', 'Dia do Trabalho', 'nacional'),
(18, '2025-06-19', 'Corpus Christi', 'nacional'),
(19, '2025-09-07', 'Independência do Brasil', 'nacional'),
(20, '2025-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(21, '2025-11-02', 'Finados', 'nacional'),
(22, '2025-11-15', 'Proclamação da República', 'nacional'),
(23, '2025-12-25', 'Natal', 'nacional'),
(24, '2026-01-01', 'Confraternização Universal', 'nacional'),
(25, '2026-02-17', 'Carnaval', 'nacional'),
(26, '2026-04-03', 'Sexta-feira Santa', 'nacional'),
(27, '2026-04-05', 'Páscoa', 'nacional'),
(28, '2026-04-21', 'Tiradentes', 'nacional'),
(29, '2026-05-01', 'Dia do Trabalho', 'nacional'),
(30, '2026-06-04', 'Corpus Christi', 'nacional'),
(31, '2026-09-07', 'Independência do Brasil', 'nacional'),
(32, '2026-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(33, '2026-11-02', 'Finados', 'nacional'),
(34, '2026-11-15', 'Proclamação da República', 'nacional'),
(35, '2026-12-25', 'Natal', 'nacional'),
(36, '2027-01-01', 'Confraternização Universal', 'nacional'),
(37, '2027-02-09', 'Carnaval', 'nacional'),
(38, '2027-03-26', 'Sexta-feira Santa', 'nacional'),
(39, '2027-03-28', 'Páscoa', 'nacional'),
(40, '2027-04-21', 'Tiradentes', 'nacional'),
(41, '2027-05-01', 'Dia do Trabalho', 'nacional'),
(42, '2027-05-27', 'Corpus Christi', 'nacional'),
(43, '2027-09-07', 'Independência do Brasil', 'nacional'),
(44, '2027-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(45, '2027-11-02', 'Finados', 'nacional'),
(46, '2027-11-15', 'Proclamação da República', 'nacional'),
(47, '2027-12-25', 'Natal', 'nacional'),
(48, '2028-01-01', 'Confraternização Universal', 'nacional'),
(49, '2028-02-29', 'Carnaval', 'nacional'),
(50, '2028-04-14', 'Sexta-feira Santa', 'nacional'),
(51, '2028-04-16', 'Páscoa', 'nacional'),
(52, '2028-04-21', 'Tiradentes', 'nacional'),
(53, '2028-05-01', 'Dia do Trabalho', 'nacional'),
(54, '2028-06-15', 'Corpus Christi', 'nacional'),
(55, '2028-09-07', 'Independência do Brasil', 'nacional'),
(56, '2028-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(57, '2028-11-02', 'Finados', 'nacional'),
(58, '2028-11-15', 'Proclamação da República', 'nacional'),
(59, '2028-12-25', 'Natal', 'nacional'),
(60, '2029-01-01', 'Confraternização Universal', 'nacional'),
(61, '2029-02-13', 'Carnaval', 'nacional'),
(62, '2029-03-30', 'Sexta-feira Santa', 'nacional'),
(63, '2029-04-01', 'Páscoa', 'nacional'),
(64, '2029-04-21', 'Tiradentes', 'nacional'),
(65, '2029-05-01', 'Dia do Trabalho', 'nacional'),
(66, '2029-05-31', 'Corpus Christi', 'nacional'),
(67, '2029-09-07', 'Independência do Brasil', 'nacional'),
(68, '2029-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(69, '2029-11-02', 'Finados', 'nacional'),
(70, '2029-11-15', 'Proclamação da República', 'nacional'),
(71, '2029-12-25', 'Natal', 'nacional'),
(72, '2030-01-01', 'Confraternização Universal', 'nacional'),
(73, '2030-03-05', 'Carnaval', 'nacional'),
(74, '2030-04-19', 'Sexta-feira Santa', 'nacional'),
(75, '2030-04-21', 'Tiradentes', 'nacional'),
(76, '2030-04-21', 'Páscoa', 'nacional'),
(77, '2030-05-01', 'Dia do Trabalho', 'nacional'),
(78, '2030-06-20', 'Corpus Christi', 'nacional'),
(79, '2030-09-07', 'Independência do Brasil', 'nacional'),
(80, '2030-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(81, '2030-11-02', 'Finados', 'nacional'),
(82, '2030-11-15', 'Proclamação da República', 'nacional'),
(83, '2030-12-25', 'Natal', 'nacional'),
(84, '2031-01-01', 'Confraternização Universal', 'nacional'),
(85, '2031-02-25', 'Carnaval', 'nacional'),
(86, '2031-04-11', 'Sexta-feira Santa', 'nacional'),
(87, '2031-04-13', 'Páscoa', 'nacional'),
(88, '2031-04-21', 'Tiradentes', 'nacional'),
(89, '2031-05-01', 'Dia do Trabalho', 'nacional'),
(90, '2031-06-12', 'Corpus Christi', 'nacional'),
(91, '2031-09-07', 'Independência do Brasil', 'nacional'),
(92, '2031-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(93, '2031-11-02', 'Finados', 'nacional'),
(94, '2031-11-15', 'Proclamação da República', 'nacional'),
(95, '2031-12-25', 'Natal', 'nacional'),
(96, '2032-01-01', 'Confraternização Universal', 'nacional'),
(97, '2032-02-10', 'Carnaval', 'nacional'),
(98, '2032-03-26', 'Sexta-feira Santa', 'nacional'),
(99, '2032-03-28', 'Páscoa', 'nacional'),
(100, '2032-04-21', 'Tiradentes', 'nacional'),
(101, '2032-05-01', 'Dia do Trabalho', 'nacional'),
(102, '2032-05-27', 'Corpus Christi', 'nacional'),
(103, '2032-09-07', 'Independência do Brasil', 'nacional'),
(104, '2032-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(105, '2032-11-02', 'Finados', 'nacional'),
(106, '2032-11-15', 'Proclamação da República', 'nacional'),
(107, '2032-12-25', 'Natal', 'nacional'),
(108, '2033-01-01', 'Confraternização Universal', 'nacional'),
(109, '2033-03-01', 'Carnaval', 'nacional'),
(110, '2033-04-15', 'Sexta-feira Santa', 'nacional'),
(111, '2033-04-17', 'Páscoa', 'nacional'),
(112, '2033-04-21', 'Tiradentes', 'nacional'),
(113, '2033-05-01', 'Dia do Trabalho', 'nacional'),
(114, '2033-06-16', 'Corpus Christi', 'nacional'),
(115, '2033-09-07', 'Independência do Brasil', 'nacional'),
(116, '2033-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(117, '2033-11-02', 'Finados', 'nacional'),
(118, '2033-11-15', 'Proclamação da República', 'nacional'),
(119, '2033-12-25', 'Natal', 'nacional'),
(120, '2034-01-01', 'Confraternização Universal', 'nacional'),
(121, '2034-02-21', 'Carnaval', 'nacional'),
(122, '2034-04-07', 'Sexta-feira Santa', 'nacional'),
(123, '2034-04-09', 'Páscoa', 'nacional'),
(124, '2034-04-21', 'Tiradentes', 'nacional'),
(125, '2034-05-01', 'Dia do Trabalho', 'nacional'),
(126, '2034-06-08', 'Corpus Christi', 'nacional'),
(127, '2034-09-07', 'Independência do Brasil', 'nacional'),
(128, '2034-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(129, '2034-11-02', 'Finados', 'nacional'),
(130, '2034-11-15', 'Proclamação da República', 'nacional'),
(131, '2034-12-25', 'Natal', 'nacional'),
(132, '2035-01-01', 'Confraternização Universal', 'nacional'),
(133, '2035-02-06', 'Carnaval', 'nacional'),
(134, '2035-03-23', 'Sexta-feira Santa', 'nacional'),
(135, '2035-03-25', 'Páscoa', 'nacional'),
(136, '2035-04-21', 'Tiradentes', 'nacional'),
(137, '2035-05-01', 'Dia do Trabalho', 'nacional'),
(138, '2035-05-24', 'Corpus Christi', 'nacional'),
(139, '2035-09-07', 'Independência do Brasil', 'nacional'),
(140, '2035-10-12', 'Nossa Senhora Aparecida', 'nacional'),
(141, '2035-11-02', 'Finados', 'nacional'),
(142, '2035-11-15', 'Proclamação da República', 'nacional'),
(143, '2035-12-25', 'Natal', 'nacional');

-- --------------------------------------------------------

--
-- Estrutura para tabela `instrutores`
--

CREATE TABLE `instrutores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `instrutores`
--

INSERT INTO `instrutores` (`id`, `nome`) VALUES
(1, 'Kevin Brian Medeiros da Silva'),
(2, 'Vitoria Gabriele de Lima Moreira');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_sistema`
--

CREATE TABLE `logs_sistema` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci,
  `tabela_afetada` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `data_hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `logs_sistema`
--

INSERT INTO `logs_sistema` (`id`, `usuario_id`, `acao`, `descricao`, `tabela_afetada`, `registro_id`, `ip`, `user_agent`, `data_hora`) VALUES
(1, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 17:54:28'),
(2, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 17:54:44'),
(3, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 17:55:01'),
(4, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 17:55:18'),
(5, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:01:11'),
(6, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:01:30'),
(7, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:02:07'),
(8, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:02:21'),
(9, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:02:51'),
(10, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:03:16'),
(11, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:07:31'),
(12, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:07:51'),
(13, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:08:37'),
(14, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:09:00'),
(15, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:09:11'),
(16, NULL, 'MIGRAÇÃO', 'Migração simplificada dos dados executada com sucesso', 'SISTEMA', NULL, NULL, NULL, '2025-05-31 18:09:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_status_aluno`
--

CREATE TABLE `log_status_aluno` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `status_novo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_acao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `modalidades`
--

INSERT INTO `modalidades` (`id`, `nome`) VALUES
(1, 'Presencial'),
(2, 'On-line');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `nome` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carga_horaria` int(11) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `modulos`
--

INSERT INTO `modulos` (`id`, `curso_id`, `nome`, `carga_horaria`, `ordem`) VALUES
(1, 13, 'Introdução à Informática', 16, 1),
(2, 13, 'Windows 11', 16, 2),
(3, 13, 'Word 2021', 16, 3),
(4, 13, 'PowerPoint 2021', 16, 4),
(5, 13, 'Excel 2021', 16, 5),
(6, 13, 'Excel 2021 Avançado - Módulo I', 16, 6),
(7, 13, 'Internet e Outlook', 16, 7),
(8, 8, 'CorelDraw Graphics Suite - Módulo I', 12, 1),
(9, 8, 'CorelDraw Graphics Suite - Módulo II', 16, 2),
(10, 8, 'Illustrator', 16, 3),
(11, 8, 'Photoshop I', 12, 4),
(12, 8, 'Photoshop II', 16, 5),
(13, 8, 'Photoshop III', 16, 6),
(14, 8, 'After Effects', 16, 7),
(15, 8, 'Premiere', 16, 8),
(16, 8, 'XD', 10, 9),
(17, 8, 'Comunicação e Oratória', 16, 10),
(18, 8, 'Inovação e Criatividade', 16, 11),
(19, 4, 'After Effects', 16, 1),
(20, 4, 'Illustrator', 16, 2),
(21, 4, 'Photoshop I', 12, 3),
(22, 4, 'Angular', 12, 4),
(23, 4, 'Cordova', 11, 5),
(24, 4, 'Ionic', 17, 6),
(25, 4, 'Desenvolvimento de Games 2D e 3D', 16, 7),
(26, 4, 'Lógica de Programação', 12, 8),
(27, 2, 'Banco de Dados SQL', 16, 1),
(28, 2, 'Educação Financeira', 16, 2),
(29, 2, 'Excel 2021', 16, 3),
(30, 2, 'Excel 2021 Avançado - Módulo I', 16, 4),
(31, 2, 'Excel 2021 Avançado - Módulo II', 16, 5),
(32, 2, 'Inteligência Emocional', 16, 6),
(33, 2, 'Lógica de Programação', 12, 7),
(34, 2, 'Operador de Caixa', 16, 8),
(35, 2, 'Power BI', 16, 9),
(36, 2, 'Programação C# - Módulo I', 16, 10),
(37, 3, 'After Effects', 16, 1),
(38, 3, 'Illustrator', 16, 2),
(39, 3, 'Photoshop I', 12, 3),
(40, 3, 'Photoshop II', 16, 4),
(41, 3, 'Photoshop III', 16, 5),
(42, 3, 'Premiere', 16, 6),
(43, 3, 'Assistente de Propaganda e Marketing', 16, 7),
(44, 3, 'Comunicação e Oratória', 16, 8),
(45, 3, 'Google Adwords', 16, 9),
(46, 3, 'Marketing Digital - Módulo I', 16, 10),
(47, 3, 'Marketing Digital - Módulo II', 16, 11),
(48, 5, 'Atendente de Farmácia I', 16, 1),
(49, 5, 'Atendente de Farmácia II', 16, 2),
(50, 5, 'Atendimento ao Cliente', 16, 3),
(51, 5, 'Auxiliar Médico', 16, 4),
(52, 5, 'Auxiliar Odontológico - Saúde Bucal', 16, 5),
(53, 5, 'Cuidador de Idosos', 16, 6),
(54, 5, 'Empreendedorismo', 16, 7),
(55, 5, 'Matemática Financeira', 16, 8),
(56, 5, 'Operador de Caixa', 16, 9),
(57, 5, 'Recepcionista de Serviços da Saúde', 16, 10),
(58, 6, 'Criatividade no Ambiente de Trabalho', 16, 1),
(59, 6, 'Comunicação e Oratória', 16, 2),
(60, 6, 'Desenvolvimento Profissional', 16, 3),
(61, 6, 'Dez Maneiras de Vender uma Ideia', 16, 4),
(62, 6, 'Educação Financeira', 16, 5),
(63, 6, 'Empreendedorismo', 16, 6),
(64, 6, 'Estratégias para Conflitos', 16, 7),
(65, 6, 'Excelência no Atendimento', 16, 8),
(66, 6, 'Inovação e Criatividade', 16, 9),
(67, 6, 'Inteligência Emocional', 16, 10),
(68, 6, 'Liderança', 16, 11),
(69, 6, 'Marketing Pessoal', 16, 12),
(70, 6, 'Negociação e Influência', 16, 13),
(71, 6, 'Produtividade e Gestão do Tempo', 16, 14),
(72, 6, 'Trabalho em Equipe', 16, 15),
(73, 1, 'After Effects', 16, 1),
(74, 1, 'Illustrator', 16, 2),
(75, 1, 'Photoshop I', 12, 3),
(76, 1, 'Photoshop II', 16, 4),
(77, 1, 'Photoshop III', 16, 5),
(78, 1, 'Premiere', 16, 6),
(79, 1, 'XD', 10, 7),
(80, 1, 'Desenvolvimento Web - HTML', 16, 8),
(81, 1, 'Desenvolvimento Web - CSS', 16, 9),
(82, 1, 'Desenvolvimento Web - JavaScript', 16, 10),
(83, 1, 'Desenvolvimento Web - WordPress', 16, 11),
(84, 1, 'Inovação e Criatividade', 16, 12),
(85, 9, 'Banco de Dados SQL', 16, 1),
(86, 9, 'Aplicativos Mobile Cordova', 11, 2),
(87, 9, 'Lógica de Programação', 12, 3),
(88, 9, 'Montagem e Manutenção de Computadores', 16, 4),
(89, 9, 'Programação C# - Módulo I', 16, 5),
(90, 9, 'Programação C# - Módulo II', 16, 6),
(91, 9, 'Programação C# - Módulo III', 16, 7),
(92, 9, 'Programação C# - Módulo IV', 16, 8),
(93, 9, 'Redes - Cabeamento e Infraestrutura', 16, 9),
(94, 9, 'Redes - Tecnologias Wireless', 16, 10),
(95, 12, 'Programação C# - Módulo I', 16, 1),
(96, 12, 'Programação C# - Módulo II', 16, 2),
(97, 12, 'Programação C# - Módulo III', 16, 3),
(98, 12, 'Programação C# - Módulo IV', 16, 4),
(99, 12, 'Banco de Dados SQL', 16, 5),
(100, 12, 'Lógica de Programação', 12, 6),
(101, 12, 'XD', 10, 7),
(102, 12, 'Angular', 12, 8),
(103, 12, 'Cordova', 11, 9),
(104, 12, 'Ionic', 17, 10),
(105, 12, 'Power BI', 16, 11),
(106, 10, 'Lógica de Programação', 12, 1),
(107, 10, 'Robótica I', 16, 2),
(108, 10, 'Robótica II', 16, 3),
(109, 10, 'Robótica Mio I', 16, 4),
(110, 10, 'Robótica Mio II', 16, 5),
(111, 11, 'Assistente Administrativo e Rotinas Administrativas - Módulo I', 16, 1),
(112, 11, 'Assistente Administrativo e Rotinas Administrativas - Módulo II', 16, 2),
(113, 11, 'Assistente de Administração Financeira', 16, 3),
(114, 11, 'Assistente de Departamento Pessoal', 16, 4),
(115, 11, 'Assistente de Escrita Fiscal e Contabilidade', 16, 5),
(116, 11, 'Assistente de Recursos Humanos', 16, 6),
(117, 11, 'Compras e Estoque', 16, 7),
(118, 11, 'Custos', 16, 8),
(119, 11, 'Gestão de Pessoas', 16, 9),
(120, 11, 'Secretariado', 16, 10),
(121, 7, 'Annual Book 2 (Iniciantes)', 32, 1),
(122, 7, 'Annual Book 4 (Intermediate 1)', 32, 2),
(123, 7, 'Annual Book 6 (Intermediate 2)', 32, 3),
(124, 7, 'Annual Book 8 (Advanced)', 32, 4),
(125, 7, 'Annual Book 9 (Professional)', 32, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `periodos`
--

CREATE TABLE `periodos` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `periodos`
--

INSERT INTO `periodos` (`id`, `nome`) VALUES
(1, 'Manhã'),
(2, 'Tarde'),
(3, 'Noite'),
(4, '08:00 às 10:00'),
(5, '10:00 às 12:00'),
(6, '13:30 às 15:30'),
(7, '15:30 às 17:30'),
(8, '17:30 às 19:30'),
(9, '19:30 às 21:30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `presencas`
--

CREATE TABLE `presencas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `turma_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `presente` tinyint(1) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `presencas`
--

INSERT INTO `presencas` (`id`, `aluno_id`, `turma_id`, `data`, `presente`, `criado_em`) VALUES
(1, 42, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(2, 48, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(3, 64, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(4, 73, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(5, 106, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(6, 111, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(7, 144, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(8, 166, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(9, 168, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(10, 796, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(11, 264, 29, '2025-06-12', 1, '2025-06-12 14:48:27'),
(12, 8, 30, '2025-06-12', 1, '2025-06-12 14:57:07'),
(13, 9, 30, '2025-06-12', 0, '2025-06-12 14:57:07'),
(14, 34, 30, '2025-06-12', 1, '2025-06-12 14:57:07'),
(15, 66, 30, '2025-06-12', 0, '2025-06-12 14:57:07'),
(16, 84, 30, '2025-06-12', 1, '2025-06-12 14:57:07'),
(17, 189, 30, '2025-06-12', 0, '2025-06-12 14:57:07'),
(18, 233, 34, '2025-06-12', 1, '2025-06-12 15:00:33'),
(19, 117, 52, '2025-06-12', 1, '2025-06-12 20:55:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas`
--

CREATE TABLE `tarefas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `status` enum('afazer','progresso','concluido') COLLATE utf8mb4_unicode_ci DEFAULT 'afazer',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_evento` date DEFAULT NULL,
  `prioridade` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etiqueta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tarefas`
--

INSERT INTO `tarefas` (`id`, `usuario_id`, `titulo`, `descricao`, `status`, `data_criacao`, `data_evento`, `prioridade`, `etiqueta`) VALUES
(4, 1, 'Verificar Erros no Pedido de Apostila', 'Verificar Erros no Pedido de Apostila', 'concluido', '2025-06-10 23:47:54', '2025-06-11', 'alta', 'Coordenação');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas_tratativas`
--

CREATE TABLE `tarefas_tratativas` (
  `id` int(11) NOT NULL,
  `tarefa_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `status_anterior` enum('afazer','progresso','concluido') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_novo` enum('afazer','progresso','concluido') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tratativa` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_tratativa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tarefas_tratativas`
--

INSERT INTO `tarefas_tratativas` (`id`, `tarefa_id`, `usuario_id`, `status_anterior`, `status_novo`, `tratativa`, `data_tratativa`) VALUES
(1, 4, 1, 'afazer', 'progresso', 'teste de tratativa', '2025-06-11 02:01:41'),
(2, 4, 1, 'progresso', 'afazer', 'voltando', '2025-06-11 02:03:08'),
(3, 4, 1, 'afazer', 'progresso', 'tste', '2025-06-11 02:07:58'),
(4, 4, 1, 'progresso', 'concluido', 'teste', '2025-06-11 02:14:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas_usuarios`
--

CREATE TABLE `tarefas_usuarios` (
  `tarefa_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_aluno`
--

CREATE TABLE `tipos_aluno` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `tipos_aluno`
--

INSERT INTO `tipos_aluno` (`id`, `nome`) VALUES
(1, 'PARCELADO'),
(2, 'BOLSISTA'),
(3, 'ACORDO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_pgto`
--

CREATE TABLE `tipos_pgto` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `tipos_pgto`
--

INSERT INTO `tipos_pgto` (`id`, `nome`) VALUES
(1, 'Parcelado mensal'),
(2, 'VIP');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_turma`
--

CREATE TABLE `tipos_turma` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `tipos_turma`
--

INSERT INTO `tipos_turma` (`id`, `nome`) VALUES
(1, 'DINÂMICO'),
(2, 'MULTIMÍDIA');

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `instrutor_id` int(11) DEFAULT NULL,
  `vagas` int(11) DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dia_semana_id` int(11) DEFAULT NULL,
  `periodo_id` int(11) DEFAULT NULL,
  `tipo_id` int(11) DEFAULT NULL,
  `dia_semana` int(11) DEFAULT NULL,
  `periodo` int(11) DEFAULT NULL,
  `ultima_presenca_em` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id`, `nome`, `instrutor_id`, `vagas`, `status`, `dia_semana_id`, `periodo_id`, `tipo_id`, `dia_semana`, `periodo`, `ultima_presenca_em`) VALUES
(29, 'Seg - 10:00 às 12:00', 1, 15, 'ativa', 1, 1, 1, NULL, NULL, '2025-06-12'),
(30, 'Seg - 15:30 às 17:30', 2, 20, 'ativa', 1, 2, 1, NULL, NULL, '2025-06-12'),
(31, 'Seg - 17:30 às 19:30', 2, 15, 'ativa', 1, 3, 1, NULL, NULL, NULL),
(32, 'Seg - 19:30 às 21:30', 2, 15, 'ativa', 1, 3, 1, NULL, NULL, NULL),
(33, 'Ter - 10:00 às 12:00', 1, 15, 'ativa', 2, 1, 1, NULL, NULL, NULL),
(34, 'Ter - 15:30 às 17:30', 2, 20, 'ativa', 2, 2, 1, NULL, NULL, '2025-06-12'),
(35, 'Ter - 19:30 às 21:30', 2, 16, 'ativa', 2, 3, 1, NULL, NULL, NULL),
(36, 'Qua - 08:00 às 10:00', 1, 16, 'ativa', 3, 1, 1, NULL, NULL, NULL),
(37, 'Qua - 13:30 às 15:30', 2, 20, 'ativa', 3, 2, 1, NULL, NULL, NULL),
(38, 'Qua - 15:30 às 17:30', 2, 20, 'ativa', 3, 2, 1, NULL, NULL, NULL),
(39, 'Qua - 19:30 às 21:30', 2, 15, 'ativa', 3, 3, 1, NULL, NULL, NULL),
(40, 'Qui - 10:00 às 12:00', 1, 15, 'ativa', 4, 1, 1, NULL, NULL, NULL),
(41, 'Qui - 15:30 às 17:30', 2, 20, 'ativa', 4, 2, 1, NULL, NULL, NULL),
(42, 'Qui - 17:30 às 19:30', 2, 15, 'ativa', 4, 3, 1, NULL, NULL, NULL),
(43, 'ONLINE t2', 2, 15, 'ativa', 1, 1, 1, NULL, NULL, NULL),
(44, 'Sab - 08:00 às 10:00', 2, 20, 'ativa', 6, 1, 1, NULL, NULL, NULL),
(45, 'Sab - 10:00 às 12:00', 2, 20, 'ativa', 6, 1, 1, NULL, NULL, NULL),
(46, 'Sab - 10:00 às 12:00 t2', 2, 6, 'ativa', 6, 1, 1, NULL, NULL, NULL),
(47, 'Sab - 13:00 às 15:00', 2, 20, 'ativa', 6, 2, 1, NULL, NULL, NULL),
(48, 'Sab - 15:00 às 17:00', 2, 20, 'ativa', 6, 2, 1, NULL, NULL, NULL),
(49, 'Sab - 15:00 às 17:00 t2', 2, 5, 'ativa', 6, 2, 1, NULL, NULL, NULL),
(50, 'ONLINE -', 1, 20, 'ativa', 1, 1, 1, NULL, NULL, NULL),
(52, 'ING - SEG - 19:00 às 21:00', 1, 2, 'ativa', 1, 3, 2, NULL, NULL, '2025-06-12'),
(53, 'ING - TER - 15:30 às 17:30', 1, 6, 'ativa', 2, 2, 2, NULL, NULL, NULL),
(55, 'ING - SÁB - 08:00 às 10:00', 1, 10, 'ativa', 6, 1, 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `user` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `passa` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cargo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `tentativas_login` int(11) DEFAULT '0',
  `bloqueado_ate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `user`, `passa`, `nome`, `cargo`, `ativo`, `ultimo_login`, `tentativas_login`, `bloqueado_ate`) VALUES
(1, 'admin', '$2b$12$xQ6xdMwkWUV/VAUuPIkOcuOQ9i76kbWxslQtWOxID.eiFb05vOORa', 'Administrador', 'admin', 1, NULL, 0, NULL),
(3, 'sara', '$2y$10$4gWDvZnsjcKUvM8kEIBcgO7bZEVXjGf9wYHqicJSt0b5iMdd82iJiC', 'Sara', 'usuario', 1, NULL, 0, NULL),
(4, 'vitoria', '$2y$10$XQFRt0hB/GjE6sUBtX3A2eB0yVhURcfLEF7NsNSoIFuSBbi6kwq8q', 'Vitoria', 'usuario', 1, NULL, 0, NULL),
(5, 'daniel', '$2y$10$M5bq5nT9nMoEMzYdF2K2AON7u4mWx/PrjxI5Gu6EFqkZzIMyyEw4G', 'Daniel', 'usuario', 1, NULL, 0, NULL),
(6, 'lucas', '$2y$10$w.5kp2uJchM6VGClQXYh2OCFXSeKn4j6v8pym39QjaZtZKp3plGBe', 'Lucas', 'usuario', 1, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_estatisticas_gerais`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_estatisticas_gerais` (
`total_turmas` bigint(21)
,`total_alunos_cadastrados` bigint(21)
,`total_alunos_ativos` bigint(21)
,`total_alunos_trancados` bigint(21)
,`total_vagas` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_estatisticas_gerais`
--
DROP TABLE IF EXISTS `vw_estatisticas_gerais`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_estatisticas_gerais`  AS SELECT (select count(0) from `turmas` where (`turmas`.`status` = 'ativa')) AS `total_turmas`, (select count(0) from `alunos`) AS `total_alunos_cadastrados`, (select count(distinct `alunos_turmas`.`aluno_id`) from `alunos_turmas` where (`alunos_turmas`.`ativo` = 1)) AS `total_alunos_ativos`, (select count(distinct `alunos_turmas`.`aluno_id`) from `alunos_turmas` where (`alunos_turmas`.`ativo` = 0)) AS `total_alunos_trancados`, (select sum(`turmas`.`vagas`) from `turmas` where (`turmas`.`status` = 'ativa')) AS `total_vagas` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_codigo_original` (`codigo_original`);

--
-- Índices de tabela `alunos_turmas`
--
ALTER TABLE `alunos_turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_aluno_id` (`aluno_id`),
  ADD KEY `idx_turma_id` (`turma_id`);

--
-- Índices de tabela `contrato`
--
ALTER TABLE `contrato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aluno` (`id_aluno`),
  ADD KEY `tipo_pgto_id` (`tipo_pgto_id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `modalidade_id` (`modalidade_id`);

--
-- Índices de tabela `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contratos_turma` (`turma_id`),
  ADD KEY `fk_contratos_curso` (`curso_id`);

--
-- Índices de tabela `contratos_cursos`
--
ALTER TABLE `contratos_cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_id` (`contrato_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `dias_semana`
--
ALTER TABLE `dias_semana`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `faq_meraki`
--
ALTER TABLE `faq_meraki`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `feriados`
--
ALTER TABLE `feriados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `instrutores`
--
ALTER TABLE `instrutores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `log_status_aluno`
--
ALTER TABLE `log_status_aluno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices de tabela `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `presencas`
--
ALTER TABLE `presencas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aluno_data_turma` (`aluno_id`,`turma_id`,`data`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `tarefas`
--
ALTER TABLE `tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `tarefas_tratativas`
--
ALTER TABLE `tarefas_tratativas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarefa_id` (`tarefa_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `tarefas_usuarios`
--
ALTER TABLE `tarefas_usuarios`
  ADD PRIMARY KEY (`tarefa_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `tipos_aluno`
--
ALTER TABLE `tipos_aluno`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipos_pgto`
--
ALTER TABLE `tipos_pgto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipos_turma`
--
ALTER TABLE `tipos_turma`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dia_semana` (`dia_semana_id`),
  ADD KEY `fk_periodo` (`periodo_id`),
  ADD KEY `fk_instrutor` (`instrutor_id`),
  ADD KEY `fk_tipo_turma` (`tipo_id`),
  ADD KEY `dia_semana` (`dia_semana`),
  ADD KEY `periodo` (`periodo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=797;

--
-- AUTO_INCREMENT de tabela `alunos_turmas`
--
ALTER TABLE `alunos_turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=867;

--
-- AUTO_INCREMENT de tabela `contrato`
--
ALTER TABLE `contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contratos`
--
ALTER TABLE `contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `contratos_cursos`
--
ALTER TABLE `contratos_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `dias_semana`
--
ALTER TABLE `dias_semana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `faq_meraki`
--
ALTER TABLE `faq_meraki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `feriados`
--
ALTER TABLE `feriados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT de tabela `instrutores`
--
ALTER TABLE `instrutores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `log_status_aluno`
--
ALTER TABLE `log_status_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de tabela `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `presencas`
--
ALTER TABLE `presencas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tarefas_tratativas`
--
ALTER TABLE `tarefas_tratativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tipos_aluno`
--
ALTER TABLE `tipos_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tipos_pgto`
--
ALTER TABLE `tipos_pgto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tipos_turma`
--
ALTER TABLE `tipos_turma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos_turmas`
--
ALTER TABLE `alunos_turmas`
  ADD CONSTRAINT `alunos_turmas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `alunos_turmas_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `contrato`
--
ALTER TABLE `contrato`
  ADD CONSTRAINT `contrato_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `contrato_ibfk_2` FOREIGN KEY (`tipo_pgto_id`) REFERENCES `tipos_pgto` (`id`),
  ADD CONSTRAINT `contrato_ibfk_3` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `contrato_ibfk_4` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidades` (`id`);

--
-- Restrições para tabelas `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `fk_contratos_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `fk_contratos_turma` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `contratos_cursos`
--
ALTER TABLE `contratos_cursos`
  ADD CONSTRAINT `contratos_cursos_ibfk_1` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`),
  ADD CONSTRAINT `contratos_cursos_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Restrições para tabelas `log_status_aluno`
--
ALTER TABLE `log_status_aluno`
  ADD CONSTRAINT `log_status_aluno_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `log_status_aluno_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Restrições para tabelas `presencas`
--
ALTER TABLE `presencas`
  ADD CONSTRAINT `presencas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presencas_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tarefas`
--
ALTER TABLE `tarefas`
  ADD CONSTRAINT `tarefas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tarefas_tratativas`
--
ALTER TABLE `tarefas_tratativas`
  ADD CONSTRAINT `tarefas_tratativas_ibfk_1` FOREIGN KEY (`tarefa_id`) REFERENCES `tarefas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarefas_tratativas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tarefas_usuarios`
--
ALTER TABLE `tarefas_usuarios`
  ADD CONSTRAINT `tarefas_usuarios_ibfk_1` FOREIGN KEY (`tarefa_id`) REFERENCES `tarefas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarefas_usuarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `fk_dia_semana` FOREIGN KEY (`dia_semana_id`) REFERENCES `dias_semana` (`id`),
  ADD CONSTRAINT `fk_instrutor` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`),
  ADD CONSTRAINT `fk_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`id`),
  ADD CONSTRAINT `fk_tipo_turma` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_turma` (`id`),
  ADD CONSTRAINT `turmas_ibfk_1` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`),
  ADD CONSTRAINT `turmas_ibfk_2` FOREIGN KEY (`dia_semana`) REFERENCES `dias_semana` (`id`),
  ADD CONSTRAINT `turmas_ibfk_3` FOREIGN KEY (`periodo`) REFERENCES `periodos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
