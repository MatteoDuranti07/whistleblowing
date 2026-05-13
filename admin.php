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
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Area Admin</title>

  <link rel="stylesheet" href="stile.css" />
  <link rel="stylesheet" href="home.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <div id="sideMenu" class="side-menu">
    <h3>Menu</h3>
    <a href="registro_segnalazioni.php">Registro segnalazioni</a>
    <a href="gestione_utenti.php">Gestione utenti</a>
    <a href="aggiungi_avviso.php">Pubblica avviso</a>
    <a href="aggiungi_incarico.php">Assegna incarico</a>
  </div>

  <div class="home-top-bar">
    <div class="menu-btn" onclick="Menu()">☰</div>
    <button class="login-btn" onclick="logout()" aria-label="Logout"></button>
  </div>

  <main class="home-content">

    <h1>Area Amministratore</h1>

    <p class="home-description">
      Questa è l'area riservata agli amministratori del sistema.
      Da qui è possibile monitorare le segnalazioni ricevute, gestire gli utenti
      della piattaforma, pubblicare avvisi e assegnare incarichi.
    </p>

    <section class="features">

      <div class="feature-card">
        <h3>Registro segnalazioni</h3>
        <p>
          Consulta, analizza e gestisci le segnalazioni anonime
          inviate attraverso la piattaforma.
        </p>
      </div>

      <div class="feature-card">
        <h3>Gestione utenti</h3>
        <p>
          Visualizza, modifica e gestisci gli account degli utenti
          autorizzati all'accesso al sistema.
        </p>
      </div>

      <div class="feature-card">
        <h3>Pubblica avviso</h3>
        <p>
          Crea e pubblica comunicazioni aziendali visibili a tutti
          gli utenti nella loro area personale.
        </p>
      </div>

      <div class="feature-card">
        <h3>Assegna incarico</h3>
        <p>
          Assegna compiti specifici agli utenti della piattaforma,
          impostando descrizione, scadenza e stato dell'incarico.
        </p>
      </div>

    </section>

  </main>

  <script src="script.js"></script>
</body>
</html>