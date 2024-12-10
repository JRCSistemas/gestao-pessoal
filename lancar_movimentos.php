<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestao_financeira";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Processa o formulário de lançamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $valor = floatval($_POST['valor']);
    $data = date('Y-m-d H:i:s');

    $sql = "INSERT INTO movimentos (descricao, tipo, valor, data) VALUES ('$descricao', '$tipo', $valor, '$data')";

    if ($conn->query($sql) === TRUE) {
        $mensagem = "Lançamento realizado com sucesso!";
    } else {
        $mensagem = "Erro ao lançar: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançar Movimentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0277bd;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #01579b;
        }
        input, select, button {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        button {
            background-color: #0277bd;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #01579b;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            background-color: #0277bd;
            border-radius: 5px;
            margin: 5px 0;
        }
        .btn:hover {
            background-color: #01579b;
        }
        .mensagem {
            color: green;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn">Voltar à Página Inicial</a>
        <h1>Lançar Movimentos</h1>
        <?php if (isset($mensagem)): ?>
            <p class="mensagem"><?= $mensagem ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="descricao">Descrição</label>
            <input type="text" id="descricao" name="descricao" required>

            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo" required>
                <option value="">Selecione</option>
                <option value="entrada">Entrada</option>
                <option value="despesa">Despesa</option>
            </select>

            <label for="valor">Valor</label>
            <input type="number" id="valor" name="valor" step="0.01" required>

            <button type="submit">Lançar</button>
        </form>
    </div>
</body>
</html>
