<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mini Projeto - Quiz</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Quiz de Filmes</h1>

        <form action="resultado_filmes.php" method="POST">
            <h3>1) Qual filme foi o primeiro da história a ganhar 11 Oscars? <br>
            <input type="radio" name="q1" value="b"> Ben Hur.<br>
            <input type="radio" name="q1" value="c"> Titanic. <br>
            <input type="radio" name="q1" value="d"> O Senhor dos Aneis: O Retorno do Rei. <br>

            <h3>2) Quem dirigiu o filme A Origem (Inception)?</h3>
            <input type="radio" name="q2" value="a"> Steven Spielberg. <br>
            <input type="radio" name="q2" value="b"> Martin Nolan.<br>
            <input type="radio" name="q2" value="c"> James Cameron. <br>
            <input type="radio" name="q2" value="d"> Quentin Tarantino. <br>

            <h3>3) Em qual filme o personagem principal diz a frase “Eu sou o rei do mundo!”? </h3>
            <input type="radio" name="q3" value="a"> Gladiador. <br>
            <input type="radio" name="q3" value="b"> Piratas do Caribe.<br>
            <input type="radio" name="q3" value="c"> Titanic <br>
            <input type="radio" name="q3" value="d"> O Lobo de Wall Street. <br>

            <h3>4) Qual desses filmes faz parte do Universo Cinematografico da Marvel?</h3>
            <input type="radio" name="q4" value="a"> Homem de Aco <br>
            <input type="radio" name="q4" value="b"> Mulher-Maravilha. <br>
            <input type="radio" name="q4" value="c"> Coringa. <br>
            <input type="radio" name="q4" value="d"> Pantera Negra. <br>

            <h3>5) No filme Caca-Fantasmas (Ghostbusters), qual e o lema dos protagonistas? </h3>
            <input type="radio" name="q5" value="a"> "Se tem medo, fuja"<br>
            <input type="radio" name="q5" value="b"> "Chame quem puder ajudar!"<br>
            <input type="radio" name="q5" value="c"> "Quem vai chamar?" <br>
            <input type="radio" name="q5" value="d"> "Eles voltaram!" <br>

            <br>
            <div class="btn">
                <button type="submit">Enviar Respostas</button>
            </div>
        </form>
    </body>
</html>