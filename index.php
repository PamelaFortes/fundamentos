<?php
session_start();

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Página Inicial - Mini Projeto - Quiz</title>
    </head>
    <body>

        <?php if (isset($_SESSION['id'])): ?>
            <!-- Usuário quando já estiver logado deverá executar o cód -->
            <h1>Bem-vindo(a) <?= htmlspecialchars($_SESSION['nome'] ?? $_SESSION['email']) ?>!</h1>
            <p>Você já está autenticado.</p>

            <a href="perfil.php"><button>Meu Perfil</button></a>
            <a href="quiz.php"><button>Quiz</button></a>
            <a href="resultado.php"><button>Ir para Resultado</button></a>
            <a href="logout.php"><button>Sair</button></a>

            <?php else: ?>
                <!-- Formulário de Login -->
                 
                <h1>Entrar na Plataforma Quiz</h1>

                <?php if(!empty($_GET['erro'])): ?>
                    <p><?= htmlspecialchars($_GET['erro']) ?></p>
                <?php endif; ?>

                <?php if (!empty($_GET['ok'])): ?>
                    <p><?= htmlspecialchars($_GET['ok']) ?></p>
                <?php endif; ?>

                <form action="login.php" method="POST" autocomplete="off">
                    <label for="email">E-mail:</label><br>
                    <input type="email" id="email" name="email" required>
            
                    <br>
                    <br>

                    <label for="senha">Senha:</label><br>
                    <input type="password" id="senha" name="senha" required>

                    <br>
                    <br>

                    <!-- Após o usuário clicar no botão Entrar redirecionar para a tela perfil.php -->

                    <input type="hidden" name="next" value="perfil.php">

                    <button type="submit">Entrar</button>              
                </form>

            <!-- Adicionando nova linha para o usuario que esqueceu a senha -->
            <br>
            <a href="esqueci_senha.php">Esqueci a senha</a>
            <br>

            <br>
            <a href="registro.php"><button type="button">Cadastrar</button></a>
        <?php endif; ?>
    </body>
</html>