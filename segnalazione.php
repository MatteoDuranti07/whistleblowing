<?php
// avvia la sessione utente
session_start();

// Salva la pagina di provenienza solo alla prima visita (non dopo il redirect POST)
// controlla il tipo di richiesta
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    // Accetta solo index.html e login.html come pagine di provenienza
    if (preg_match('/(index\.html|login\.html)/', $referer)) {
        $_SESSION['pagina_precedente'] = $referer;
    }
}

// Configurazione connessione DB
$host     = 'localhost';
$dbname   = 'whistleblowing_db';
$user     = 'root';
$password = '';

// controlla il tipo di richiesta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testo = trim($_POST['descrizione'] ?? '');

    if (empty($testo)) {
        $_SESSION['stato'] = 'vuoto';
    } else {
        try {
// connessione al database con pdo
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
// prepara la query sql
            $stmt = $pdo->prepare("INSERT INTO segnalazioni (testo) VALUES (:testo)");
// esegue la query
            $stmt->execute([':testo' => $testo]);
            $_SESSION['stato'] = 'ok';
        } catch (PDOException $e) {
            $_SESSION['stato'] = 'errore';
        }
    }
// reindirizza l'utente
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$stato = $_SESSION['stato'] ?? '';
unset($_SESSION['stato']);

$pagina_precedente = $_SESSION['pagina_precedente'] ?? 'index.html';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Segnalazione Anonima</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="top-bar">
  <a href="<?= htmlspecialchars($pagina_precedente) ?>" class="back-btn">←</a>
</div>

<header class="main-header">
  <h1>Segnalazione Anonima</h1>
</header>

<main class="content-wrapper">
<!-- modulo principale -->
  <form id="moduloSegnalazione" class="card-form" method="POST" action="">

    <div class="form-group">
      <label for="descrizione">Descrizione della problematica:</label>
      <textarea id="descrizione" name="descrizione" rows="6" required autocomplete="off"></textarea>
    </div>

    <div class="button-container">
      <button type="submit" class="button donate-button">
        Invia anonimante
      </button>
    </div>

  </form>

  <p id="risultato" class="success-message">
    <?php if ($stato === 'ok'): ?>
      Segnalazione inviata con successo!
    <?php elseif ($stato === 'vuoto'): ?>
      <span style="color:#d32f2f;">Il testo della segnalazione non può essere vuoto.</span>
    <?php elseif ($stato === 'errore'): ?>
      <span style="color:#d32f2f;">Errore durante il salvataggio. Riprova.</span>
    <?php endif; ?>
  </p>
</main>

<!-- script javascript -->
<script>
  window.addEventListener('pageshow', function() {
    document.getElementById('descrizione').value = '';
  });
</script>

</body>
</html>