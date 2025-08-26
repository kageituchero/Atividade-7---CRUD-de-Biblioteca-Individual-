<?php
include 'conexao.php'; // Inclui a conexão com o banco de dados

// Funções CRUD para autores (já existentes no seu arquivo)
function listarAutores($conn) {
    $stmt = $conn->query("SELECT * FROM autores");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarAutor($conn, $nome, $nacionalidade, $ano_nascimento) {
    $stmt = $conn->prepare("INSERT INTO autores (nome, nacionalidade, ano_nascimento) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $nacionalidade, $ano_nascimento]);
}

function atualizarAutor($conn, $id, $nome, $nacionalidade, $ano_nascimento) {
    $stmt = $conn->prepare("UPDATE autores SET nome = ?, nacionalidade = ?, ano_nascimento = ? WHERE id = ?");
    $stmt->execute([$nome, $nacionalidade, $ano_nascimento, $id]);
}

function excluirAutor($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM autores WHERE id = ?");
    $stmt->execute([$id]);
}

// --- Início do Código HTML para exibir os autores ---
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Autores - Biblioteca CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Gerenciamento de Autores</h1>
    <nav>
        <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="livros.php">Livros</a></li>
            <li><a href="leitores.php">Leitores</a></li>
            <li><a href="emprestimos.php">Empréstimos</a></li>
        </ul>
    </nav>

    <h2>Lista de Autores</h2>
    <?php
    // Chama a função para listar autores e exibe-os em uma tabela
    $autores = listarAutores($conn);
    if ($autores) {
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Nome</th><th>Nacionalidade</th><th>Ano de Nascimento</th><th>Ações</th></tr></thead>";
        echo "<tbody>";
        foreach ($autores as $autor) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($autor['id']) . "</td>";
            echo "<td>" . htmlspecialchars($autor['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($autor['nacionalidade']) . "</td>";
            echo "<td>" . htmlspecialchars($autor['ano_nascimento']) . "</td>";
            echo "<td>";
            echo "<a href='?action=edit&id=" . htmlspecialchars($autor['id']) . "'>Editar</a> | ";
            echo "<a href='?action=delete&id=" . htmlspecialchars($autor['id']) . "' onclick='return confirm(\"Tem certeza que deseja excluir este autor?\")'>Excluir</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Nenhum autor encontrado.</p>";
    }

    // Lógica para criar/atualizar/excluir autores (exemplo simplificado)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            criarAutor($conn, $_POST['nome'], $_POST['nacionalidade'], $_POST['ano_nascimento']);
            header("Location: autores.php"); // Redireciona para evitar reenvio do formulário
            exit();
        } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
            atualizarAutor($conn, $_POST['id'], $_POST['nome'], $_POST['nacionalidade'], $_POST['ano_nascimento']);
            header("Location: autores.php");
            exit();
        }
    } elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        excluirAutor($conn, $_GET['id']);
        header("Location: autores.php");
        exit();
    }
    ?>

    <h2>Adicionar Novo Autor</h2>
    <form method="POST" action="autores.php">
        <input type="hidden" name="action" value="add">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br>
        <label for="nacionalidade">Nacionalidade:</label>
        <input type="text" id="nacionalidade" name="nacionalidade"><br>
        <label for="ano_nascimento">Ano de Nascimento:</label>
        <input type="number" id="ano_nascimento" name="ano_nascimento"><br>
        <button type="submit">Adicionar Autor</button>
    </form>

    <?php
    // Formulário de edição (aparece se 'action=edit' e 'id' estiverem na URL)
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id_autor_editar = $_GET['id'];
        // Você precisaria de uma função para buscar um único autor pelo ID aqui
        // Por simplicidade, vamos assumir que você tem os dados para preencher o formulário
        // $autor_para_editar = buscarAutorPorId($conn, $id_autor_editar);
        // if ($autor_para_editar) {
        ?>
        <h2>Editar Autor</h2>
        <form method="POST" action="autores.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_autor_editar); ?>">
            <label for="edit_nome">Nome:</label>
            <input type="text" id="edit_nome" name="nome" value="" required><br> <!-- Preencher com dados do autor -->
            <label for="edit_nacionalidade">Nacionalidade:</label>
            <input type="text" id="edit_nacionalidade" name="nacionalidade" value=""><br> <!-- Preencher com dados do autor -->
            <label for="edit_ano_nascimento">Ano de Nascimento:</label>
            <input type="number" id="edit_ano_nascimento" name="ano_nascimento" value=""><br> <!-- Preencher com dados do autor -->
            <button type="submit">Atualizar Autor</button>
        </form>
        <?php
        // }
    }
    ?>

</body>
</html>
