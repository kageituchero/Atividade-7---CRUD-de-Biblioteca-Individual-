<?php
include 'conexao.php';

// Funções CRUD para empréstimos (já existentes)
function listarEmprestimos($conn) {
    $stmt = $conn->query("SELECT * FROM emprestimos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarEmprestimo($conn, $id_livro, $id_leitor, $data_emprestimo, $data_devolucao) {
    $stmt = $conn->prepare("INSERT INTO emprestimos (id_livro, id_leitor, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_livro, $id_leitor, $data_emprestimo, $data_devolucao]);
}

function listarEmprestimosAtivos($conn) {
    $stmt = $conn->query("SELECT * FROM emprestimos WHERE data_devolucao IS NULL");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function listarEmprestimosConcluidos($conn) {
    $stmt = $conn->query("SELECT * FROM emprestimos WHERE data_devolucao IS NOT NULL");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function listarLivrosEmprestadosPorLeitor($conn, $id_leitor) {
    $stmt = $conn->prepare("SELECT * FROM emprestimos WHERE id_leitor = ?");
    $stmt->execute([$id_leitor]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- Início do Código HTML para exibir os empréstimos ---
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Empréstimos - Biblioteca CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Gerenciamento de Empréstimos</h1>
    <nav>
        <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="autores.php">Autores</a></li>
            <li><a href="livros.php">Livros</a></li>
            <li><a href="leitores.php">Leitores</a></li>
        </ul>
    </nav>

    <h2>Lista de Empréstimos</h2>
    <?php
    $emprestimos = listarEmprestimos($conn); // Ou listarEmprestimosAtivos(), etc.
    if ($emprestimos) {
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>ID Livro</th><th>ID Leitor</th><th>Data Empréstimo</th><th>Data Devolução</th></tr></thead>";
        echo "<tbody>";
        foreach ($emprestimos as $emprestimo) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($emprestimo['id']) . "</td>";
            echo "<td>" . htmlspecialchars($emprestimo['id_livro']) . "</td>";
            echo "<td>" . htmlspecialchars($emprestimo['id_leitor']) . "</td>";
            echo "<td>" . htmlspecialchars($emprestimo['data_emprestimo']) . "</td>";
            echo "<td>" . htmlspecialchars($emprestimo['data_devolucao'] ?? 'Em Aberto') . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Nenhum empréstimo encontrado.</p>";
    }

    // Lógica para criar empréstimo
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            criarEmprestimo($conn, $_POST['id_livro'], $_POST['id_leitor'], $_POST['data_emprestimo'], $_POST['data_devolucao']);
            header("Location: emprestimos.php");
            exit();
        }
    }
    ?>

    <h2>Registrar Novo Empréstimo</h2>
    <form method="POST" action="emprestimos.php">
        <input type="hidden" name="action" value="add">
        <label for="id_livro">ID do Livro:</label>
        <input type="number" id="id_livro" name="id_livro" required><br>
        <label for="id_leitor">ID do Leitor:</label>
        <input type="number" id="id_leitor" name="id_leitor" required><br>
        <label for="data_emprestimo">Data de Empréstimo:</label>
        <input type="date" id="data_emprestimo" name="data_emprestimo" value="<?php echo date('Y-m-d'); ?>" required><br>
        <label for="data_devolucao">Data de Devolução (opcional):</label>
        <input type="date" id="data_devolucao" name="data_devolucao"><br>
        <button type="submit">Registrar Empréstimo</button>
    </form>

</body>
</html>
