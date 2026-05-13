<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Avvisi Aziendali – TrustLine Digital</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-bar">
    <a href="utente.php" class="back-btn">←</a>
  </div>

  <header class="main-header">
    <h1>Avvisi Aziendali</h1>
  </header>

  <main class="content-wrapper">

    <section style="max-width:700px; margin:0 auto 50px;">

      <?php
      // --- Connessione al database ---
      $host     = 'localhost';
      $dbname   = 'whistleblowing_db';
      $dbuser   = 'root';
      $password = '';

      $avvisi = [];
      $errore = false;

      try {
          $pdo = new PDO(
              "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
              $dbuser,
              $password,
              [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
          );
          $stmt = $pdo->query("SELECT titolo, testo, data_pubblicazione FROM avvisi ORDER BY data_pubblicazione DESC");
          $avvisi = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
          $errore = true;
      }
      ?>

      <?php if ($errore): ?>
        <div class="feature-card" style="background:#fff3f3; border-left:4px solid #d32f2f;">
          <p style="color:#d32f2f; text-align:center;">
            Impossibile caricare gli avvisi al momento. Riprova più tardi.
          </p>
        </div>

      <?php elseif (empty($avvisi)): ?>
        <div class="feature-card">
          <h3>Nessun avviso presente</h3>
          <p>
            Al momento non ci sono comunicazioni aziendali pubblicate.
            Torna a controllare in seguito per eventuali aggiornamenti.
          </p>
        </div>

      <?php else: ?>
        <?php foreach ($avvisi as $avviso): ?>
          <div class="feature-card" style="text-align:left; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
              <h3 style="text-align:left; margin:0;">
                <?= htmlspecialchars($avviso['titolo']) ?>
              </h3>
              <span style="font-size:0.85rem; color:#888; white-space:nowrap;">
                <?= htmlspecialchars(date('d/m/Y', strtotime($avviso['data_pubblicazione']))) ?>
              </span>
            </div>
            <p><?= nl2br(htmlspecialchars($avviso['testo'])) ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </section>

  </main>

  <script src="script.js"></script>
</body>
</html>