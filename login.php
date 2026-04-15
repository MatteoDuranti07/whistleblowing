<?php
session_start();
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

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Campi obbligatori
if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Inserisci username e password."]);
    exit;
}

// Cerca l'utente per username
$stmt = $conn->prepare("SELECT id, nome, password, ruolo FROM utenti WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $utente = $result->fetch_assoc();

    if (password_verify($password, $utente['password'])) {
        // Salva i dati in sessione
        $_SESSION['id']    = $utente['id'];
        $_SESSION['nome']  = $utente['nome'];
        $_SESSION['ruolo'] = $utente['ruolo'];

        // Pagina di destinazione in base al ruolo
        $redirect = ($utente['ruolo'] === 'admin') ? 'admin.html' : 'utente.html';

        echo json_encode([
            "success"  => true,
            "redirect" => $redirect
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Password errata."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Utente non trovato."]);
}

$stmt->close();
$conn->close();
?>