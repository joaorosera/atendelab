<?php
class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // -------------------------------------------------------
    // LISTAR COM JOIN
    // -------------------------------------------------------
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT
                    a.id,
                    p.nome  AS pessoa_nome,
                    t.nome  AS tipo_nome,
                    u.nome  AS responsavel_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.observacao_final
                FROM atendimentos a
                INNER JOIN pessoas            p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios           u ON u.id = a.usuario_id
                ORDER BY a.id DESC';

        $stmt         = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

        $sql = 'SELECT
                    a.id,
                    p.nome  AS pessoa_nome,
                    t.nome  AS tipo_nome,
                    u.nome  AS responsavel_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.observacao_final
                FROM atendimentos a
                INNER JOIN pessoas            p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios           u ON u.id = a.usuario_id
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

    // -------------------------------------------------------
    // CRIAR
    // Body (form-encode): pessoa_id, tipo_atendimento_id, usuario_id,
    //                     descricao, data_atendimento, horario_atendimento, status
    // -------------------------------------------------------
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoaId   = filter_input(INPUT_POST, 'pessoa_id',           FILTER_VALIDATE_INT);
        $tipoId     = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $usuarioId  = filter_input(INPUT_POST, 'usuario_id',          FILTER_VALIDATE_INT);
        $descricao  = trim($_POST['descricao']          ?? '');
        $data       =      $_POST['data_atendimento']   ?? '';
        $horario    =      $_POST['horario_atendimento'] ?? '';
        $status     =      $_POST['status']             ?? 'aberto';

        if (!$pessoaId || !$tipoId || !$usuarioId || $descricao === '' || $data === '' || $horario === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Preencha os campos obrigatorios: pessoa_id, tipo_atendimento_id, usuario_id, descricao, data_atendimento e horario_atendimento.']);
            return;
        }

        // Novo atendimento só pode iniciar como aberto ou em_andamento
        if (!in_array($status, ['aberto', 'em_andamento'], true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'Status inicial invalido. Use: aberto ou em_andamento.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos
                        (pessoa_id, tipo_atendimento_id, usuario_id,
                         descricao, status, data_atendimento, horario_atendimento)
                    VALUES
                        (:pessoa_id, :tipo_id, :usuario_id,
                         :descricao, :status, :data, :horario)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'pessoa_id'  => $pessoaId,
                'tipo_id'    => $tipoId,
                'usuario_id' => $usuarioId,
                'descricao'  => $descricao,
                'status'     => $status,
                'data'       => $data,
                'horario'    => $horario,
            ]);

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Atendimento registrado com sucesso.',
                'id'       => $this->pdo->lastInsertId(),
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar atendimento. Verifique se os IDs informados existem.']);
        }
    }

    // -------------------------------------------------------
    // ALTERAR STATUS
    // Body (form-encode): id, status, observacao_final (obrigatória ao concluir)
    // -------------------------------------------------------
    public function alterarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id            = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status        =      $_POST['status']           ?? '';
        $observacao    = trim($_POST['observacao_final'] ?? '');

        if (!$id || !in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            http_response_code(422);
            echo json_encode(['erro' => 'ID ou status invalido.']);
            return;
        }

        // Conclusão exige observação final
        if ($status === 'concluido' && $observacao === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Informe a observacao final para concluir o atendimento.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos
                    SET status          = :status,
                        observacao_final = :observacao
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id'         => $id,
                'status'     => $status,
                'observacao' => $observacao !== '' ? $observacao : null,
            ]);

            echo json_encode(['mensagem' => 'Status atualizado com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status do atendimento.']);
        }
    }
}