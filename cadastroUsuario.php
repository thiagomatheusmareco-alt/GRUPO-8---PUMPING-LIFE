<?php
session_start();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $tipoConta = $_POST['tipo_conta'] ?? '';

    if (
        $nome === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($senha) < 6 ||
        !in_array($tipoConta, ['empresa', 'trabalhador'], true)
    ) {
        $erro = 'Preencha todos os campos corretamente. A senha deve ter pelo menos 6 caracteres.';
    } else {

        $arquivos = [
            'nome.json',
            'email.json',
            'senha.json',
            'tipoConta.json',
            'perfil.json'
        ];

        $dados = [];

        foreach ($arquivos as $arquivo) {

            if (file_exists($arquivo)) {
                $conteudo = json_decode(
                    file_get_contents($arquivo),
                    true
                );

                $dados[$arquivo] = is_array($conteudo)
                    ? $conteudo
                    : [];

            } else {
                $dados[$arquivo] = [];
            }
        }

        if (in_array(
            $email,
            array_map('strtolower', $dados['email.json']),
            true
        )) {

            $erro = 'Este e-mail já está cadastrado.';

        } else {

            $dados['nome.json'][] = $nome;
            $dados['email.json'][] = $email;

            // Armazena a senha de forma protegida
            $dados['senha.json'][] = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );

            $dados['tipoConta.json'][] = $tipoConta;

            // Cria um perfil vazio, mantendo o mesmo índice dos demais arquivos
            $dados['perfil.json'][] = [
                'endereco' => '',
                'descricao' => '',
                'qualificacoes' => '',
                'genero' => '',
                'curriculo' => ''
            ];

            $sucesso = true;

            foreach ($dados as $arquivo => $conteudo) {

                $json = json_encode(
                    $conteudo,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                );

                if (
                    $json === false ||
                    file_put_contents($arquivo, $json, LOCK_EX) === false
                ) {
                    $sucesso = false;
                    break;
                }
            }

            if ($sucesso) {
                header('Location: index.php?cadastro=sucesso');
                exit;
            }

            $erro = 'Não foi possível salvar o cadastro. Verifique as permissões dos arquivos JSON.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Cadastro | Projeto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>

        body {
            min-height: 100vh;
            margin: 0;

            background: linear-gradient(
                -45deg,
                #696969,
                #4F4F4F,
                #363636,
                #1C1C1C
            );

            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .topo {
            background-color: #D3D3D3;
            padding: 12px;
            text-align: center;
        }

        .card {

            --main-col: #ffeba7;
            --bg-col: #2a2b38;
            --bg-field: #1f2029;

            width: min(600px, calc(100% - 32px));

            margin: 42px auto;
            padding: 1.9rem 1.2rem;

            text-align: center;

            background: var(--bg-col);

            border-radius: 10px;
            border: 1px solid var(--main-col);
        }

        .title {
            margin-bottom: 1rem;
            font-size: 1.5em;
            font-weight: 500;
            color: var(--main-col);
            text-shadow: 1px 1px 20px var(--main-col);
            text-transform: uppercase;
        }

        .field {
            margin-top: .75rem;
            display: flex;
            align-items: center;
            padding-left: .75rem;
            gap: .5rem;
            background-color: var(--bg-field);
            border-radius: 4px;
        }

        .input-icon {
            color: var(--main-col);
            min-width: 1.2em;
        }

        .input-field {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            color: var(--main-col);
            padding: .75em 1em .75em 0;
        }

        .input-field::placeholder {
            color: #c9c9c9;
        }

        .select-field {
            width: 100%;
            padding: .75em;
            color: var(--main-col);
            background: var(--bg-field);
            border: 1px solid #555;
            border-radius: 4px;
        }

        .btn {
            margin: 1rem .5rem .5rem;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: .8em;
            text-transform: uppercase;
            padding: .7em 1.4em;
            background-color: var(--main-col);
            color: var(--bg-col);
            transition: all .3s ease-in-out;
            cursor: pointer;
        }

        .btn:hover {
            background: var(--bg-field);
            color: var(--main-col);
        }

        .link-voltar {
            display: block;
            color: #f5f5f5;
            font-size: .85em;
            margin-top: .5rem;
        }

        .link-voltar:hover {
            color: var(--main-col);
        }

        .erro {
            color: #ff9b9b;
            margin: .75rem 0;
        }

    </style>

</head>

<body>

    <header class="topo">
        <h2><b>PROJETO</b></h2>
    </header>

    <main class="card">

        <h4 class="title">Criar conta</h4>

        <?php if ($erro !== ''): ?>
            <p class="erro">
                <?php echo htmlspecialchars($erro); ?>
            </p>
        <?php endif; ?>

        <form action="cadastroUsuario.php" method="post">

            <label class="field" for="nome">

                <span class="input-icon">👤</span>

                <input
                    class="input-field"
                    id="nome"
                    name="nome"
                    type="text"
                    placeholder="Nome"
                    required
                    maxlength="120"
                    value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                >

            </label>

            <label class="field" for="email">

                <span class="input-icon">@</span>

                <input
                    class="input-field"
                    id="email"
                    name="email"
                    type="email"
                    placeholder="Email"
                    required
                    maxlength="190"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                >

            </label>

            <label class="field" for="senha">

                <span class="input-icon">🔒</span>

                <input
                    class="input-field"
                    id="senha"
                    name="senha"
                    type="password"
                    placeholder="Senha (mínimo 6 caracteres)"
                    required
                    minlength="6"
                >

            </label>

            <div class="field" style="margin-top: .75rem; padding: .35rem;">

                <select
                    class="select-field"
                    name="tipo_conta"
                    required
                >

                    <option value="" disabled selected>
                        Selecione o tipo de conta
                    </option>

                    <option value="empresa">
                        Empresa
                    </option>

                    <option value="trabalhador">
                        Trabalhador
                    </option>

                </select>

            </div>

            <button class="btn" type="submit">
                Cadastrar
            </button>

        </form>

        <a class="link-voltar" href="index.php">
            Já possui uma conta? Voltar ao login
        </a>

    </main>

</body>
</html>