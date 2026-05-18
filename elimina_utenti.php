<?php
// avvia la sessione utente
session_start();

if (
    !isset($_SESSION['id']) ||
    !isset($_SESSION['ruolo']) ||
    $_SESSION['ruolo'] !== 'admin'
) {

// restituisce una risposta json
    echo json_encode([
        'success' => false,
        'message' => 'Accesso negato'
    ]);

    exit;
}

// reindirizza l'utente
header('Content-Type: application/json');

// connessione al database
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "whistleblowing_db"
);

if ($conn->connect_error) {

// restituisce una risposta json
    echo json_encode([
        'success' => false,
        'message' => 'Errore connessione database'
    ]);

    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id'])
) {

    $id = (int) $_POST['id'];

// prepara la query sql
    $stmt = $conn->prepare("
        DELETE FROM utenti
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

// esegue la query
    if ($stmt->execute()) {

// restituisce una risposta json
        echo json_encode([
            'success' => true
        ]);

    } else {

// restituisce una risposta json
        echo json_encode([
            'success' => false,
            'message' => 'Errore eliminazione'
        ]);
    }

    $stmt->close();

} else {

// restituisce una risposta json
    echo json_encode([
        'success' => false,
        'message' => 'Richiesta non valida'
    ]);
}

$conn->close();
?>