<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale-1">
        <meta http-equiv="content-language" content="pt-br">
        <title>PHP / Array</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <style>
        .user{
            float: right;
        }
        body{
            background: #696969;
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
            background-position: 0% 50%;
            }
            50% {
            background-position: 100% 50%;
            }
            100% {
            background-position: 0% 50%;
        }
        }
        .card-body{
            background-color: #D3D3D3;
        }
        /* From Uiverse.io by 3bdel3ziz-T */ 
        .card {
            --main-col: #ffeba7;
            --bg-col: #2a2b38;
            --bg-field: #1f2029;
            position: absolute;
            left: 50%;
            top: 40%;
            -moz-transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -o-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            width: 600px;
            padding: 1.9rem 1.2rem;
            text-align: center;
            background: var(--bg-col);
            border-radius: 10px;
            border: 1px solid var(--main-col);
            user-select: none;
        }

        /*Inputs*/
        .field {
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-left: 0.5rem;
            gap: 0.5rem;
            background-color: var(--bg-field);
            border-radius: 4px;
        }

        .input-icon {
            width: 1em;
            color: var(--main-col);
            fill: var(--main-col);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-field {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            color: var(--main-col);
            padding: 0.5em 1em 0.5em 0;
            caret-color: var(--main-col);
        }

        .filed:has(.input-field:valid) {
            border: 1px solid var(--main-col);
        }

        /*Text*/
        .title {
            margin-bottom: 1rem;
            font-size: 1.5em;
            font-weight: 500;
            color: var(--main-col);
            text-shadow: 1px 1px 20px var(--main-col);
            text-transform: uppercase;
        }

        /*Buttons*/
        .btn {
            margin: 1rem;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8em;
            text-transform: uppercase;
            padding: 0.6em 1.2em;
            background-color: var(--main-col);
            color: var(--bg-col);
            box-shadow: 0 8px 24px 0 rgb(255 235 167 / 20%);
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .btn-link {
            color: #f5f5f5;
            display: block;
            font-size: 0.75em;
            transition: color 0.3s ease-out;
        }

        /*Hover & focus*/
        .field input:focus::placeholder {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn:hover {
            background-color: var(--bg-field);
            color: var(--main-col);
            box-shadow: 0 8px 24px 0 rgb(16 39 112 / 20%);
        }

        .btn-link:hover {
            color: var(--main-col);
            text-decoration: underline;
        }

    </style>
    <body>
        <div class="card-body" style="background-color: #D3D3D3">    
            <center><h2><b>PROJETO</b></h2></center>
        </div>
        <br/><br/>
        <?php if (($_GET['cadastro'] ?? '') === 'sucesso'): ?>

    <div class="alert alert-success text-center mx-auto"
         style="max-width:600px;">

        Cadastro realizado com sucesso!
        Entre com seu novo e-mail e senha.

    </div>

    <?php elseif (($_GET['erro'] ?? '') === 'credenciais'): ?>

        <div class="alert alert-danger text-center mx-auto"
            style="max-width:600px;">

            E-mail ou senha inválidos. Tente novamente.

        </div>

    <?php endif; ?>
        <div class="row justify-content-center row-cols-1 row-cols-md-3 text-center align-items-center">
            <div class="cols">
                    <div class="card">
                        <h4 class="title">Login</h4>
                        <form action="login.php" method="post">
                            <label class="field" for="logemail">
                            <span class="input-icon">@</span>
                            <input
                                autocomplete="off"
                                id="logemail"
                                placeholder="Email"
                                required
                                class="input-field"
                                name="email"
                                type="email"
                            />
                            </label>
                            <label class="field" for="logpass">
                            <svg
                                class="input-icon"
                                viewBox="0 0 500 500"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                d="M80 192V144C80 64.47 144.5 0 224 0C303.5 0 368 64.47 368 144V192H384C419.3 192 448 220.7 448 256V448C448 483.3 419.3 512 384 512H64C28.65 512 0 483.3 0 448V256C0 220.7 28.65 192 64 192H80zM144 192H304V144C304 99.82 268.2 64 224 64C179.8 64 144 99.82 144 144V192z"
                                ></path>
                            </svg>

                            <input
                                id="logpass"
                                placeholder="Senha"
                                required
                                class="input-field"
                                name="senha"
                                type="password"
                            />
                            </label>
                            <button class="btn" type="submit">Entrar</button>
                        </form>
                        <a href="cadastroUsuario.php" class="btn-link">
                            Ainda não tem uma conta? Cadastre-se
                        </a>
                    </div>
            </div>
        </div>
    </body>
</html>