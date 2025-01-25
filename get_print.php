<?php
// Configurações do banco de dados
$host = '131.196.172.2'; // Endereço do servidor
$user = 'studiocl_stdclip'; // Nome de usuário do banco
$password = 'mRI1IeT=Kqr@'; // Senha do banco
$database = 'studiocl_studioclipagem'; // Nome do banco de dados

// Criar a conexão
$conn = new mysqli($host, $user, $password, $database);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definir o charset (opcional, mas recomendado)
$conn->set_charset("utf8");

// Agora você pode usar a variável $conn para executar consultas
$sql = "SELECT id, link_arquivo FROM app_web WHERE id_knewin < 1000000 AND link_arquivo IS NULL";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Nome: " . $row['nome'] . "<br>";
    }
} else {
    echo "Nenhum resultado encontrado.";
}

// Fechar a conexão (opcional, pois o PHP fecha automaticamente no final do script)
$conn->close();

?>