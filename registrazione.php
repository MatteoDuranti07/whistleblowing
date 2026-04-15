<?php
header('Content-Type: application/json');

$host   = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "whistleblowing_db";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Errore di connessione al database."]);
    exit;
}

$nome = trim($_POST['nome']     ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$conferma = trim($_POST['conferma'] ?? '');

// Campi obbligatori
if (empty($nome) || empty($username) || empty($password) || empty($conferma)) {
    echo json_encode(["success" => false, "message" => "Tutti i campi sono obbligatori."]);
    exit;
}

// Lunghezza minima password
if (strlen($password) < 8) {
    echo json_encode(["success" => false, "message" => "La password deve avere almeno 8 caratteri."]);
    exit;
}

// Conferma password
if ($password !== $conferma) {
    echo json_encode(["success" => false, "message" => "Le password non coincidono."]);
    exit;
}

// Controlla se lo username è già in uso
$check = $conn->prepare("SELECT id FROM utenti WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username già in uso. Scegline un altro."]);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Hash sicuro della password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Inserimento (ruolo sempre 'utente' per la registrazione pubblica)
$stmt = $conn->prepare("INSERT INTO utenti (nome, username, password, ruolo) VALUES (?, ?, ?, 'utente')");
$stmt->bind_param("sss", $nome, $username, $hash);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registrazione completata! Verrai reindirizzato al login."]);
} else {
    echo json_encode(["success" => false, "message" => "Errore durante la registrazione. Riprova."]);
}

$stmt->close();
$conn->close();
?>