<?php
include 'conexao.php';

// Funções para criar, listar, atualizar e excluir leitores
function listarLeitores($conn) {
    $stmt = $conn->query("SELECT * FROM leitores");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarLeitor($conn, $nome, $email, $telefone) {
    $stmt = $conn->prepare("INSERT INTO leitores (nome, email, telefone) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $telefone]);
}

// Adicione funções para atualizar e excluir leitores conforme necessário
?>
