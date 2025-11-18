<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?erro=' . urlencode('Faça login para continuar.'));
    exit;
}

$userId = (int) $_SESSION['usuario_id'];
$msg = "";

$stmt = $conn -> prepare("SELECT id, nome, email, senha, foto_perfil FROM usuarios WHERE id = ? LIMIT 1");
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

//p armazenar imagens
$dirUpload = __DIR__ . 'upload/avatar';
$urlBase = 'uploads/avatar';
$tamanhoMaximo = 2 * 1024 * 1024;
$tiposExtensao = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];


//limpeza cache armazenamento de imagens

function limparArquivoAntigo(?string $caminhoRelativo): void{
    if (!$caminhoRelativo) return;
    $arquivo = __DIR__ . "/" . $caminhoRelativo;
    if(is_file($arquivo)){
        @unlink($arquivo);
    }
}
$feedback = null;

//envio de foto
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'upload'){
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK){
        $feedback = ['tipo' => 'erro', 'msg' => 'Falha no Upload. Selecione um arquivo valido.'];
    }else{
        $foto = $_FILES['foto'];
    } 

    //validaco do tipo da foto

    $mime = mime_content_type($foto['tmp_name']);
    if (!isset($tiposExtensao[$mime])) {
        $feedback = ['tipo' => 'erro', 'msg' => 'Formato invalido. Envie um arquivo com formato JPG, PNG ou WEBP.'];
    }elseif($foto['size'] > $tamanhoMaximo){
        $feedback = ['tipo' => 'erro', 'msg' => 'Arquivo muito grande (max 2MB).'];
    }else{
        //gera nome único: useID_timestamp.ext
        $ext = $tiposAceitos[$mime];
        $nomeUnico = 'u' . $usuarioID . '_' . time() . '.' . $ext;

        //Garante diretorio
        if (!is_dir($dirUpload)){
            @mkdir($dirUpload, 0775, true);
        }

        //Mover

        $destinoFs = $dirUpload . $nomeUnico;
        $destinoRel = $urlBase . $nomeUnico;
        if (move_uploaded_file($foto['tmp_name'], $destinoFs)) {
                // Apaga a antiga (se houver)
                limparArquivoAntigo($usuario['foto_perfil']);

                // Atualiza Banco de Dados
                $up = $conn -> prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
                $up -> bind_param("si", $destinoRel, $usuarioId);
                if ($up -> execute()) {
                    $feedback = ['tipo' => 'ok', 'msg' => 'Foto atualizada com sucesso!'];
                    $usuario['foto_perfil'] = $destinoRel;
                } else {
                    // Se falhar, remover o arquivo recém adicionado no banco
                    @unlink($destinoFs);
                    $feedback = ['tipo' => 'erro', 'msg' => 'Não foi possível salvar no banco. Tente novamente.'];
                }
                $up -> close();
            }else {
                $feedback = ['tipo' => 'erro', 'msg' => 'Não foi possível mover o arquivo. Verifique permissões.'];
        }
    }
}

// Remover o foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'remover') {
    // Apagando a foto da pasta uploads
    limparArquivoAntigo($usuario['foto_perfil']);
    
    // Limpar agora no Banco de Dados
    $up = $conn -> prepare("UPDATE usuarios SET foto_perfil = NULL WHERE id = ?");
    $up -> bind_param("i", $usuarioId);
    if ($up -> execute()) {
        $feedback = ['tipo' => 'ok', 'msg' => 'Foto removida.'];
        $usuario['foto_perfil'] = null;
    } else {
        $feedback = ['tipo' => 'erro', 'msg' => 'Não foi possível remover a foto.'];
    }
    $up -> close();
}

// Caminho será exibido (retorno para placeholder)
$srcFoto = $usuario['foto_perfil'] ?: 'assets/avatar-default.png';

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
                    <img src="<?= htmlspecialchars($srcFoto) ?>" alt="Foto de Perfil">
                    <p>Formatos: JPG/PNG/WEBP - Máx: 2MB</p>
                </div>

                <div>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="acao" value="upload">
                        <div>
                            <input type="file" name="foto" accept="image/jpeg, image/png, image/webp" required>
                        </div>
                        <div>
                            <button type="submit">Atualizar foto</button>
                            <?php if (!empty($usuario['foto_perfil'])): ?>
                                <button type="submit" name="acao" value="remover" onclick="return confirm('Remover sua foto atual?')">Remover foto</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
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

            <br>

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