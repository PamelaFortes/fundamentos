<?php
include("conexao.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // Armazenar os dados digitados pelo usuário no formulário de cadastro
    $sql = "INSERT INTO usuarios(nome, email, senha) VALUES ('$nome', '$email', '$senha')";

    // Validar se os dados foram cadastrados
    if ($conn -> query($sql) === TRUE) {
        echo "Cadastro realizado com sucesso!<a href='index.php'>Fazer Login</a>";
    } else {
        echo "Erro: ". $conn -> error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastrar - Mini Projeto - Quiz</title>
    </head>
    <body>
        <h1>Cadastro de Usuário</h1>
        <form method="POST">
            <label>Nome:</label><br>
            <input type="text" name="nome" required><br><br>

            <label>E-mail:</label><br>
            <input type="email" name="email" required><br><br>

            <label>Senha</label><br>
            <input type="password" name="senha" required><br><br>

            <button type="submit">Cadastrar</button>
        </form>
    </body>
</html>