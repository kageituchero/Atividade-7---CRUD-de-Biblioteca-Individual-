<?php
include 'conexao.php';

// Funções CRUD para livros (já existentes)
function listarLivros($conn, $filtro = null) {
    $query = "SELECT * FROM livros";
    if ($filtro) {
        $conditions = [];
        $params = [];
        if (!empty($filtro['genero'])) {
            $conditions[] = "genero LIKE :genero";
            $params[':genero'] = '%' . $filtro['genero'] . '%';
        }
        if (!empty($filtro['ano'])) {
            $conditions[] = "ano_publicacao = :ano";
            $params[':ano'] = $filtro['ano'];
        }
        if (!empty($filtro['id_autor'])) {
            $conditions[] = "id_autor = :id_autor";
            $params[':id_autor'] = $filtro['id_autor'];
        }
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" OR ", $conditions);
        }
    }
    $stmt = $conn->prepare($query);
    $stmt->execute($params ?? []);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarLivro($conn, $titulo, $genero, $ano_publicacao, $id_autor) {
    $ano_atual = date("Y");
    if ($ano_publicacao > 1500 && $ano_publicacao <= $ano_atual) {
        $stmt = $conn->prepare("INSERT INTO livros (titulo, genero, ano_publicacao, id_autor) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $genero, $ano_publicacao, $id_autor]);
    } else {
        throw new Exception("Ano de publicação inválido.");
    }
}

function atualizarLivro($conn, $id, $titulo, $genero, $ano_publicacao, $id_autor) {
    $stmt = $conn->prepare("UPDATE livros SET titulo = ?, genero = ?, ano_publicacao = ?, id_autor = ? WHERE id = ?");
    $stmt->execute([$titulo, $genero, $ano_publicacao, $id_autor, $id]);
}

function excluirLivro($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM livros WHERE id = ?");
    $stmt->execute([$id]);
}

// A função listarLivrosComFiltro já existe, mas a listarLivros acima foi ajustada para lidar com filtros também.
// Se quiser usar a paginação, você precisará de mais lógica para calcular o total de páginas e exibir os links de paginação.

// --- Início do Código HTML para exibir os livros ---
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Livros - Biblioteca CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Gerenciamento de Livros</h1>
    <nav>
        <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="autores.php">Autores</a></li>
            <li><a href="leitores.php">Leitores</a></li>
            <li><a href="emprestimos.php">Empréstimos</a></li>
        </ul>
    </nav>

    <h2>Filtrar Livros</h2>
    <form method="GET" action="livros.php">
        <label for="genero_filtro">Gênero:</label>
        <input type="text" id="genero_filtro" name="genero" value="<?php echo htmlspecialchars($_GET['genero'] ?? ''); ?>"><br>
        <label for="ano_filtro">Ano de Publicação:</label>
        <input type="number" id="ano_filtro" name="ano" value="<?php echo htmlspecialchars($_GET['ano'] ?? ''); ?>"><br>
        <label for="autor_filtro">ID do Autor:</label>
        <input type="number" id="autor_filtro" name="id_autor" value="<?php echo htmlspecialchars($_GET['id_autor'] ?? ''); ?>"><br>
        <button type="submit">Filtrar</button>
        <a href="livros.php"><button type="button">Limpar Filtro</button></a>
    </form>

    <h2>Lista de Livros</h2>
    <?php
    $filtro = [
        'genero' => $_GET['genero'] ?? '',
        'ano' => $_GET['ano'] ?? '',
        'id_autor' => $_GET['id_autor'] ?? ''
    ];
    $livros = listarLivros($conn, $filtro);

    if ($livros) {
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Título</th><th>Gênero</th><th>Ano Publicação</th><th>ID Autor</th><th>Ações</th></tr></thead>";
        echo "<tbody>";
        foreach ($livros as $livro) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($livro['id']) . "</td>";
            echo "<td>" . htmlspecialchars($livro['titulo']) . "</td>";
            echo "<td>" . htmlspecialchars($livro['genero']) . "</td>";
            echo "<td>" . htmlspecialchars($livro['ano_publicacao']) . "</td>";
            echo "<td>" . htmlspecialchars($livro['id_autor']) . "</td>";
            echo "<td>";
            echo "<a href='?action=edit&id=" . htmlspecialchars($livro['id']) . "'>Editar</a> | ";
            echo "<a href='?action=delete&id=" . htmlspecialchars($livro['id']) . "' onclick='return confirm(\"Tem certeza que deseja excluir este livro?\")'>Excluir</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>Nenhum livro encontrado com os filtros aplicados.</p>";
    }

    // Lógica para criar/atualizar/excluir livros
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            try {
                criarLivro($conn, $_POST['titulo'], $_POST['genero'], $_POST['ano_publicacao'], $_POST['id_autor']);
                header("Location: livros.php");
                exit();
            } catch (Exception $e) {
                echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
            atualizarLivro($conn, $_POST['id'], $_POST['titulo'], $_POST['genero'], $_POST['ano_publicacao'], $_POST['id_autor']);
            header("Location: livros.php");
            exit();
        }
    } elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        excluirLivro($conn, $_GET['id']);
        header("Location: livros.php");
        exit();
    }
    ?>

    <h2>Adicionar Novo Livro</h2>
    <form method="POST" action="livros.php">
        <input type="hidden" name="action" value="add">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required><br>
        <label for="genero">Gênero:</label>
        <input type="text" id="genero" name="genero" required><br>
        <label for="ano_publicacao">Ano de Publicação:</label>
        <input type="number" id="ano_publicacao" name="ano_publicacao" required><br>
        <label for="id_autor">ID do Autor:</label>
        <input type="number" id="id_autor" name="id_autor" required><br>
        <button type="submit">Adicionar Livro</button>
    </form>

    <?php
    // Formulário de edição
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id_livro_editar = $_GET['id'];
        // Você precisaria de uma função para buscar um único livro pelo ID aqui
        ?>
        <h2>Editar Livro</h2>
        <form method="POST" action="livros.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_livro_editar); ?>">
            <label for="edit_titulo">Título:</label>
            <input type="text" id="edit_titulo" name="titulo" value="" required><br>
            <label for="edit_genero">Gênero:</label>
            <input type="text" id="edit_genero" name="genero" value="" required><br>
            <label for="edit_ano_publicacao">Ano de Publicação:</label>
            <input type="number" id="edit_ano_publicacao" name="ano_publicacao" value="" required><br>
            <label for="edit_id_autor">ID do Autor:</label>
            <input type="number" id="edit_id_autor" name="id_autor" value="" required><br>
            <button type="submit">Atualizar Livro</button>
        </form>
        <?php
    }
    ?>

</body>
</html>
