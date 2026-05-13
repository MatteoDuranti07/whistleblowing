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

$host     = 'localhost';
$dbname   = 'whistleblowing_db';
$user     = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $utenti = $pdo->query("
        SELECT id, nome, username, ruolo, data_registrazione
        FROM utenti
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Errore di esecuzione query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestione Utenti</title>

  <link rel="stylesheet" href="stile.css" />

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap"
        rel="stylesheet">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    thead {
      background-color: #1976d2;
      color: #ffffff;
    }

    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid #dce3ed;
      font-size: 0.95rem;
    }

    tbody tr:hover {
      background-color: #f0f5fb;
    }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .badge-admin {
      background: #e3ecf5;
      color: #0d47a1;
    }

    .badge-utente {
      background: #e8f5e9;
      color: #2e7d32;
    }

    .action-link {
      color: #1976d2;
      margin-right: 12px;
      font-size: 1rem;
      text-decoration: none;
      transition: color 0.2s;
      cursor: pointer;
    }

    .action-link:hover {
      color: #0d47a1;
    }

    .action-link.delete {
      color: #e53935;
    }

    .action-link.delete:hover {
      color: #b71c1c;
    }

    .add-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background-color: #1976d2;
      color: #ffffff;
      border: none;
      padding: 10px 22px;
      font-size: 0.95rem;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      margin-bottom: 24px;
      transition: background-color 0.3s ease,
                  transform 0.2s ease;
    }

    .add-btn:hover {
      background-color: #0d47a1;
      transform: translateY(-2px);
    }

  </style>
</head>

<body>

<div class="top-bar">
  <a href="admin.php" class="back-btn">←</a>
</div>

<header class="main-header">
  <h1>Gestione Utenti</h1>
</header>

<main class="content-wrapper">

  <div class="card-form">

    <a href="crea_utente.php" class="add-btn">
      <i class="fas fa-plus"></i>
      Nuovo utente
    </a>

    <table>

      <thead>
        <tr>
          <th>Nome</th>
          <th>Username</th>
          <th>Ruolo</th>
          <th>Registrato il</th>
          <th>Azioni</th>
        </tr>
      </thead>

      <tbody>

      <?php foreach ($utenti as $utente): ?>

        <tr id="riga-utente-<?= $utente['id'] ?>">

          <td><?= htmlspecialchars($utente['nome']) ?></td>

          <td><?= htmlspecialchars($utente['username']) ?></td>

          <td>
            <span class="badge badge-<?= $utente['ruolo'] ?>">
              <?= htmlspecialchars($utente['ruolo']) ?>
            </span>
          </td>

          <td><?= htmlspecialchars($utente['data_registrazione']) ?></td>

          <td>

            <a href="modifica_utenti.php?id=<?= $utente['id'] ?>"
               class="action-link"
               title="Modifica">

              <i class="fas fa-edit"></i>

            </a>

            <a class="action-link delete"
               title="Elimina"
               data-id="<?= $utente['id'] ?>"
               onclick="eliminaUtente(this)">

              <i class="fas fa-trash-alt"></i>

            </a>

          </td>

        </tr>

      <?php endforeach; ?>

      </tbody>

    </table>

  </div>

</main>

<script>

function eliminaUtente(el) {

    if (!confirm('Sei sicuro di voler eliminare questo utente?')) {
        return;
    }

    const id   = el.getAttribute('data-id');
    const riga = document.getElementById('riga-utente-' + id);

    fetch('elimina_utenti.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(id)
    })

    .then(res => res.json())

    .then(data => {

        if (data.success) {

            riga.style.transition = 'opacity 0.3s';
            riga.style.opacity = '0';

            setTimeout(() => {
                riga.remove();
            }, 300);

        } else {

            alert(
                'Errore: ' +
                (data.message || 'Impossibile eliminare l\'utente.')
            );
        }
    })

    .catch(() => {
        alert('Errore di rete. Riprova.');
    });
}

</script>

</body>
</html>