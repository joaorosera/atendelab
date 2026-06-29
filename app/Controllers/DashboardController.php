<?php

class DashboardController
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

    // numeros pro dashboard, nada muito elaborado
    public function resumo()
    {
        $totalPessoas = (int) $this->pdo->query('SELECT COUNT(*) FROM pessoas')->fetchColumn();
        $totalTipos = (int) $this->pdo->query('SELECT COUNT(*) FROM tipos_atendimentos')->fetchColumn();
        $totalAtendimentos = (int) $this->pdo->query('SELECT COUNT(*) FROM atendimentos')->fetchColumn();

        $recentes = $this->pdo->query(
            'SELECT a.id, p.nome AS pessoa_nome, t.nome AS tipo_nome, a.status, a.data_atendimento
             FROM atendimentos a
             INNER JOIN pessoas p ON p.id = a.pessoa_id
             INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             ORDER BY a.id DESC
             LIMIT 5'
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->json([
            'indicadores' => [
                'total_pessoas' => $totalPessoas,
                'total_tipos' => $totalTipos,
                'total_atendimentos' => $totalAtendimentos,
            ],
            'atendimentos_recentes' => $recentes,
        ]);
    }
}
