-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 16/06/2026 às 01:46
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atendelab`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('aberto','em_andamento','concluido') DEFAULT 'aberto',
  `data_atendimento` date NOT NULL,
  `horario_atendimento` time NOT NULL,
  `observacao_final` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `descricao`, `status`, `data_atendimento`, `horario_atendimento`, `observacao_final`, `criado_em`, `atualizado_em`) VALUES
(21, 1, 1, 1, 'Duvida sobre criterios de avaliacao da disciplina de Calculo.', 'concluido', '2026-06-01', '09:00:00', 'Criterios explicados e material de apoio enviado.', '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(22, 2, 2, 1, 'Orientacao sobre estrutura do TCC e prazos de entrega.', 'concluido', '2026-06-02', '10:30:00', 'Aluna orientada sobre normas ABNT e cronograma.', '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(23, 3, 3, 1, 'Problema de acesso ao sistema academico apos troca de senha.', 'concluido', '2026-06-03', '14:00:00', 'Senha redefinida e acesso restaurado.', '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(24, 4, 4, 1, 'Solicitacao de declaracao de matricula para estagio.', 'concluido', '2026-06-04', '08:30:00', 'Declaracao emitida e entregue ao aluno.', '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(25, 5, 5, 1, 'Agendamento do laboratorio de redes para apresentacao de projeto.', 'concluido', '2026-06-05', '11:00:00', 'Laboratorio reservado para o dia 15/06 das 14h as 16h.', '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(26, 1, 2, 1, 'Revisao de nota em trabalho da disciplina de Engenharia de Software.', 'em_andamento', '2026-06-09', '10:00:00', NULL, '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(27, 2, 1, 1, 'Duvida sobre filtros por status em listagem de atendimentos.', 'em_andamento', '2026-06-10', '11:30:00', NULL, '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(28, 3, 4, 1, 'Solicitacao de historico escolar para processo seletivo.', 'aberto', '2026-06-11', '08:00:00', NULL, '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(29, 4, 5, 1, 'Reserva do laboratorio de quimica para aula experimental.', 'aberto', '2026-06-12', '09:00:00', NULL, '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(30, 5, 2, 1, 'Orientacao sobre documentacao academica para intercambio.', 'aberto', '2026-06-13', '14:00:00', NULL, '2026-06-15 23:40:34', '2026-06-15 23:40:34'),
(31, 1, 1, 1, 'Duvida sobre atividade avaliativa.', 'aberto', '2026-06-15', '14:30:00', NULL, '2026-06-15 23:42:01', '2026-06-15 23:42:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `documento` varchar(30) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `curso` varchar(120) DEFAULT NULL,
  `periodo` varchar(20) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `documento`, `telefone`, `email`, `curso`, `periodo`, `observacoes`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Joao da Silva Atualizado', '123.456.789-00', '(47) 99999-9999', 'joao.silva@exemplo.com', 'Engenharia de Software', '6o', '', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:33:25'),
(2, 'Ana Carolina', '987.654.321-00', '(47) 99999-0002', 'ana.carolina@exemplo.com', 'Sistemas de Informacao', '7o', NULL, 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:03:26'),
(3, 'Maria de Souza', '111.222.333-44', '(47) 99999-1234', 'maria.souza@exemplo.com', 'Engenharia de Software', '5o', 'Cadastro de teste', 'ativo', '2026-06-15 23:32:54', '2026-06-15 23:32:54'),
(4, 'Carlos Henrique Souza', '321.654.987-10', '(47) 99999-0010', 'carlos.souza@exemplo.com', 'Engenharia de Software', '3o', NULL, 'ativo', '2026-06-15 23:40:26', '2026-06-15 23:40:26'),
(5, 'Mariana Oliveira Costa', '741.852.963-20', '(47) 99999-0011', 'mariana.oliveira@exemplo.com', 'Sistemas de Informacao', '5o', NULL, 'ativo', '2026-06-15 23:40:26', '2026-06-15 23:40:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Duvida academica', 'Duvidas sobre disciplinas e atividades.', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:37:14'),
(2, 'Orientacao de atividade', 'Orientacoes sobre trabalhos, TCC e projetos.', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:03:26'),
(3, 'Suporte tecnico', 'Problemas com sistemas, equipamentos e acessos.', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:03:26'),
(4, 'Matricula e documentacao', 'Solicitacoes de matricula, declaracoes e historicos.', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:03:26'),
(5, 'Acesso ao laboratorio', 'Liberacao de uso e agendamento de laboratorios.', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:03:26'),
(6, 'Orientacao de projeto', 'Orientacoes academicas sobre projetos integradores.', 'inativo', '2026-06-15 23:35:50', '2026-06-15 23:37:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','atendente') DEFAULT 'atendente',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, 'Administrador', 'admin@atendelab.com', '$2y$10$aELjJUxpBhMQf9J4.PtV6.KrlkvqjIl4.jtDH3Z.gdR9Uy8MoYlyG', 'admin', 'ativo', '2026-06-15 23:03:26', '2026-06-15 23:26:25');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atendimentos_pessoa` (`pessoa_id`),
  ADD KEY `fk_atendimentos_tipo` (`tipo_atendimento_id`),
  ADD KEY `fk_atendimentos_usuario` (`usuario_id`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- Índices de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `fk_atendimentos_pessoa` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  ADD CONSTRAINT `fk_atendimentos_tipo` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`),
  ADD CONSTRAINT `fk_atendimentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
