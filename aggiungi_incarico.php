<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$host     = 'localhost';
$dbname   = 'whistleblowing_db';
$dbuser   = 'root';
$password = '';

$utenti = [];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $stmt = $pdo->prepare("SELECT id, nome, username FROM utenti WHERE ruolo = 'utente' ORDER BY nome ASC");
    $stmt->execute();
    $utenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $utenti = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_utente   = intval($_POST['id_utente'] ?? 0);
    $titolo      = trim($_POST['titolo'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $scadenza    = trim($_POST['scadenza'] ?? '');
    $stato_inc   = $_POST['stato'] ?? 'in_attesa';
    $stati_validi = ['in_attesa', 'in_corso', 'completato', 'scaduto'];

    if (!$id_utente || empty($titolo) || empty($descrizione)) {
        $_SESSION['stato'] = 'vuoto';
    } elseif (!in_array($stato_inc, $stati_validi)) {
        $_SESSION['stato'] = 'errore';
    } else {
        try {
            $scadenza_val = $scadenza ?: null;
            $stmt = $pdo->prepare("
                INSERT INTO incarichi (id_utente, id_assegnato_da, titolo, descrizione, scadenza, stato)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_utente, $_SESSION['id'], $titolo, $descrizione, $scadenza_val, $stato_inc]);
            $_SESSION['stato'] = 'ok';
        } catch (PDOException $e) {
            $_SESSION['stato'] = 'errore';
        }
    }
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
  <title>Assegna Incarico – TrustLine Digital</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-bar">
    <a href="admin.php" class="back-btn">←</a>
  </div>

  <header class="main-header">
    <h1>Assegna Incarico</h1>
  </header>

  <main class="content-wrapper">

    <form class="card-form" method="POST" id="moduloIncarico">

      <?php if (empty($utenti)): ?>
        <p style="text-align:center; color:#888;">Nessun utente disponibile a cui assegnare un incarico.</p>
      <?php else: ?>

      <div class="form-group">
        <label for="id_utente">Assegna a</label>
        <select id="id_utente" name="id_utente" required>
          <option value="">— Seleziona utente —</option>
          <?php foreach ($utenti as $u): ?>
            <option value="<?= $u['id'] ?>">
              <?= htmlspecialchars($u['nome']) ?> (<?= htmlspecialchars($u['username']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="titolo">Titolo incarico</label>
        <input type="text" id="titolo" name="titolo"
               placeholder="Es. Revisione documenti Q2" required>
      </div>

      <div class="form-group">
        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" rows="6"
                  placeholder="Descrivi il compito da svolgere..." required></textarea>
      </div>

      <div class="form-group">
        <label for="scadenza">Scadenza <span style="font-weight:400; color:#888;">(facoltativa)</span></label>
        <input type="text" id="scadenza" name="scadenza"
               placeholder="AAAA-MM-GG"
               onfocus="this.type='date'"
               onblur="if(!this.value) this.type='text'">
      </div>

      <div class="form-group">
        <label for="stato">Stato iniziale</label>
        <select id="stato" name="stato">
          <option value="in_attesa">In attesa</option>
          <option value="in_corso">In corso</option>
          <option value="completato">Completato</option>
          <option value="scaduto">Scaduto</option>
        </select>
      </div>

      <div class="button-container">
        <button type="submit" class="button donate-button">Assegna incarico</button>
      </div>

      <?php endif; ?>

    </form>

    <p id="risultato" class="success-message">
      <?php if ($stato === 'ok'): ?>
        Incarico assegnato con successo!
      <?php elseif ($stato === 'vuoto'): ?>
        <span style="color:#d32f2f;">Compila tutti i campi obbligatori.</span>
      <?php elseif ($stato === 'errore'): ?>
        <span style="color:#d32f2f;">Errore durante il salvataggio. Riprova.</span>
      <?php endif; ?>
    </p>

  </main>

  <script>
    window.addEventListener('pageshow', function () {
      document.getElementById('moduloIncarico').reset();
    });
  </script>

  <script src="script.js"></script>
</body>
</html>