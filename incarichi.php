<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}

$id_utente = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Incarichi – TrustLine Digital</title>
  <link rel="stylesheet" href="stile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-bar">
    <a href="utente.php" class="back-btn">←</a>
  </div>

  <header class="main-header">
    <h1>I Miei Incarichi</h1>
  </header>

  <main class="content-wrapper">

    <section style="max-width:700px; margin:0 auto 50px;">

      <?php
      $host     = 'localhost';
      $dbname   = 'whistleblowing_db';
      $dbuser   = 'root';
      $password = '';

      $incarichi = [];
      $errore    = false;

      try {
          $pdo = new PDO(
              "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
              $dbuser,
              $password,
              [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
          );
          $stmt = $pdo->prepare("
              SELECT titolo, descrizione, scadenza, stato
              FROM incarichi
              WHERE id_utente = ?
              ORDER BY scadenza ASC
          ");
          $stmt->execute([$id_utente]);
          $incarichi = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
          $errore = true;
      }

      // Mappa stati → colori e testi
      $badge = [
          'in_corso'    => ['color' => '#1976d2', 'bg' => '#e3f0fb', 'label' => 'In corso'],
          'completato'  => ['color' => '#2e7d32', 'bg' => '#eafaf1', 'label' => 'Completato'],
          'in_attesa'   => ['color' => '#f57c00', 'bg' => '#fff8e1', 'label' => 'In attesa'],
          'scaduto'     => ['color' => '#c62828', 'bg' => '#fdecea', 'label' => 'Scaduto'],
      ];
      ?>

      <?php if ($errore): ?>
        <div class="feature-card" style="background:#fff3f3; border-left:4px solid #d32f2f;">
          <p style="color:#d32f2f; text-align:center;">
            Impossibile caricare gli incarichi al momento. Riprova più tardi.
          </p>
        </div>

      <?php elseif (empty($incarichi)): ?>
        <div class="feature-card">
          <h3>Nessun incarico assegnato</h3>
          <p>
            Non hai ancora incarichi assegnati. Quando l'amministratore ti assegnerà
            un compito, lo troverai qui con tutti i dettagli e le scadenze.
          </p>
        </div>

      <?php else: ?>
        <?php foreach ($incarichi as $inc):
          $stato  = $inc['stato'] ?? 'in_attesa';
          $stile  = $badge[$stato] ?? $badge['in_attesa'];
          $scad   = $inc['scadenza'] ? date('d/m/Y', strtotime($inc['scadenza'])) : '—';
        ?>
          <div class="feature-card" style="text-align:left; margin-bottom:20px;">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
              <h3 style="text-align:left; margin:0;">
                <?= htmlspecialchars($inc['titolo']) ?>
              </h3>
              <span style="
                font-size:0.8rem;
                font-weight:700;
                padding:3px 10px;
                border-radius:20px;
                color:<?= $stile['color'] ?>;
                background:<?= $stile['bg'] ?>;
                white-space:nowrap;
              ">
                <?= $stile['label'] ?>
              </span>
            </div>

            <p style="margin-bottom:12px;"><?= nl2br(htmlspecialchars($inc['descrizione'])) ?></p>

            <p style="font-size:0.88rem; color:#666; margin:0;">
              <strong>Scadenza:</strong> <?= $scad ?>
            </p>

          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </section>

  </main>

  <script src="script.js"></script>
</body>
</html>