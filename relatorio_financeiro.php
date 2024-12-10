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

// Deletar dado se solicitado
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $senhaAtual = $_POST['senha_atual'] ?? null;

    // Busca a senha salva no banco
    $sqlSenha = "SELECT valor FROM configuracoes WHERE chave = 'senha_admin'";
    $resultSenha = $conn->query($sqlSenha);

    if ($resultSenha->num_rows > 0) {
        $rowSenha = $resultSenha->fetch_assoc();
        $senhaHash = $rowSenha['valor'];

        // Verifica se a senha atual está correta
        if (password_verify($senhaAtual, $senhaHash)) {
            $conn->query("DELETE FROM movimentos WHERE id = $delete_id");
            echo "<script>alert('Registro deletado com sucesso!'); window.location.href = 'relatorio_financeiro.php';</script>";
        } else {
            echo "<script>alert('Senha incorreta.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Senha não configurada.');</script>";
    }
}

// Obter todos os registros
$sql = "SELECT * FROM movimentos";
$result = $conn->query($sql);

// Calcular totais
$total_entradas = 0;
$total_despesas = 0;

$movimentos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $movimentos[] = $row;
        if ($row['tipo'] === 'entrada') {
            $total_entradas += $row['valor'];
        } elseif ($row['tipo'] === 'despesa') {
            $total_despesas += $row['valor'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        .totais {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .totais div {
            font-size: 18px;
            color: #01579b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #0288d1;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f1f1f1;
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
        .actions {
            display: flex;
            gap: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
        }
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn">Voltar à Página Inicial</a>
        <a href="imprimir_relatorio.php" class="btn" onclick="window.print(); return false;">Imprimir Relatório</a> <!-- Botão para imprimir -->
        <h1>Relatório Financeiro</h1>
        <div class="totais">
            <div>Total Entradas: R$ <?= number_format($total_entradas, 2, ',', '.') ?></div>
            <div>Total Despesas: R$ <?= number_format($total_despesas, 2, ',', '.') ?></div>
            <div style="color: <?= ($total_entradas - $total_despesas) < 0 ? 'red' : '#01579b' ?>; font-weight: bold;">Saldo Final: R$ <?= number_format($total_entradas - $total_despesas, 2, ',', '.') ?></div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th style="width: 160px;">Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentos as $movimento): ?>
                <tr>
                    <td><?= $movimento['id'] ?></td>
                    <td><?= htmlspecialchars($movimento['descricao']) ?></td>
                    <td><?= ucfirst($movimento['tipo']) ?></td>
                    <td>R$ <?= number_format($movimento['valor'], 2, ',', '.') ?></td>
                    <td><?= $movimento['data'] ?></td>
                    <td class="actions">
                        <button class="btn" onclick="openModal('delete', <?= $movimento['id'] ?>)">Deletar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form method="POST" action="?delete_id=" id="deleteForm">
                <input type="password" name="senha_atual" placeholder="Senha Atual" required>
                <button type="submit" class="btn">Confirmar</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(action, id) {
            document.getElementById('deleteForm').action = '?delete_id=' + id;
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }
    </script>
</body>
</html>
