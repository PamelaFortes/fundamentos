<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?erro=' . urlencode('Faça login para continuar.'));
    exit;
}

$userId = (int) $_SESSION['usuario_id'];
$msg = "";

// 1. Busca inicial do usuário
$stmt = $conn->prepare("SELECT id, nome, email, senha, foto_perfil FROM usuarios WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || !$result->num_rows) {
    session_unset();
    session_destroy();
    header('Location: index.php?erro=' . urlencode('Sessão inválida. Faça login novamente.'));
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// -- Configuração para armazenamento de fotos --
$dirUpload = __DIR__ . 'uploads/avatars/'; // Adicionei a barra no final para garantir
$urlBase = '/fundamentos/uploads/avatars/'; 
$tamanhoMaximo = 2 * 1024 * 1024; 
$tiposExtensao = ['image/jpeg' => 'jpg', 'image/png'     => 'png', 'image/webp' => 'webp'];

// -- Limpeza de cache de armazenamento de fotos
function limparArquivoAntigo(?string $caminhoRelativo): void {
    if (!$caminhoRelativo) return;
    // Ajuste para garantir caminho correto no sistema de arquivos
    $arquivo = __DIR__ . "/" . $caminhoRelativo; 
    // Remove duplicidade de barras se houver
    $arquivo = str_replace('//', '/', $arquivo);
    
    if (is_file($arquivo)) {
        @unlink($arquivo);
    }
}

$feedback = null;

// -------------------------
// PROCESSAMENTO: FOTO
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    // Envio de foto
    if ($_POST['acao'] === 'upload') {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $feedback = ['tipo' => 'erro', 'msg' => 'Falha no Upload. Selecione um arquivo válido.'];
        } else {
            $foto = $_FILES['foto'];

            // Validação do tipo da foto
            $mime = mime_content_type($foto['tmp_name']);
            if (!isset($tiposExtensao[$mime])) {
                $feedback = ['tipo' => 'erro', 'msg' => 'Formato inválido. Envie arquivo com formato JPG, PNG ou WEBP.'];
            } elseif ($foto['size'] > $tamanhoMaximo) {
                $feedback = ['tipo' => 'erro', 'msg' => 'Arquivo muito grande (máx. 2 MB).'];
            } else {
                // Gera nome único: uID_timestamp.ext
                $ext = $tiposExtensao[$mime];
                $nomeUnico = 'u' . $userId . '_' . time() . '.' . $ext;

                // Garante diretório
                if (!is_dir($dirUpload)) {
                    @mkdir($dirUpload, 0775, true);
                }

                // Caminhos
                $destinoFs = $dirUpload . $nomeUnico; // Caminho do servidor
                $destinoRel = $urlBase . $nomeUnico;  // Caminho para o banco/HTML

                if (move_uploaded_file($foto['tmp_name'], $destinoFs)) {
                    // Apaga a antiga (se houver) usando a variável correta $user
                    limparArquivoAntigo($user['foto_perfil']);

                    // Atualiza Banco de Dados
                    $up = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
                    $up->bind_param("si", $destinoRel, $userId);
                    if ($up->execute()) {
                        $feedback = ['tipo' => 'ok', 'msg' => 'Foto atualizada com sucesso!'];
                        $user['foto_perfil'] = $destinoRel; // Atualiza a variável em memória
                    } else {
                        @unlink($destinoFs);
                        $feedback = ['tipo' => 'erro', 'msg' => 'Não foi possível salvar no banco. Tente novamente.'];
                    }
                    $up->close();
                } else {
                    $feedback = ['tipo' => 'erro', 'msg' => 'Não foi possível mover o arquivo. Verifique permissões.'];
                }
            }
        }
    }
    
    // Remover a foto
    elseif ($_POST['acao'] === 'remover') {
        limparArquivoAntigo($user['foto_perfil']);
        
        $up = $conn->prepare("UPDATE usuarios SET foto_perfil = NULL WHERE id = ?");
        $up->bind_param("i", $userId);
        if ($up->execute()) {
            $feedback = ['tipo' => 'ok', 'msg' => 'Foto removida.'];
            $user['foto_perfil'] = null;
        } else {
            $feedback = ['tipo' => 'erro', 'msg' => 'Não foi possível remover a foto.'];
        }
        $up->close();
    }
}

// Caminho para exibição
$srcFoto = '';

if (!empty($user['foto_perfil']) && file_exists(__DIR__ . '/' . $user['foto_perfil'])) {
    $srcFoto = $user['foto_perfil'];
} else {
    $nomeUrl = urlencode($user['nome']);
    $srcFoto = "https://upload.wikimedia.org/wikipedia/pt/a/aa/Bart_Simpson_200px.png";
}

