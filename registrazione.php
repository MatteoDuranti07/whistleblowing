<?php
// reindirizza l'utente
header('Content-Type: application/json');

$host   = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "whistleblowing_db";

// connessione al database
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "Errore di connessione al database."]);
    exit;
}

$nome      = trim($_POST['nome'] ?? '');
$username  = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$conferma  = $_POST['conferma'] ?? '';

// Campi obbligatori
if (empty($nome) || empty($username) || empty($email) || empty($password) || empty($conferma)) {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "Tutti i campi sono obbligatori."]);
    exit;
}

// Validazione email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "Email non valida."]);
    exit;
}

// Lunghezza minima password
if (strlen($password) < 8) {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "La password deve avere almeno 8 caratteri."]);
    exit;
}

// Conferma password
if ($password !== $conferma) {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "Le password non coincidono."]);
    exit;
}

// Controlla se username o email esistono
// prepara la query sql
$check = $conn->prepare("SELECT id FROM utenti WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
// esegue la query
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "Username o email già in uso."]);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Hash password
// crea la password criptata
$hash = password_hash($password, PASSWORD_DEFAULT);

// INSERT con email
// prepara la query sql
$stmt = $conn->prepare("INSERT INTO utenti (nome, username, email, password, ruolo) VALUES (?, ?, ?, ?, 'utente')");
$stmt->bind_param("ssss", $nome, $username, $email, $hash);

// esegue la query
if ($stmt->execute()) {

    require 'mail.php';
    emailRegistrazione($email, $nome);

// restituisce una risposta json
    echo json_encode(["success" => true, "message" => "Registrazione completata! Verrai reindirizzato al login."]);

} else {
// restituisce una risposta json
    echo json_encode(["success" => false, "message" => "Errore durante la registrazione. Riprova."]);
}

$stmt->close();
$conn->close();
?>