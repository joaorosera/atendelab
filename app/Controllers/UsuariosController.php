<?php

// controller dos usuarios do sistema (quem faz login, tipo admin/atendente/aluno)
class UsuariosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar()
    {
        header('Content-Type: application/json; charset=utf-8');

        // nunca retorna a coluna senha, mesmo com hash nao precisa expor isso
        $sql = 'SELECT id, nome, email, perfil, status, criado_em FROM usuarios ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id, nome, email, perfil, status, criado_em FROM usuarios WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado.']);
            return;
        }

        echo json_encode($usuario, JSON_UNESCAPED_UNICODE);
    }

    public function criar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $email === '' || $senha === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome, e-mail e senha são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        // perfis aceitos: admin, aluno e atendente (ver enum da tabela usuarios)
        if (!in_array($perfil, ['admin', 'aluno', 'atendente'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Perfil inválido. Use: admin, aluno ou atendente.']);
            return;
        }

        if ($status != 'ativo' && $status != 'inativo') {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: ativo ou inativo.']);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = 'INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES (:nome, :email, :senha, :perfil, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Usuário cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId(),
            ], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            // provavelmente email duplicado, tem unique no banco
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar usuário.']);
        }
    }

    public function atualizar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

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

        if (!in_array($perfil, ['admin', 'aluno', 'atendente'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Perfil inválido. Use: admin, aluno ou atendente.']);
            return;
        }

        if ($status != 'ativo' && $status != 'inativo') {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido. Use: ativo ou inativo.']);
            return;
        }

        try {
            $sql = 'UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, status = :status WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Usuário atualizado com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar usuário.']);
        }
    }

    public function excluir()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = 'DELETE FROM usuarios WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Usuário excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            // se o usuario ja tiver atendimento vinculado, da erro de FK aqui
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir usuário.']);
        }
    }
}
