<?php
$servername = "localhost"; // Acesso ao Localhost
$username = "root"; // Usuário padrão do PHPMYADMIN
$password = ""; // Senha (no XAMPP é vazio)
$dbname = "quiz_db"; // Nome do Banco de Dados criado no PHPMYADMIN

// Criando a conexão com phpMyAdmin
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação da conexão
if ($conn -> connect_error) {
    die("Falha na conexão: ". $conn->connect_error);
}
?>