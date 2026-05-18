<?php
// avvia la sessione utente
session_start();

$host     = 'localhost';
$dbname   = 'whistleblowing_db';
$user     = 'root';
$password = '';

$msg  = "";
$tipo = "";

if (!isset($_SESSION['id'])) {
// reindirizza l'utente
    header("Location: login.html");
    exit;
}

$id = $_SESSION['id'];

try {

// connessione al database con pdo
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    require 'mail.php';

// prepara la query sql
    $stmt = $pdo->prepare("
        SELECT id, nome, username, email, password
        FROM utenti
        WHERE id = ?
    ");

// esegue la query
    $stmt->execute([$id]);

// invia una richiesta al server
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utente) {
        session_destroy();
// reindirizza l'utente
        header("Location: login.html");
        exit;
    }

// controlla il tipo di richiesta
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $password_attuale = $_POST['password_attuale'] ?? '';
        $nuova_password   = $_POST['password'] ?? '';
        $conferma         = $_POST['conferma'] ?? '';

// controlla la password inserita
        if (!password_verify($password_attuale, $utente['password'])) {

            $msg  = "La password attuale non è corretta";
            $tipo = "errore";

        } elseif (strlen($nuova_password) < 8) {

            $msg  = "La nuova password deve avere almeno 8 caratteri";
            $tipo = "errore";

        } elseif ($nuova_password !== $conferma) {

            $msg  = "Le password non coincidono";
            $tipo = "errore";

        } else {

// crea la password criptata
            $hash = password_hash($nuova_password, PASSWORD_DEFAULT);

// prepara la query sql
            $upd = $pdo->prepare("
                UPDATE utenti
                SET password = ?
                WHERE id = ?
            ");

// esegue la query
            $upd->execute([$hash, $id]);

            emailCambioPassword(
                $utente['email'],
                $utente['nome'],
                'utente'
            );

            $msg  = "Password aggiornata correttamente";
            $tipo = "successo";
        }
    }

} catch (PDOException $e) {

    die("Errore: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Aggiorna Password</title>

    <link rel="stylesheet" href="stile.css">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap"
          rel="stylesheet">

</head>

<body>

<div class="top-bar">
    <a href="utente.php" class="back-btn">←</a>
</div>

<header class="main-header">
    <div class="overlay">
        <h1>Aggiorna Password</h1>
    </div>
</header>

<main class="content-wrapper">

    <div class="form-box">

        <div class="info-section">

            <h3>Dati account</h3>

            <div class="info-row">
                <span class="info-label">Nome</span>

                <span class="info-value">
                    <?= htmlspecialchars($utente['nome']) ?>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Username</span>

                <span class="info-value">
                    <?= htmlspecialchars($utente['username']) ?>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Email</span>

                <span class="info-value">
                    <?= htmlspecialchars($utente['email']) ?>
                </span>
            </div>

        </div>

        <hr class="divider">

<!-- modulo principale -->
        <form method="POST">

            <div class="form-group">

                <label>Password attuale</label>

                <div class="password-wrapper">

                    <input type="password"
                           id="password_attuale"
                           name="password_attuale"
                           required>

                    <button type="button"
                            class="toggle-password"
                            onclick="toggle('password_attuale')"></button>

                </div>

            </div>

            <div class="form-group">

                <label>Nuova password</label>

                <div class="password-wrapper">

                    <input type="password"
                           id="password"
                           name="password"
                           required>

                    <button type="button"
                            class="toggle-password"
                            onclick="toggle('password')"></button>

                </div>

            </div>

            <div class="form-group">

                <label>Conferma password</label>

                <div class="password-wrapper">

                    <input type="password"
                           id="conferma"
                           name="conferma"
                           required>

                    <button type="button"
                            class="toggle-password"
                            onclick="toggle('conferma')"></button>

                </div>

            </div>

            <?php if ($msg): ?>

                <p class="reg-msg <?= $tipo ?>">
                    <?= htmlspecialchars($msg) ?>
                </p>

            <?php endif; ?>

            <div class="button-container">

                <button type="submit"
                        class="button donate-button">

                    Aggiorna password

                </button>

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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.password-wrapper {
    display: flex;
    align-items: center;
}

.password-wrapper input {
    flex: 1;
}

.reg-msg {
    text-align: center;
    font-weight: 600;
    margin-top: 10px;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid;
}

.reg-msg.errore {
    color: #c0392b;
    background: #fdecea;
    border-color: #e74c3c;
}

.reg-msg.successo {
    color: #1e7e34;
    background: #eafaf1;
    border-color: #27ae60;
}

</style>

<!-- script javascript -->
<script>

// funzione javascript
function toggle(id) {

    const input = document.getElementById(id);

    input.type = input.type === "password"
        ? "text"
        : "password";
}

</script>

</body>
</html>