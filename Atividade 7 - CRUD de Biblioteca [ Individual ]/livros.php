<?php
include 'conexao.php';

// Funções para criar, listar, atualizar e excluir livros
function listarLivros($conn) {
    $stmt = $conn->query("SELECT * FROM livros");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarLivro($conn, $titulo, $genero, $ano_publicacao, $id_autor) {
    $stmt = $conn->prepare("INSERT INTO livros (titulo, genero, ano_publicacao, id_autor) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titulo, $genero, $ano_publicacao, $id_autor]);
}

// Adicione funções para atualizar e excluir livros conforme necessário
?>
