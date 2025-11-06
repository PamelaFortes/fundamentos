<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Resultado do Quiz</h1>

    <?php
    $gabarito = [
        "q1" => "a", // A alternativa correta é Ben Hur.
        "q2" => "c", // A alternativa correta é Christopher Nolan
        "q3" => "c", // A alternativa correta é Titanic.
        "q4" => "d", // A alternativa correta é Pantera Negra.
        "q5" => "c" // A alternativa correta é Quem voce vai chamar?
    ];
    
    $pontos = 0;

    foreach ($gabarito as $pergunta => $pergunta_armazenada) {
        if (isset($_POST[$pergunta]) && $_POST[$pergunta] === $pergunta_armazenada) {
            $pontos++;
        }
    }

    echo "<p>Você acertou <strong>$pontos</strong> de " . count($gabarito) . " perguntas</p>";

    if ($pontos == 5) {
        echo "<p>Excelente! Você é um gênio, foi muito bem!</p>";
    } elseif ($pontos >= 3) {
        echo "<p>Bom trabalho! Mas ainda pode melhorar.</p>";
    } else {
        echo "<p>Você consegue, continue estudando!</p>";
    }

    ?>
    <a href="index.php">Tentar novamente</a>
</body>
</html>