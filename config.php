<?php
// Conexão com o banco de dados.
// Este arquivo é incluído em todas as telas e cria a variável $pdo,
// que é usada para executar as consultas.

$host    = "localhost";
$banco   = "prosa_traco";
$usuario = "root";
$senha   = "";

try {
    // PDO é a extensão do PHP que conversa com o banco
    $pdo = new PDO("mysql:host={$host};dbname={$banco};charset=utf8mb4", $usuario, $senha);

    // avisa com erro sempre que uma consulta falhar
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // devolve todos os valores como texto, para o JSON da API ficar
    // sempre no mesmo formato (por isso o TypeScript converte depois)
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
} catch (PDOException $erro) {
    die("Erro ao conectar no banco de dados: " . $erro->getMessage());
}
