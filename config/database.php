<?php
// dados de conexao com o banco (xampp, porta default do mysql as vezes muda quando
// tem outro servico ja usando a 3306, por isso ta na 3307 aqui)
$host     = 'localhost';
$port     = '3307';
$dbname   = 'atendelab';
$user     = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // por enquanto so mostra a mensagem mesmo, depois ver se da pra fazer uma tela de erro melhor
    die('Erro ao conectar com o banco de dados: ' . $e->getMessage());
}
