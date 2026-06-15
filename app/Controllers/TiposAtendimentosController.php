<?php
class TiposAtendimentosController
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

        $sql = 'SELECT id, nome, descricao, status, criado_em
                FROM tipos_atendimentos
                ORDER BY nome';

        $stmt  = $this->pdo->query($sql);
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($tipos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

        $sql = 'SELECT id, nome, descricao, status, criado_em
                FROM tipos_atendimentos
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tipo) {
            http_response_code(404);
            echo json_encode(['erro' => 'Tipo de atendimento nao encontrado.']);
            return;
        }

        echo json_encode($tipo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // -------------------------------------------------------
    // CRIAR
    // Body (form-encode): nome, descricao, status
    // -------------------------------------------------------
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome      = trim($_POST['nome']      ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status    =      $_POST['status']    ?? 'ativo';

        if ($nome === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Nome e obrigatorio.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Status invalido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO tipos_atendimentos (nome, descricao, status)
                    VALUES (:nome, :descricao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'nome'      => $nome,
                'descricao' => $descricao !== '' ? $descricao : null,
                'status'    => $status,
            ]);

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Tipo de atendimento cadastrado com sucesso.',
                'id'       => $this->pdo->lastInsertId(),
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar tipo de atendimento.']);
        }
    }

    // -------------------------------------------------------
    // ATUALIZAR
    // Body (form-encode): id, nome, descricao, status
    // -------------------------------------------------------
    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id        = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome      = trim($_POST['nome']      ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status    =      $_POST['status']    ?? 'ativo';

        if (!$id || $nome === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'ID e nome sao obrigatorios.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Status invalido.']);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos
                    SET nome      = :nome,
                        descricao = :descricao,
                        status    = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id'        => $id,
                'nome'      => $nome,
                'descricao' => $descricao !== '' ? $descricao : null,
                'status'    => $status,
            ]);

            echo json_encode(['mensagem' => 'Tipo de atendimento atualizado com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar tipo de atendimento.']);
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
            "UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['mensagem' => 'Tipo de atendimento inativado com sucesso.'], JSON_UNESCAPED_UNICODE);
    }
}