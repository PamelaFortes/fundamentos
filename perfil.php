<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?erro=' . urlencode('Faça login para continuar.'));
    exit;
}

$userId = (int) $_SESSION['usuario_id'];
$msg = "";

$stmt = $conn -> prepare("SELECT id, nome, email, senha FROM usuarios WHERE id = ? LIMIT 1");
$stmt -> bind_param("i", $userId);
$stmt -> execute();
$result = $stmt -> get_result();

if (!$result || !$result -> num_rows) {
    session_unset();
    session_destroy();
    header('Location: index.php?erro=' . urlencode('Sessão inválida. Faça login novamente.'));
    exit;
}

$user = $result -> fetch_assoc();
$stmt -> close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoNome = trim($_POST['nome'] ?? $user['nome']);
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $senhaNova = $_POST['senha_nova'] ?? '';
    $senhaConf = $_POST['senha_confirma'] ?? '';

    if ($novoNome === '' ) {
        $msg = "Informe um nome válido.";
    } else {
        $okNome = true;
        if ($novoNome !== $user['nome']) {
            $updNome = $conn -> prepare ("UPDATE usuarios SET nome = ? WHERE id = ?");
            $updNome -> bind_param("si", $novoNome, $userId);
            $okNome = $upNome -> execute();
            $updNome -> close();

            if ($okNome) {
                $_SESSION['usuario_nome'] = $novoNome;
            }
        }

        $okSenha = true;
        if ($senhaNova !== '') {
            if ($senhaAtual === '' || $senhaConf === '') {
                $okSenha = false;
                $msg = "Para alterar a senha, preencha Senha Atual e Confirmação.";
            } elseif ($senhaNova !== $senhaConf) {
                $okSenha = false;
                $msg = "A confirmação da nova senha não confere.";
            } else {
                if (!password_verify($senhaAtual, $user['senha'])) {
                    $okSenha = false;
                    $msg = "Senha atual incorreta.";
                } else {
                    $hash = password_hash($senhaNova, PASSWORD_DEFAULT);
                    $updSenha = $conn -> prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                    $updSenha -> bind_param("si", $hash, $userId);
                    $okSenha = $updSenha -> execute();
                    $updSenha -> close();

                    if (!$okSenha) {
                        $msg = "Erro ao atualizar a senha. Tente novamente.";
                    }
                }
            }
        }

        if ($okNome && $okSenha) {
            $stmt2 = $conn -> prepare("SELECT id, nome, email, senha FROM usuarios WHERE id = ? LIMIT 1");
            $stmt2 = bind_param('i', $userId);
            $stmt2 = execute();
            $result2 = $stmt2 -> get_result();
            $user = $result2 -> fetch_assoc();
            $stmt2 -> close();

            if ($msg === "") {
                $msg = "Perfil atualizado com sucesso!";
            }
        } elseif ($msg === "") {
            $msg = "Nenhuma alteração realizada.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Meu Perfil - Mini Projeto - Quiz</title>
    </head>
    <body>
        <h1>Meu Perfil</h1>

        <div>
            <a href="index.php">Voltar</a>
            <a href="quiz.php">Quiz's</a>
            <a href="logout.php">Sair</a>
        </div>

        <?php if (!empty($msg)): ?>
            <p class="<?= strpos($msg, 'sucesso') !== false ? 'msg-ok' : (strpos($msg, 'erro') !== false ? 'msg-erro' : '') ?>">
                <?= $msg ?>
            </p>
        <?php endif; ?>

        <form method='POST'>
            <div>
                <div>
                    <?= htmlspecialchars($user['nome'])?>
                </div>
                <div>
                    <div>
                        <strong><?htmlspecialchars($user['nome'] ?: 'Sem nome') ?></strong>
                    </div>
                    <div>
                        <?= htmlspecialchars($user['email']) ?>
                    </div>
                </div>
            </div>

            <div>Dados Básicos</div>
            <div>
                <label for="nome">Nome</label><br>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
            </div>

            <div>Alterar senha (opcional)</div>
            <div>
                <label for="senha_atual">Senha Atual</label><br>
                <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite apenas se for trocar a senha">
            </div>
            <div>
                <label for="senha_nova">Nova senha</label><br>
                <input type="password" id="senha_nova" name="senha_nova" minlegth="6">
            </div>
            <div>
                <label for="senha_confirma">Confirmar nova senha</label><br>
                <input type="password" id="senha_confirma" name="senha_confirma" minlegth="6">
            </div>

            <div>
                <button type="submit">Salvar alterações</button>
            </div>
        </form>
    </body>
</html>