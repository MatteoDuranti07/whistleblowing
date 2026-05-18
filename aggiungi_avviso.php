<?php
// avvia la sessione utente
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'admin') {
// reindirizza l'utente
    header("Location: login.html");
    exit;
}

$host     = 'localhost';
$dbname   = 'whistleblowing_db';
$dbuser   = 'root';
$password = '';

// controlla il tipo di richiesta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = trim($_POST['titolo'] ?? '');
    $testo  = trim($_POST['testo'] ?? '');

    if (empty($titolo) || empty($testo)) {
        $_SESSION['stato'] = 'vuoto';
    } else {
        try {
// connessione al database con pdo
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $dbuser,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
// prepara la query sql
            $stmt = $pdo->prepare("INSERT INTO avvisi (titolo, testo, id_autore) VALUES (?, ?, ?)");
// esegue la query
            $stmt->execute([$titolo, $testo, $_SESSION['id']]);
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
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pubblica Avviso – TrustLine Digital</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-bar">
    <a href="admin.php" class="back-btn">←</a>
  </div>

  <header class="main-header">
    <h1>Pubblica Avviso</h1>
  </header>

  <main class="content-wrapper">

<!-- modulo principale -->
    <form class="card-form" method="POST">

      <div class="form-group">
        <label for="titolo">Titolo avviso</label>
        <input type="text" id="titolo" name="titolo"
               placeholder="Es. Aggiornamento policy aziendale" required>
      </div>

      <div class="form-group">
        <label for="testo">Testo dell'avviso</label>
        <textarea id="testo" name="testo" rows="7"
                  placeholder="Scrivi qui il contenuto dell'avviso..." required></textarea>
      </div>

      <div class="button-container">
        <button type="submit" class="button donate-button">Pubblica avviso</button>
      </div>

    </form>

    <p id="risultato" class="success-message">
      <?php if ($stato === 'ok'): ?>
        Avviso pubblicato con successo!
      <?php elseif ($stato === 'vuoto'): ?>
        <span style="color:#d32f2f;">Compila tutti i campi.</span>
      <?php elseif ($stato === 'errore'): ?>
        <span style="color:#d32f2f;">Errore durante il salvataggio. Riprova.</span>
      <?php endif; ?>
    </p>

  </main>

<!-- script javascript -->
  <script>
// funzione javascript
    window.addEventListener('pageshow', function () {
      document.getElementById('testo').value = '';
      document.getElementById('titolo').value = '';
    });
  </script>

  <script src="script.js"></script>
</body>
</html>