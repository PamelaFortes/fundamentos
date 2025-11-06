<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mini Projeto - Quiz</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Quiz de Conhecimentos Gerais de Tecnologia</h1>

        <form action="resultado_tecnologia.php" method="POST">
            <h3>1) O que e computacao em nuvem (cloud computing)?</h3>
            <input type="radio" name="q1" value="a"> Um tipo de computador fisico usado em grandes empresas. <br>
            <input type="radio" name="q1" value="b"> Um software que melhora o desempenho do computador.<br>
            <input type="radio" name="q1" value="c"> O armazenamento e processamento de dados na internet por meio de servidores remotos. <br>
            <input type="radio" name="q1" value="d"> Uma tecnica de programacao para criar jogos digitais. <br>

            <h3>2) Qual a principal caracteristica da Inteligencia Artificial (IA)?</h3>
            <input type="radio" name="q2" value="a"> Funcionar apenas com comandos diretos do usuario. <br>
            <input type="radio" name="q2" value="b"> Reproduzir sons e imagens em alta qualidade.<br>
            <input type="radio" name="q2" value="c"> Simular o raciocinio humano e aprender com dados. <br>
            <input type="radio" name="q2" value="d"> Ser usada apenas em robos fisicos. <br>

            <h3>3) O que e phishing, uma ameaca comum na ciberseguranca? </h3>
            <input type="radio" name="q3" value="a"> Um tipo de virus que destroi o disco rigido. <br>
            <input type="radio" name="q3" value="b"> Uma tecnica para enganar pessoas e roubar dados pessoais.<br>
            <input type="radio" name="q3" value="c"> Um metodo de criptografia de informacoes <br>
            <input type="radio" name="q3" value="d"> Um sistema de protecao de redes. <br>

            <h3>4) Qual das opcoes abaixo e um exemplo de dispositivo da Internet das Coisas (IoT)?</h3>
            <input type="radio" name="q4" value="a"> Um pen drive comum. <br>
            <input type="radio" name="q4" value="b"> Um relogio inteligente conectado a Internet. <br>
            <input type="radio" name="q4" value="c"> Um monitor de computador. <br>
            <input type="radio" name="q4" value="d"> Um teclado sem fio. <br>

            <h3>5) Qual das opcoes abaixo representa um assistente virtual baseado em IA? </h3>
            <input type="radio" name="q5" value="a"> Photoshop <br>
            <input type="radio" name="q5" value="b"> Windows Defender <br>
            <input type="radio" name="q5" value="c"> Siri <br>
            <input type="radio" name="q5" value="d"> PowerPoint <br>

            <br>
            <div class="btn">
                <button type="submit">Enviar Respostas</button>
            </div>
        </form>
    </body>
</html>