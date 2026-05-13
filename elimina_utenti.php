<?php
session_start();

if (
    !isset($_SESSION['id']) ||
    !isset($_SESSION['ruolo']) ||
    $_SESSION['ruolo'] !== 'admin'
) {

    echo json_encode([
        'success' => false,
        'message' => 'Accesso negato'
    ]);

    exit;
}

header('Content-Type: application/json');

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "whistleblowing_db"
);

if ($conn->connect_error) {

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

    $stmt = $conn->prepare("
        DELETE FROM utenti
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        echo json_encode([
            'success' => true
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Errore eliminazione'
        ]);
    }

    $stmt->close();

} else {

    echo json_encode([
        'success' => false,
        'message' => 'Richiesta non valida'
    ]);
}

$conn->close();
?>