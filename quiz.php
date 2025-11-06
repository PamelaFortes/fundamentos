<?php
session_start();
require_once "conexao.php";
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?erro=' . urlencode('Faça login para continuar.'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Escolha seu Quiz - Mini Projeto - Quiz</title>
</head>
<body>
    <div class="navbar">
        <h1>Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? $_SESSION['usuario_email']) ?>! </h1>
        
        <div id="menu">
            <a href="perfil.php">Meu Perfil</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>

    <div class="container-cards">
        <div class="cards">
            <div>
                <h2> Quiz de Conhecimentos Gerais</h2>
                <p>Perguntas variadas sobre fatos, cultura e curiosidades. </p>
            </div>
            
            <div id="start">
                    <a href="quiz_tecnologia.php">Começar</a>
            </div>
        </div>

        <div class="cards">
            <div>
                <h2> Quiz de Tecnologia</h2>
                <p>Operações básicas, lógica e raciocínio numérico. </p>
            </div>
                <div id="start">
                    <a href="quiz_tecnologia.php">Começar</a>
                </div>
        </div>
        <div class="cards">
            <div>
                <h2> Quiz de Filmes</h2>
                <p>Perguntas variadas sobre filmes. </p>
            </div>
                <div id="start">
                    <a href="quiz_filmes.php">Começar</a>
                </div>
        </div>
    </div>
</body>
</html>
