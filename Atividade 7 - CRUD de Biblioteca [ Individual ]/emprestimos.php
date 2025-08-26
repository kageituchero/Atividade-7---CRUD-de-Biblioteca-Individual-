<?php
include 'conexao.php';

// Funções para criar, listar, atualizar e excluir empréstimos
function listarEmprestimos($conn) {
    $stmt = $conn->query("SELECT * FROM emprestimos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function criarEmprestimo($conn, $id_livro, $id_leitor, $data_emprestimo, $data_devolucao) {
    $stmt = $conn->prepare("INSERT INTO emprestimos (id_livro, id_leitor, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_livro, $id_leitor, $data_emprestimo, $data_devolucao]);
}

// Adicione funções para atualizar e excluir empréstimos conforme necessário
?>
