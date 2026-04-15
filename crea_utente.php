<?php
session_start();

$msg = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conn = new mysqli("localhost", "root", "", "whistleblowing_db");

    if ($conn->connect_error) {
        $msg = "Errore connessione database";
        $tipo = "errore";
    } else {

        $nome      = trim($_POST['nome'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $password  = $_POST['password'] ?? '';
        $conferma  = $_POST['conferma'] ?? '';
        $ruolo     = $_POST['ruolo'] ?? 'utente';

        if (!$nome || !$username || !$password || !$conferma) {
            $msg = "Compila tutti i campi";
            $tipo = "errore";
        } elseif (strlen($password) < 8) {
            $msg = "Password minimo 8 caratteri";
            $tipo = "errore";
        } elseif ($password !== $conferma) {
            $msg = "Le password non coincidono";
            $tipo = "errore";
        } else {

            $check = $conn->prepare("SELECT id FROM utenti WHERE username = ?");
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $msg = "Username già esistente";
                $tipo = "errore";
            } else {
                $check->close();

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO utenti (nome, username, password, ruolo) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nome, $username, $hash, $ruolo);

                if ($stmt->execute()) {
                    // 🔥 REDIRECT
                    header("Location: gestione_utenti.php?success=1");
                    exit;
                } else {
                    $msg = "Errore salvataggio";
                    $tipo = "errore";
                }

                $stmt->close();
            }

            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aggiungi utente</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="top-bar">
  <a href="gestione_utenti.php" class="back-btn">←</a>
</div>

<header class="main-header">
  <div class="overlay">
    <h1>Crea nuovo utente</h1>
  </div>
</header>

<main class="content-wrapper">

  <!-- 🔥 BOX CENTRATO COME REGISTRAZIONE -->
  <div class="form-box">

    <form method="POST">

      <div class="form-group">
        <label>Nome completo:</label>
        <input type="text" name="nome" required />
      </div>

      <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" required />
      </div>

      <div class="form-group">
        <label>Ruolo:</label>
        <select name="ruolo">
          <option value="utente">Utente</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label>Password:</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" required />
          <button type="button" class="toggle-password" onclick="toggle('password')"></button>
        </div>
      </div>

      <div class="form-group">
        <label>Conferma password:</label>
        <div class="password-wrapper">
          <input type="password" id="conferma" name="conferma" required />
          <button type="button" class="toggle-password" onclick="toggle('conferma')"></button>
        </div>
      </div>

      <?php if ($msg): ?>
        <p class="reg-msg <?php echo $tipo; ?>">
          <?php echo $msg; ?>
        </p>
      <?php endif; ?>

      <div class="button-container">
        <button type="submit" class="button donate-button">Salva utente</button>
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