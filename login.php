<?php

// avvia la sessione utente
session_start();

// imposta la risposta in formato json
header('Content-Type: application/json');

// dati connessione database
$host   = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "whistleblowing_db";

// connessione al database
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// controlla eventuali errori di connessione
if ($conn->connect_error) {

    // restituisce errore json
    echo json_encode([
        "success" => false,
        "message" => "Errore di connessione al database."
    ]);

    exit;
}

// recupera i dati inseriti nel login
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// controlla se i campi sono vuoti
if (empty($username) || empty($password)) {

    // restituisce messaggio di errore
    echo json_encode([
        "success" => false,
        "message" => "Inserisci username e password."
    ]);

    exit;
}

// prepara la query sql
$stmt = $conn->prepare("
    SELECT id, nome, email, password, ruolo
    FROM utenti
    WHERE username = ?
");

// collega il parametro username
$stmt->bind_param("s", $username);

// esegue la query
$stmt->execute();

// recupera il risultato
$result = $stmt->get_result();

// controlla se esiste un utente
if ($result->num_rows === 1) {

    // recupera i dati dell'utente
    $utente = $result->fetch_assoc();

    // verifica la password
    if (password_verify($password, $utente['password'])) {

        // salva i dati nella sessione
        $_SESSION['id']     = $utente['id'];
        $_SESSION['nome']   = $utente['nome'];
        $_SESSION['email']  = $utente['email'];
        $_SESSION['ruolo']  = $utente['ruolo'];

        // controlla il ruolo dell'utente
        if ($utente['ruolo'] === 'admin') {

            // reindirizza area admin
            $redirect = 'admin.php';

        } else {

            // reindirizza area utente
            $redirect = 'utente.php';
        }

        // login completato con successo
        echo json_encode([
            "success"  => true,
            "redirect" => $redirect
        ]);

    } else {

        // password errata
        echo json_encode([
            "success" => false,
            "message" => "Password errata."
        ]);
    }

} else {

    // utente non trovato
    echo json_encode([
        "success" => false,
        "message" => "Utente non trovato."
    ]);
}

// chiude la query
$stmt->close();

// chiude la connessione database
$conn->close();

?>