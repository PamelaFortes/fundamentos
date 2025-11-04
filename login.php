<?php
session_start();
require_once "conexao.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$next = trim($_POST['next'] ?? 'perfil.php');

if ($email === '' || $senha === '') {
    header('Location: index.php?erro=' . urlencode("Preencha e-mail e senha"));
    exit;
}


$stmt = $conn->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result-> fetch_assoc()) {
    if (password_verify($senha, $row['senha'])) {
        $_SESSION['usuario_id'] = (int)$row['id'];
        $_SESSION['usuario_nome'] = $row['nome'];
        $_SESSION['usuario_email'] = $row['email'];

        header('Location: ' . $next);
        exit;
    }
}

header('Location: index.php?erro=' . urlencode('E-mail ou senha inválidos.'));
exit;