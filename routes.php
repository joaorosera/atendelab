<?php

require_once __DIR__ . '/app/Middleware/auth.php';

function responderRotaNaoEncontrada(string $mensagem = 'Rota não encontrada.'): void
{
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['erro' => $mensagem], JSON_UNESCAPED_UNICODE);
}

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

// Autenticação: login, entrar, dashboard, logout
if ($controller === 'auth') {
    require_once __DIR__ . '/app/Controllers/AuthController.php';
    $auth = new AuthController();

    switch ($action) {
        case 'login':
            $auth->exibirLogin();
            break;
        case 'entrar':
            $auth->entrar();
            break;
        case 'dashboard':
            exigirAutenticacao();
            $auth->dashboard();
            break;
        case 'logout':
            $auth->logout();
            break;
        default:
            responderRotaNaoEncontrada('Ação de autenticação não encontrada.');
    }
    exit;
}

// Todas as rotas abaixo exigem sessão ativa
exigirAutenticacao();

switch ($controller) {

    case 'frontend':
        require_once __DIR__ . '/app/Controllers/FrontendController.php';
        $frontendController = new FrontendController();
        switch ($action) {
            case 'pessoas':
                $frontendController->pessoas();
                break;
            case 'tipos':
                $frontendController->tipos();
                break;
            case 'atendimentos':
                $frontendController->atendimentos();
                break;
            default:
                responderRotaNaoEncontrada('Página não encontrada.');
        }
        break;

    case 'dashboard':
        require_once __DIR__ . '/app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        switch ($action) {
            case 'resumo':
                $dashboardController->resumo();
                break;
            default:
                responderRotaNaoEncontrada('Ação de dashboard não encontrada.');
        }
        break;

    case 'pessoas':
        require_once __DIR__ . '/app/Controllers/PessoasController.php';
        $pessoasController = new PessoasController();
        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $pessoasController->buscar();
                break;
            case 'criar':
                $pessoasController->criar();
                break;
            case 'atualizar':
                $pessoasController->atualizar();
                break;
            case 'inativar':
                $pessoasController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de pessoas não encontrada.');
        }
        break;

    case 'tipos':
        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
        $tiposController = new TiposAtendimentosController();
        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $tiposController->buscar();
                break;
            case 'criar':
                $tiposController->criar();
                break;
            case 'atualizar':
                $tiposController->atualizar();
                break;
            case 'inativar':
                $tiposController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de tipos de atendimento não encontrada.');
        }
        break;

    case 'atendimentos':
        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
        $atendimentosController = new AtendimentosController();
        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'visualizar':
                $atendimentosController->buscar();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'alterarStatus':
            case 'atualizarStatus':
                $atendimentosController->alterarStatus();
                break;
            default:
                responderRotaNaoEncontrada('Ação de atendimentos não encontrada.');
        }
        break;

    case 'usuarios':
        require_once __DIR__ . '/app/Controllers/UsuariosController.php';
        $obj = new UsuariosController();
        if (!method_exists($obj, $action)) {
            responderRotaNaoEncontrada('Ação de usuários não encontrada.');
            break;
        }
        $obj->$action();
        break;

    default:
        http_response_code(404);
        exit('Controller não encontrado.');
}
