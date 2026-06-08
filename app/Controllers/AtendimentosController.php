<?php
class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // LISTAR COM JOIN
    // Retorna todos os atendimentos com nome do paciente, tipo e atendente
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT
                    a.id,
                    p.nome      AS paciente_nome,
                    t.nome      AS tipo_atendimento,
                    u.nome      AS atendente_nome,
                    a.descricao,
                    a.data_atendimento,
                    a.status
                FROM atendimentos a
                JOIN pessoas            p ON a.pessoa_id           = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                JOIN usuarios           u ON a.usuario_id          = u.id
                ORDER BY a.id DESC';

        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // BUSCAR POR ID COM JOIN
    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID invalido.']);
            return;
        }

        $sql = 'SELECT
                    a.id,
                    p.nome      AS paciente_nome,
                    t.nome      AS tipo_atendimento,
                    u.nome      AS atendente_nome,
                    a.descricao,
                    a.data_atendimento,
                    a.status
                FROM atendimentos a
                JOIN pessoas            p ON a.pessoa_id           = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                JOIN usuarios           u ON a.usuario_id          = u.id
                WHERE a.id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento nao encontrado.']);
            return;
        }

        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // CRIAR
    // Body (form-encode): pessoa_id, tipo_atendimento_id, usuario_id, descricao, status
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoa_id           = filter_input(INPUT_POST, 'pessoa_id',           FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $usuario_id          = filter_input(INPUT_POST, 'usuario_id',          FILTER_VALIDATE_INT);
        $descricao           = trim($_POST['descricao'] ?? '');
        $status              =      $_POST['status']    ?? 'aberto';

        if (!$pessoa_id || !$tipo_atendimento_id || !$usuario_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'Os campos pessoa_id, tipo_atendimento_id e usuario_id sao obrigatorios e numericos.']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'finalizado'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status invalido. Use: aberto, em_andamento ou finalizado.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status)
                    VALUES (:pessoa_id, :tipo_atendimento_id, :usuario_id, :descricao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id',           $pessoa_id,           PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':usuario_id',          $usuario_id,          PDO::PARAM_INT);
            $stmt->bindValue(':descricao',           $descricao !== '' ? $descricao : null);
            $stmt->bindValue(':status',              $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento registrado com sucesso.',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar atendimento. Verifique se os IDs informados existem.']);
        }
    }

    // ATUALIZAR STATUS
    // Body (form-encode): id, status
    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status =      $_POST['status'] ?? '';

        if (!$id || $status === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e status sao obrigatorios.']);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'finalizado'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status invalido. Use: aberto, em_andamento ou finalizado.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Status do atendimento atualizado com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status do atendimento.']);
        }
    }
}