// -------------------------
// PROCESSAMENTO: DADOS (Nome/Senha)
// -------------------------
// Verifica se o post NÃO é de ação de foto (ou seja, é atualização de perfil)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['acao'])) {
    $novoNome = trim($_POST['nome'] ?? $user['nome']);
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $senhaNova = $_POST['senha_nova'] ?? '';
    $senhaConf = $_POST['senha_confirma'] ?? '';

    if ($novoNome === '' ) {
        $msg = "Informe um nome válido.";
    } else {
        $okNome = true;
        // Atualizar Nome
        if ($novoNome !== $user['nome']) {
            $updNome = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
            $updNome->bind_param("si", $novoNome, $userId);
            $okNome = $updNome->execute(); // CORRIGIDO: variavel era $upNome, agora $updNome
            $updNome->close();

            if ($okNome) {
                $_SESSION['usuario_nome'] = $novoNome;
            }
        }

        $okSenha = true;
        // Atualizar Senha
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
                    $updSenha = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                    $updSenha->bind_param("si", $hash, $userId);
                    $okSenha = $updSenha->execute();
                    $updSenha->close();

                    if (!$okSenha) {
                        $msg = "Erro ao atualizar a senha. Tente novamente.";
                    }
                }
            }
        }

        // Recarrega dados se tudo deu certo
        if ($okNome && $okSenha) {
            // CORREÇÃO DO ERRO FATAL AQUI:
            $stmt2 = $conn->prepare("SELECT id, nome, email, senha, foto_perfil FROM usuarios WHERE id = ? LIMIT 1");
            $stmt2->bind_param('i', $userId); // Sintaxe de objeto
            $stmt2->execute();                // Sintaxe de objeto
            $result2 = $stmt2->get_result();
            $user = $result2->fetch_assoc();
            $stmt2->close();

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
        <style>
            .msg-ok { color: green; font-weight: bold; }
            .msg-erro { color: red; font-weight: bold; }
            .avatar-img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc; }
        </style>
    </head>
    <body>
        <h1>Meu Perfil</h1>

        <div>
            <a href="index.php">Voltar</a> | 
            <a href="quiz.php">Quiz's</a> | 
            <a href="logout.php">Sair</a>
        </div>

        <br>

        <?php if (!empty($msg)): ?>
            <p class="<?= strpos($msg, 'sucesso') !== false ? 'msg-ok' : (strpos($msg, 'erro') !== false || strpos($msg, 'incorreta') !== false ? 'msg-erro' : '') ?>">
                <?= $msg ?>
            </p>
        <?php endif; ?>

        <?php if ($feedback): ?>
            <p class="<?= $feedback['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>">
                <?= htmlspecialchars($feedback['msg']) ?>
            </p>
        <?php endif; ?>

        <hr>

        <div style="margin-bottom: 30px; border: 1px solid #eee; padding: 15px; max-width: 400px;">
            <h3>Foto de Perfil</h3>
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="<?= htmlspecialchars($srcFoto) ?>" alt="Foto de Perfil" class="avatar-img">
            </div>
            
            
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="upload">
                <div style="margin-bottom: 10px;">
                    <input type="file" name="foto" accept="image/jpeg, image/png, image/webp" required>
                    <small style="display:block; color:#666;">JPG, PNG ou WEBP (Máx: 2MB)</small>
                </div>
                <button type="submit">Atualizar foto</button>
            </form>

            <?php if (!empty($user['foto_perfil'])): ?>
                <form method="post" style="margin-top: 10px;">
                    <input type="hidden" name="acao" value="remover">
                    <button type="submit" onclick="return confirm('Tem certeza que deseja remover sua foto?')" style="background-color: #ffdddd; border: 1px solid red; cursor: pointer;">
                        Remover foto atual
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <form method='POST'>
            <div>
                <h3>Dados Pessoais</h3>
                <div>
                    <p>Email: <strong><?= htmlspecialchars($user['email']) ?></strong></p>
                </div>
                
                <div>
                    <label for="nome">Nome:</label><br>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
                </div>

                <br>
                <hr>
                <h3>Segurança</h3>
                <p><small>Preencha abaixo apenas se quiser trocar a senha.</small></p>

                <div>
                    <label for="senha_atual">Senha Atual:</label><br>
                    <input type="password" id="senha_atual" name="senha_atual">
                </div>
                <div>
                    <br>
                    <label for="senha_nova">Nova senha:</label><br>
                    <input type="password" id="senha_nova" name="senha_nova" minlength="6">
                </div>
                <div>
                    <br>
                    <label for="senha_confirma">Confirmar nova senha:</label><br>
                    <input type="password" id="senha_confirma" name="senha_confirma" minlength="6">
                </div>
            </div>

            <div>
                <br>
                <button type="submit" style="padding: 10px 20px; font-size: 16px;">Salvar Dados</button>
            </div>
        </form>
    </body>
</html>