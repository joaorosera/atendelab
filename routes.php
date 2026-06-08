<?php
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TipoAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

$controller = $_GET['controller'] ?? 'home';
$action     = $_GET['action']     ?? 'index';

// -------------------------------------------------------
// USUARIOS
// -------------------------------------------------------
if ($controller === 'usuarios') {

    $ctrl = new UsuariosController();

    switch ($action) {
        case 'listar':   $ctrl->listar();      break;
        case 'buscar':   $ctrl->buscarPorId(); break;
        case 'criar':    $ctrl->criar();       break;
        case 'atualizar':$ctrl->atualizar();   break;
        case 'excluir':  $ctrl->excluir();     break;
        default: echo json_encode(['erro' => 'Acao de usuarios nao encontrada.']); break;
    }

// -------------------------------------------------------
// PESSOAS
// -------------------------------------------------------
} elseif ($controller === 'pessoas') {

    $ctrl = new PessoasController();

    switch ($action) {
        case 'listar':   $ctrl->listar();      break;
        case 'buscar':   $ctrl->buscarPorId(); break;
        case 'criar':    $ctrl->criar();       break;
        case 'atualizar':$ctrl->atualizar();   break;
        case 'excluir':  $ctrl->excluir();     break;
        default: echo json_encode(['erro' => 'Acao de pessoas nao encontrada.']); break;
    }

// -------------------------------------------------------
// TIPOS DE ATENDIMENTOS
// -------------------------------------------------------
} elseif ($controller === 'tipos') {

    $ctrl = new TiposAtendimentosController();

    switch ($action) {
        case 'listar':   $ctrl->listar();      break;
        case 'buscar':   $ctrl->buscarPorId(); break;
        case 'criar':    $ctrl->criar();       break;
        case 'atualizar':$ctrl->atualizar();   break;
        case 'excluir':  $ctrl->excluir();     break;
        default: echo json_encode(['erro' => 'Acao de tipos nao encontrada.']); break;
    }

// -------------------------------------------------------
// ATENDIMENTOS
// -------------------------------------------------------
} elseif ($controller === 'atendimentos') {

    $ctrl = new AtendimentosController();

    switch ($action) {
        case 'listar':   $ctrl->listar();      break;
        case 'buscar':   $ctrl->buscarPorId(); break;
        case 'criar':    $ctrl->criar();       break;
        case 'atualizar':$ctrl->atualizar();   break;
        default: echo json_encode(['erro' => 'Acao de atendimentos nao encontrada.']); break;
    }

// -------------------------------------------------------
// HOME
// -------------------------------------------------------
} else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Rotas disponiveis:</p>';
    echo '<ul>';
    echo '<li>?controller=usuarios&action=listar</li>';
    echo '<li>?controller=pessoas&action=listar</li>';
    echo '<li>?controller=tipos&action=listar</li>';
    echo '<li>?controller=atendimentos&action=listar</li>';
    echo '</ul>';
}