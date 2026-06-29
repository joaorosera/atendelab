<?php
declare(strict_types=1);
$baseUrl = '/atendelab/public/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | AtendeLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --al-green: #198754; --al-green-dark: #146c43; }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: system-ui, -apple-system, sans-serif; min-height: 100vh; display: flex; }

        /* ── Painel esquerdo ── */
        .al-side {
            width: 38%;
            background: var(--al-green);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 2.5rem;
            color: #fff;
        }

        .al-side .logo-box {
            width: 56px; height: 56px;
            background: rgba(255,255,255,.18);
            border-radius: 14px;
            display: grid; place-items: center;
            font-size: 1.2rem; font-weight: 800;
            letter-spacing: .04em;
            margin-bottom: 1.5rem;
        }

        .al-side h1 { font-size: 2rem; font-weight: 700; margin-bottom: .4rem; }
        .al-side .subtitle { opacity: .82; font-size: .95rem; line-height: 1.5; }

        .al-side .credits {
            font-size: .78rem;
            opacity: .65;
            line-height: 1.6;
        }
        .al-side .credits strong { opacity: 1; }

        /* ── Painel direito ── */
        .al-form-area {
            flex: 1;
            background: #f5f6f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .al-form-box { width: min(100%, 400px); }

        .al-form-box h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: .3rem; color: #1a1a1a; }
        .al-form-box .hint { font-size: .88rem; color: #6c757d; margin-bottom: 1.75rem; }

        .al-form-box label { font-weight: 500; font-size: .9rem; color: #333; margin-bottom: .35rem; display: block; }

        .al-form-box input {
            width: 100%;
            padding: .65rem .9rem;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            font-size: .97rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
            margin-bottom: 1rem;
        }
        .al-form-box input:focus {
            border-color: var(--al-green);
            box-shadow: 0 0 0 3px rgba(25,135,84,.15);
        }

        .al-form-box .mb-last { margin-bottom: 1.5rem; }

        .btn-entrar {
            width: 100%;
            padding: .72rem;
            background: var(--al-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-entrar:hover { background: var(--al-green-dark); }

        .alert-box {
            padding: .75rem 1rem;
            border-radius: 8px;
            font-size: .88rem;
            margin-bottom: 1.25rem;
        }
        .alert-success { background: #d1e7dd; color: #0a3622; border: 1px solid #a3cfbb; }
        .alert-danger  { background: #f8d7da; color: #58151c; border: 1px solid #f1aeb5; }

        /* Mobile */
        @media (max-width: 767px) {
            body { flex-direction: column; }
            .al-side { width: 100%; min-height: auto; padding: 2rem 1.5rem; }
            .al-side .credits { margin-top: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- Painel verde -->
<div class="al-side">
    <div>
        <div class="logo-box">AL</div>
        <h1>AtendeLab</h1>
        <p class="subtitle">Sistema de controle de atendimentos acadêmicos da UNIVILLE.</p>
    </div>
    <div class="credits">
        Desenvolvido por <strong>Vinicius Werner</strong><br>
        Trabalho acadêmico · Fábrica de Software
    </div>
</div>

<!-- Formulário -->
<div class="al-form-area">
    <div class="al-form-box">
        <h2>Bem-vindo de volta</h2>
        <p class="hint">Informe suas credenciais para acessar o sistema.</p>

        <?php if (!empty($mensagem)): ?>
            <div class="alert-box alert-success">
                <?= htmlspecialchars((string) $mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erroLogin)): ?>
            <div class="alert-box alert-danger">
                <?= htmlspecialchars((string) $erroLogin, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= $baseUrl ?>?controller=auth&action=entrar">
            <div>
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required autofocus>
            </div>
            <div class="mb-last">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required>
            </div>
            <button class="btn-entrar" type="submit">Entrar</button>
        </form>
    </div>
</div>

</body>
</html>
