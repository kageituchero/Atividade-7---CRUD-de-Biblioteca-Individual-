<?php
include 'conexao.php';

// Funções para criar, listar, atualizar e excluir autores
function listarAutores($conn) {
    $stmt = $conn->query("SELECT * FROM autores");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarAutor($conn, $nome, $nacionalidade, $ano_nascimento) {
    $stmt = $conn->prepare("INSERT INTO autores (nome, nacionalidade, ano_nascimento) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $nacionalidade, $ano_nascimento]);
}

// Adicione funções para atualizar e excluir autores conforme necessário
?>
