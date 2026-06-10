<?php
// Controller da entidade pessoas.
// Em uma arquitetura MVC, ele recebe a requisição, valida dados e acessa o banco.

class PessoasController
{
    // Conexão PDO reutilizada em todos os métodos.
    private PDO $pdo;

    public function __construct()
    {
        // Importa o arquivo que inicializa o objeto $pdo.
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Consulta todas as pessoas com ordenação decrescente por ID.
        $sql = 'SELECT id, nome, email, telefone, tipo, status, criado_em
                FROM pessoas
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Lê e valida o ID recebido por GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        // Consulta parametrizada evita SQL Injection.
        $sql = 'SELECT id, nome, email, telefone, tipo, status, criado_em
                FROM pessoas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            return;
        }

        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Coleta dados do formulário (POST).
        $nome     = trim($_POST['nome']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $tipo     = $_POST['tipo']          ?? 'aluno';
        $status   = $_POST['status']        ?? 'ativo';

        // Regras mínimas de validação de entrada.
        if ($nome === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome e e-mail são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        // Whitelist de valores válidos para o campo tipo.
        if (!in_array($tipo, ['aluno', 'professor', 'funcionario'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Tipo inválido. Use: aluno, professor ou funcionario.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, email, telefone, tipo, status)
                    VALUES (:nome, :email, :telefone, :tipo, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome',     $nome);
            $stmt->bindValue(':email',    $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':tipo',     $tipo);
            $stmt->bindValue(':status',   $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // ID vem no POST para operação de update.
        $id       = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome     = trim($_POST['nome']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $tipo     = $_POST['tipo']          ?? 'aluno';
        $status   = $_POST['status']        ?? 'ativo';

        if (!$id || $nome === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome e e-mail são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        if (!in_array($tipo, ['aluno', 'professor', 'funcionario'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Tipo inválido. Use: aluno, professor ou funcionario.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas
                    SET nome     = :nome,
                        email    = :email,
                        telefone = :telefone,
                        tipo     = :tipo,
                        status   = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome',     $nome);
            $stmt->bindValue(':email',    $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':tipo',     $tipo);
            $stmt->bindValue(':status',   $status);
            $stmt->bindValue(':id',       $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar pessoa.']);
        }
    }

    // Exclusão lógica: altera status para 'inativo' em vez de deletar o registro.
    // Evita problemas com chaves estrangeiras em atendimentos vinculados.
    public function inativar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql  = 'UPDATE pessoas SET status = :status WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', 'inativo');
            $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Pessoa inativada com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar pessoa.']);
        }
    }
}