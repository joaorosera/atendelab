<?php

// inicia a sessao se ainda nao tiver uma rodando (precisa disso pq esse arquivo
// é incluido tanto pelo index quanto direto em alguns controllers)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario']) && is_array($_SESSION['usuario']);
}

// chama isso no topo das rotas que precisam de login
function exigirAutenticacao(): void
{
    if (!usuarioAutenticado()) {
        $_SESSION['mensagem'] = 'Faca login para acessar a area restrita.';
        header('Location: ?controller=auth&action=login');
        exit;
    }
}

function usuarioAtual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}
