<?php

require_once __DIR__ . '/app/Middleware/auth.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

switch ($controller) {

    // -------------------------------------------------------
    // AUTH — rotas públicas (sem exigirAutenticacao)
    // -------------------------------------------------------
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Acao de autenticacao nao encontrada.';
        }
        break;

    // -------------------------------------------------------
    // USUARIOS
    // -------------------------------------------------------
    case 'usuarios':
        exigirAutenticacao();
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':    $usuariosController->listar();      break;
            case 'buscar':    $usuariosController->buscarPorId(); break;
            case 'criar':     $usuariosController->criar();       break;
            case 'atualizar': $usuariosController->atualizar();   break;
            case 'excluir':   $usuariosController->excluir();     break;
            default:
                http_response_code(404);
                echo json_encode(['erro' => 'Acao de usuarios nao encontrada.']);
        }
        break;

    // -------------------------------------------------------
    // PESSOAS
    // -------------------------------------------------------
    case 'pessoas':
        exigirAutenticacao();
        $ctrl = new PessoasController();

        switch ($action) {
            case 'listar':    $ctrl->listar();      break;
            case 'buscar':    $ctrl->buscarPorId(); break;
            case 'criar':     $ctrl->criar();       break;
            case 'atualizar': $ctrl->atualizar();   break;
            case 'inativar':  $ctrl->inativar();    break;
            default:
                http_response_code(404);
                echo json_encode(['erro' => 'Acao de pessoas nao encontrada.']);
        }
        break;

    // -------------------------------------------------------
    // TIPOS DE ATENDIMENTOS
    // -------------------------------------------------------
    case 'tipos':
        exigirAutenticacao();
        $ctrl = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':    $ctrl->listar();      break;
            case 'buscar':    $ctrl->buscarPorId(); break;
            case 'criar':     $ctrl->criar();       break;
            case 'atualizar': $ctrl->atualizar();   break;
            case 'inativar':  $ctrl->inativar();    break;
            default:
                http_response_code(404);
                echo json_encode(['erro' => 'Acao de tipos nao encontrada.']);
        }
        break;

    // -------------------------------------------------------
    // ATENDIMENTOS
    // -------------------------------------------------------
    case 'atendimentos':
        exigirAutenticacao();
        $ctrl = new AtendimentosController();

        switch ($action) {
            case 'listar':        $ctrl->listar();        break;
            case 'buscar':        $ctrl->buscarPorId();   break;
            case 'criar':         $ctrl->criar();         break;
            case 'alterarStatus': $ctrl->alterarStatus(); break;
            default:
                http_response_code(404);
                echo json_encode(['erro' => 'Acao de atendimentos nao encontrada.']);
        }
        break;

    // -------------------------------------------------------
    // CONTROLLER NÃO ENCONTRADO
    // -------------------------------------------------------
    default:
        http_response_code(404);
        echo 'Controller nao encontrado.';
}