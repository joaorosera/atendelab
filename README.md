# AtendeLab

Sistema de Controle de Atendimentos Academicos, trabalho da disciplina de Fabrica
de Software.

A ideia é simular um sistema bem simples de atendimento academico: cadastra as
pessoas atendidas, os tipos de atendimento e os registros de cada atendimento
feito.

## Tecnologias usadas

- PHP 8.x (sem framework, MVC na mao mesmo)
- PDO + MySQL
- phpMyAdmin (pra importar o banco)
- Bootstrap 5 (via CDN)
- JS puro (fetch) pra falar com o backend

## Funcionalidades

- Login (com sessao)
- Dashboard com numeros gerais
- Cadastro de pessoas atendidas (criar, editar, inativar)
- Cadastro de tipos de atendimento (criar, editar, inativar)
- Registro de atendimentos com status (aberto / em andamento / concluido)

## Como rodar local (XAMPP)

1. Clonar o repositorio.
2. Colocar a pasta dentro do htdocs do XAMPP (ex: `htdocs/atendelab`).
3. Ligar o Apache e o MySQL pelo painel do XAMPP.
4. Criar o banco `atendelab` no phpMyAdmin.
5. Importar o `database/atendelab.sql`.
6. Acessar `http://localhost/atendelab/public/`.

Login padrao depois de importar o banco: `admin@atendelab.com`. A senha
ta com hash no banco de dados, se precisar resetar dá pra gerar um hash novo
rodando o `gerar-hash.php` e atualizando direto na tabela `usuarios`.

## Observacoes

- A porta do MySQL no `config/database.php` ta como 3307, porque no meu XAMPP
  a 3306 ja estava em uso por outra coisa. Se der erro de conexao, é o primeiro
  lugar pra olhar.
- Os arquivos `teste-*.php` dentro de `public/` foram usados só pra debugar
  conexao/sessao durante o desenvolvimento.
