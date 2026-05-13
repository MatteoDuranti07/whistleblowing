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
  <title>Area Utente</title>

  <link rel="stylesheet" href="stile.css" />
  <link rel="stylesheet" href="home.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <div id="sideMenu" class="side-menu">
    <h3>Menu</h3>
    <a href="">Avvisi</a>
    <a href="">Incarichi</a>
    <a href="aggiorna_password.php">Aggiorna Password</a>
  </div>

  <div class="home-top-bar">
    <div class="menu-btn" onclick="Menu()">☰</div>
    <button class="login-btn" onclick="logout()" aria-label="Logout"></button>
  </div>

  <main class="home-content">

    <h1>Benvenuto <!-- nome utente --></h1>

    <p class="home-description">
      Questa è la tua area personale. Da qui puoi consultare le comunicazioni
      aziendali e gestire gli incarichi assegnati utilizzando il menu laterale.
    </p>

    <section class="features">

      <div class="feature-card">
        <h3>Avvisi aziendali</h3>
        <p>
          Consulta le comunicazioni interne e gli avvisi importanti
          pubblicati dall’azienda.
        </p>
      </div>

      <div class="feature-card">
        <h3>Incarichi assegnati</h3>
        <p>
          Visualizza e gestisci gli incarichi a te assegnati,
          controllando stato e scadenze.
        </p>
      </div>

    </section>

  </main>

  <script src="script.js"></script>
</body>
</html>

    