<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mini Projeto - Quiz</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Quiz de Gerais de Tecnologia</h1>

        <form action="resultado_tecnologia.php" method="POST">
            <h3>1) Qual é a capital do Brasil?</h3>
            <input type="radio" name="q1" value="a"> São Paulo <br>
            <input type="radio" name="q1" value="b"> Rio de Janeiro <br>
            <input type="radio" name="q1" value="c"> Brasília <br>
            <input type="radio" name="q1" value="d"> Belo Horizonte <br>

            <h3>2) Quem pintou a Monalisa? </h3>
            <input type="radio" name="q2" value="a"> Michelangelo <br>
            <input type="radio" name="q2" value="b"> Leonardo da Vinci <br>
            <input type="radio" name="q2" value="c"> Picasso <br>
            <input type="radio" name="q2" value="d"> Van Gogh <br>

            <h3>3) Qual é o maior planeta do Sistema Solar? </h3>
            <input type="radio" name="q3" value="a"> Marte <br>
            <input type="radio" name="q3" value="b"> Júpiter <br>
            <input type="radio" name="q3" value="c"> Saturno <br>
            <input type="radio" name="q3" value="d"> Sol <br>

            <h3>4) Em que continente fica o Egito? </h3>
            <input type="radio" name="q4" value="a"> Europa <br>
            <input type="radio" name="q4" value="b"> Ásia <br>
            <input type="radio" name="q4" value="c"> África <br>
            <input type="radio" name="q4" value="d"> América <br>

            <h3>5) Qual é a linguagem de programação usada no lado do servidor? </h3>
            <input type="radio" name="q5" value="a"> HTML <br>
            <input type="radio" name="q5" value="b"> CSS <br>
            <input type="radio" name="q5" value="c"> PHP <br>
            <input type="radio" name="q5" value="d"> JavaScript <br>

            <br>
            <div class="btn">
                <button type="submit">Enviar Respostas</button>
            </div>
        </form>
    </body>
</html>