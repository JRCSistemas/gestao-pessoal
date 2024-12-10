<?php
include 'banco_de_dados/config.php';

// Variável para mensagens de erro ou sucesso
$mensagem = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['salvar'])) {
        $senhaAtual = $_POST['senha_atual'];
        $novaSenha = $_POST['nova_senha'];

        // Busca a senha salva no banco
        $sqlSenha = "SELECT valor FROM configuracoes WHERE chave = 'senha_admin'";
        $resultSenha = $conn->query($sqlSenha);

        if ($resultSenha->num_rows > 0) {
            $rowSenha = $resultSenha->fetch_assoc();
            $senhaHash = $rowSenha['valor'];

            // Verifica se a senha atual está correta
            if (password_verify($senhaAtual, $senhaHash)) {
                // Atualiza com a nova senha
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
                $sqlAtualizarSenha = "UPDATE configuracoes SET valor = '$novaSenhaHash' WHERE chave = 'senha_admin'";
                if ($conn->query($sqlAtualizarSenha) === TRUE) {
                    $mensagem = "<p style='color: #4CAF50;'>Senha alterada com sucesso!</p>";
                } else {
                    $mensagem = "<p style='color: #f44336;'>Erro ao alterar senha: " . $conn->error . "</p>";
                }
            } else {
                $mensagem = "<p style='color: #f44336;'>Senha atual incorreta.</p>";
            }
        } else {
            $mensagem = "<p style='color: #f44336;'>Senha não configurada no sistema.</p>";
        }
    } elseif (isset($_POST['cancelar'])) {
        // Redireciona para a página inicial
        header("Location: index.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e0f7fa;
        }
        form {
            background-color: #00796b;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #ffffff;
            width: 100%;
            max-width: 400px;
        }
        h1 {
            margin-bottom: 20px;
            color: #e0f7fa;
        }
        input {
            margin: 10px 0;
            padding: 10px;
            width: calc(100% - 20px);
            border: none;
            border-radius: 4px;
            outline: none;
        }
        input[type="password"] {
            background-color: #ffffff;
            color: #00796b;
        }
        button {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 4px;
            background-color: #004d40;
            color: #ffffff;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #00251a;
        }
        .voltar {
            margin-top: 10px;
            text-align: center;
        }
        .voltar a {
            color: #e0f7fa;
            text-decoration: none;
        }
        .voltar a:hover {
            text-decoration: underline;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            color: #004d40;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h1>Alterar Senha</h1>
        <?php echo $mensagem; ?>
        <input type="password" name="senha_atual" placeholder="Senha Atual" required>
        <input type="password" name="nova_senha" placeholder="Nova Senha" required>
        <button type="submit" name="salvar">Alterar Senha</button>
        <button type="submit" name="cancelar">Cancelar</button>
        <div class="voltar">
            <a href="index.php">Voltar à Página Principal</a>
        </div>
    </form>
    <footer>
        &copy; 2024 Jefferson de Castro - Empresa JRCSoluções - SISTEMAS
    </footer>
</body>
</html>
