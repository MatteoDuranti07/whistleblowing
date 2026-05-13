<?php
session_start();

if (
    !isset($_SESSION['id']) ||
    !isset($_SESSION['ruolo']) ||
    $_SESSION['ruolo'] !== 'admin'
) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "whistleblowing_db"
);

if ($conn->connect_error) {
    die("Errore connessione database");
}

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$stmt = $conn->prepare("
    SELECT *
    FROM utenti
    WHERE id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$utente = $result->fetch_assoc();

if (!$utente) {
    die("Utente non trovato");
}

$msg = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST['password'] ?? '';
    $conferma = $_POST['conferma'] ?? '';

    if (empty($password) || empty($conferma)) {

        $msg = "Compila tutti i campi";
        $tipo = "errore";

    } elseif (strlen($password) < 8) {

        $msg = "La password deve avere almeno 8 caratteri";
        $tipo = "errore";

    } elseif ($password !== $conferma) {

        $msg = "Le password non coincidono";
        $tipo = "errore";

    } else {

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $update = $conn->prepare("
            UPDATE utenti
            SET password = ?
            WHERE id = ?
        ");

        $update->bind_param(
            "si",
            $hash,
            $id
        );

        if ($update->execute()) {

            require 'mail.php';

            emailCambioPassword(
                $utente['email'],
                $utente['nome']
            );

            header(
                "Location: gestione_utenti.php?modifica=1"
            );

            exit;

        } else {

            $msg = "Errore aggiornamento";
            $tipo = "errore";
        }

        $update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modifica Utente</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="top-bar">
  <a href="gestione_utenti.php" class="back-btn">←</a>
</div>

<header class="main-header">
  <div class="overlay">
    <h1>Modifica Utente</h1>
  </div>
</header>

<main class="content-wrapper">
  <div class="form-box">

    <div class="info-section">
      <h3>Dati utente</h3>

      <div class="info-row">
        <span class="info-label">Nome</span>
        <span class="info-value"><?= htmlspecialchars($utente['nome']) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Username</span>
        <span class="info-value"><?= htmlspecialchars($utente['username']) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Email</span>
        <span class="info-value"><?= htmlspecialchars($utente['email']) ?></span>
      </div>
    </div>

    <hr class="divider" />

    <form method="POST">

      <div class="form-group">
        <label>Ruolo:</label>
        <select name="ruolo">
          <option value="utente" <?= $utente['ruolo'] === 'utente' ? 'selected' : '' ?>>Utente</option>
          <option value="admin"  <?= $utente['ruolo'] === 'admin'  ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>

      <hr class="divider" />

      <p class="section-note">Lascia vuoto se non vuoi cambiare la password</p>

      <div class="form-group">
        <label>Nuova password:</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" />
          <button type="button" class="toggle-password" onclick="toggle('password')"></button>
        </div>
      </div>

      <div class="form-group">
        <label>Conferma password:</label>
        <div class="password-wrapper">
          <input type="password" id="conferma" name="conferma" />
          <button type="button" class="toggle-password" onclick="toggle('conferma')"></button>
        </div>
      </div>

      <?php if ($msg): ?>
        <p class="reg-msg <?= $tipo ?>">
          <?= htmlspecialchars($msg) ?>
        </p>
      <?php endif; ?>

      <div class="button-container">
        <button type="submit" class="button donate-button">Salva modifiche</button>
      </div>

    </form>
  </div>
</main>

<style>
.form-box {
  background: #fff;
  max-width: 600px;
  margin: 0 auto;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.info-section h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #1976d2;
  margin-bottom: 14px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.info-label {
  font-weight: 600;
  color: #555;
  font-size: 0.9rem;
}

.info-value {
  color: #222;
  font-size: 0.95rem;
}

.divider {
  border: none;
  border-top: 1px solid #dce3ed;
  margin: 24px 0;
}

.section-note {
  font-size: 0.85rem;
  color: #888;
  margin-bottom: 16px;
  font-style: italic;
}

.reg-msg {
  text-align: center;
  font-weight: 600;
  margin-top: 10px;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid;
}
.reg-msg.errore   { color: #c0392b; background: #fdecea; border-color: #e74c3c; }
.reg-msg.successo { color: #1e7e34; background: #eafaf1; border-color: #27ae60; }
</style>

<script>
function toggle(id) {
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>