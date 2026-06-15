<?php
class PessoasController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // -------------------------------------------------------
    // LISTAR
    // -------------------------------------------------------
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id, nome, documento, telefone, email,
                       curso, periodo, status, observacoes, criado_em
                FROM pessoas
                ORDER BY nome';

        $stmt   = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // -------------------------------------------------------
    // BUSCAR POR ID
    // -------------------------------------------------------
    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID invalido.']);
            return;
        }

        $sql = 'SELECT id, nome, documento, telefone, email,
                       curso, periodo, status, observacoes, criado_em
                FROM pessoas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa nao encontrada.']);
            return;
        }

        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // -------------------------------------------------------
    // CRIAR
    // Body (form-encode): nome, documento, email, telefone,
    //                     curso, periodo, status, observacoes
    // -------------------------------------------------------
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome        = trim($_POST['nome']        ?? '');
        $documento   = trim($_POST['documento']   ?? '');
        $email       = trim($_POST['email']       ?? '');
        $telefone    = trim($_POST['telefone']    ?? '');
        $curso       = trim($_POST['curso']       ?? '');
        $periodo     = trim($_POST['periodo']     ?? '');
        $status      =      $_POST['status']      ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');

        // Campos obrigatórios
        if ($nome === '' || $documento === '' || $email === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Nome, documento e e-mail sao obrigatorios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['erro' => 'E-mail invalido.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Status invalido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas
                        (nome, documento, telefone, email, curso, periodo, status, observacoes)
                    VALUES
                        (:nome, :documento, :telefone, :email, :curso, :periodo, :status, :observacoes)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nome'        => $nome,
                'documento'   => $documento,
                'telefone'    => $telefone    !== '' ? $telefone    : null,
                'email'       => $email,
                'curso'       => $curso       !== '' ? $curso       : null,
                'periodo'     => $periodo     !== '' ? $periodo     : null,
                'status'      => $status,
                'observacoes' => $observacoes !== '' ? $observacoes : null,
            ]);

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id'       => $this->pdo->lastInsertId(),
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            // Código 23000 = violação de UNIQUE (documento duplicado)
            if ($e->getCode() === '23000') {
                http_response_code(409);
                echo json_encode(['erro' => 'Documento ja cadastrado para outra pessoa.']);
            } else {
                http_response_code(500);
                echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
            }
        }
    }

    // -------------------------------------------------------
    // ATUALIZAR
    // Body (form-encode): id, nome, documento, email, telefone,
    //                     curso, periodo, status, observacoes
    // -------------------------------------------------------
    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome        = trim($_POST['nome']        ?? '');
        $documento   = trim($_POST['documento']   ?? '');
        $email       = trim($_POST['email']       ?? '');
        $telefone    = trim($_POST['telefone']    ?? '');
        $curso       = trim($_POST['curso']       ?? '');
        $periodo     = trim($_POST['periodo']     ?? '');
        $status      =      $_POST['status']      ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');

        if (!$id || $nome === '' || $documento === '' || $email === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Dados obrigatorios ausentes.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['erro' => 'E-mail invalido.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Status invalido.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas
                    SET nome        = :nome,
                        documento   = :documento,
                        telefone    = :telefone,
                        email       = :email,
                        curso       = :curso,
                        periodo     = :periodo,
                        status      = :status,
                        observacoes = :observacoes
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id'          => $id,
                'nome'        => $nome,
                'documento'   => $documento,
                'telefone'    => $telefone    !== '' ? $telefone    : null,
                'email'       => $email,
                'curso'       => $curso       !== '' ? $curso       : null,
                'periodo'     => $periodo     !== '' ? $periodo     : null,
                'status'      => $status,
                'observacoes' => $observacoes !== '' ? $observacoes : null,
            ]);

            echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                http_response_code(409);
                echo json_encode(['erro' => 'Documento ja cadastrado para outra pessoa.']);
            } else {
                http_response_code(500);
                echo json_encode(['erro' => 'Erro ao atualizar pessoa.']);
            }
        }
    }

    // -------------------------------------------------------
    // INATIVAR  (soft delete — não apaga o registro)
    // Body (form-encode): id
    // -------------------------------------------------------
    public function inativar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(422);
            echo json_encode(['erro' => 'ID invalido.']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE pessoas SET status = 'inativo' WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['mensagem' => 'Pessoa inativada com sucesso.'], JSON_UNESCAPED_UNICODE);
    }
}