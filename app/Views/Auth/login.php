<?php
$baseUrl = '/atendelab/public/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | AtendeLab</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f4f4f2;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e5e5e3;
            border-radius: 12px;
            padding: 2.5rem 2rem;
        }

        .login-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }

        /* .login-brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background-color: #f4f4f2;
            border: 1px solid #e5e5e3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: #1a1a18;
            flex-shrink: 0;
        } */

        .login-brand-name {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a18;
        }

        .login-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a18;
            margin-bottom: 4px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #888780;
            margin-bottom: 2rem;
        }

        .login-field {
            margin-bottom: 1.25rem;
        }

        .login-field label {
            display: block;
            font-size: 13px;
            color: #5f5e5a;
            margin-bottom: 6px;
        }

        .login-field input {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            border: 1px solid #d3d1c7;
            border-radius: 8px;
            background: #fafafa;
            color: #1a1a18;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .login-field input::placeholder { color: #b4b2a9; }

        .login-field input:hover { border-color: #b4b2a9; }

        .login-field input:focus {
            border-color: #378add;
            box-shadow: 0 0 0 3px rgba(55, 138, 221, 0.12);
            background: #ffffff;
        }

        .login-btn {
            width: 100%;
            height: 38px;
            border: none;
            border-radius: 8px;
            background-color: #1a1a18;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: opacity 0.15s;
        }

        .login-btn:hover { opacity: 0.85; }
        .login-btn:active { opacity: 0.75; }

        .login-footer {
            font-size: 12px;
            color: #b4b2a9;
            text-align: center;
            margin-top: 2rem;
        }

        .alert-box {
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 12px;
            margin-bottom: 1.25rem;
        }

        .alert-success { background: #d1e7dd; color: #0a3622; border: 1px solid #a3cfbb; }
        .alert-danger  { background: #fcebeb; color: #a32d2d; border: 1px solid #f09595; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-brand">
            <!-- <div class="login-brand-icon">AL</div> -->
            <span class="login-brand-name">AtendeLab</span>
        </div>

        <h1 class="login-title">Bem-vindo de volta</h1>
        <p class="login-subtitle">Informe suas credenciais para acessar o sistema.</p>

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
            <div class="login-field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required autofocus>
            </div>
            <div class="login-field">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required>
            </div>
            <button class="login-btn" type="submit">Entrar</button>
        </form>

        <p class="login-footer">Trabalho acadêmico — Fábrica de Software</p>

    </div>
</div>

</body>
</html>