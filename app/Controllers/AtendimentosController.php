<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json($dados, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    // lista com join pra trazer nome da pessoa, do tipo e do responsavel direto
    public function listar()
    {
        $sql = 'SELECT
                    a.id,
                    p.nome AS pessoa_nome,
                    t.nome AS tipo_nome,
                    u.nome AS responsavel_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.observacao_final
                FROM atendimentos a
                INNER JOIN pessoas p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.id DESC';

        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscar()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID invalido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT a.*, p.nome AS pessoa_nome, t.nome AS tipo_nome, u.nome AS responsavel_nome
             FROM atendimentos a
             INNER JOIN pessoas p ON p.id = a.pessoa_id
             INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             INNER JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento nao encontrado.'], 404);
            return;
        }

        $this->json($atendimento);
    }

    // pega o id de quem ta logado pra salvar como responsavel do atendimento.
    // deixei um fallback pegando usuario_id do POST pra poder testar pelo Postman
    // sem precisar simular sessao toda vez
    private function usuarioResponsavel()
    {
        if (isset($_SESSION['usuario']['id'])) {
            return (int) $_SESSION['usuario']['id'];
        }

        $id = filter_var($_POST['usuario_id'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            return $id;
        }

        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['erro' => 'Usuário não autenticado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function criar()
    {
        $pessoaId = filter_var($_POST['pessoa_id'] ?? null, FILTER_VALIDATE_INT);
        $tipoId = filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT);
        $usuarioId = $this->usuarioResponsavel();
        $descricao = trim($_POST['descricao'] ?? '');
        $data = $_POST['data_atendimento'] ?? '';
        $horario = $_POST['horario_atendimento'] ?? '';
        $status = $_POST['status'] ?? 'aberto';

        if (!$pessoaId || !$tipoId || $descricao === '' || $data === '' || $horario === '') {
            $this->json(['erro' => 'Preencha os campos obrigatorios.'], 422);
            return;
        }

        // ao criar só pode comecar aberto ou em_andamento, concluido só depois via alterarStatus
        if ($status != 'aberto' && $status != 'em_andamento') {
            $this->json(['erro' => 'Status inicial invalido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO atendimentos
             (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento)
             VALUES
             (:pessoa_id, :tipo_id, :usuario_id, :descricao, :status, :data_atendimento, :horario_atendimento)'
        );
        $stmt->execute([
            'pessoa_id' => $pessoaId,
            'tipo_id' => $tipoId,
            'usuario_id' => $usuarioId,
            'descricao' => $descricao,
            'status' => $status,
            'data_atendimento' => $data,
            'horario_atendimento' => $horario,
        ]);

        $this->json([
            'mensagem' => 'Atendimento registrado com sucesso.',
            'id' => $this->pdo->lastInsertId(),
        ], 201);
    }

    public function alterarStatus()
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao = trim($_POST['observacao_final'] ?? '');

        $statusValidos = ['aberto', 'em_andamento', 'concluido'];
        if (!$id || !in_array($status, $statusValidos)) {
            $this->json(['erro' => 'ID ou status invalido.'], 422);
            return;
        }

        // pra concluir tem que justificar o que foi feito
        if ($status === 'concluido' && $observacao === '') {
            $this->json(['erro' => 'Informe a observacao final para concluir.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare('UPDATE atendimentos SET status = :status, observacao_final = :observacao WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'observacao' => $observacao !== '' ? $observacao : null,
        ]);

        $this->json(['mensagem' => 'Status atualizado com sucesso.']);
    }
}
