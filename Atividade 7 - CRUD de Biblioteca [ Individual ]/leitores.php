<?php
include 'conexao.php';

// Funções CRUD para leitores (já existentes)
function listarLeitores($conn) {
    $stmt = $conn->query("SELECT * FROM leitores");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarLeitor($conn, $nome, $email, $telefone) {
    $stmt = $conn->prepare("INSERT INTO leitores (nome, email, telefone) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $telefone]);
}

function atualizarLeitor($conn, $id, $nome, $email, $telefone) {
    $stmt = $conn->prepare("UPDATE leitores SET nome = ?, email = ?, telefone = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $telefone, $id]);
}

function excluirLeitor($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM leitores WHERE id = ?");
    $stmt->execute([$id]);
}

// --- Início do Código HTML para exibir os leitores ---
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Leitores - Biblioteca CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Gerenciamento de Leitores</h1>
    <nav>
        <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="autores.php">Autores</a></li>
            <li><a href="livros.php">Livros</a></li>
            <li><a href="emprestimos.php">Empréstimos</a></li>
        </ul>
    </nav>

    <h2>Lista de Leitores</h2>
    <?php
    $leitores = listarLeitores($conn);
    if ($leitores) {
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr></thead>";
        echo "<tbody>";
        foreach ($leitores as $leitor) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($leitor['id']) . "</td>";
            echo "<td>" . htmlspecialchars($leitor['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($leitor['email']) . "</td>";
            echo "<td>" . htmlspecialchars($leitor['telefone']) . "</td>";
            echo "<td>";
            echo "<a href='?action=edit&id=" . htmlspecialchars($leitor['id']) . "'>Editar</a> | ";
            echo "<a href='?action=delete&id=" . htmlspecialchars($leitor['id']) . "' onclick='return confirm(\"Tem certeza que deseja excluir este leitor?\")'>Excluir</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Nenhum leitor encontrado.</p>";
    }

    // Lógica para criar/atualizar/excluir leitores
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            criarLeitor($conn, $_POST['nome'], $_POST['email'], $_POST['telefone']);
            header("Location: leitores.php");
            exit();
        } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
            atualizarLeitor($conn, $_POST['id'], $_POST['nome'], $_POST['email'], $_POST['telefone']);
            header("Location: leitores.php");
            exit();
        }
    } elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        excluirLeitor($conn, $_GET['id']);
        header("Location: leitores.php");
        exit();
    }
    ?>

    <h2>Adicionar Novo Leitor</h2>
    <form method="POST" action="leitores.php">
        <input type="hidden" name="action" value="add">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone"><br>
        <button type="submit">Adicionar Leitor</button>
    </form>

    <?php
    // Formulário de edição
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id_leitor_editar = $_GET['id'];
        // Você precisaria de uma função para buscar um único leitor pelo ID aqui
        ?>
        <h2>Editar Leitor</h2>
        <form method="POST" action="leitores.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_leitor_editar); ?>">
            <label for="edit_nome">Nome:</label>
            <input type="text" id="edit_nome" name="nome" value="" required><br>
            <label for="edit_email">Email:</label>
            <input type="email" id="edit_email" name="email" value="" required><br>
            <label for="edit_telefone">Telefone:</label>
            <input type="text" id="edit_telefone" name="telefone" value=""><br>
            <button type="submit">Atualizar Leitor</button>
        </form>
        <?php
    }
    ?>

</body>
</html>
