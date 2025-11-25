<?php
$servername = "localhost";
$bancodedados = "questionqueue";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password, $bancodedados);

// Receber dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$idade = $_POST['idade'];

// Preparar e executar a query
$sql = "INSERT INTO usuarios (nome, email, idade) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $nome, $email, $idade);

if ($stmt->execute()) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

// Fechar conexão
$stmt->close();
$conn->close();
?>