<?php
// avvia la sessione utente
session_start();

if (
    !isset($_SESSION['id']) ||
    !isset($_SESSION['ruolo']) ||
    $_SESSION['ruolo'] !== 'admin'
) {
// reindirizza l'utente
    header("Location: login.html");
    exit;
}

$host = 'localhost';
$dbname = 'whistleblowing_db';
$user = 'root';
$pass = '';

try {

// connessione al database con pdo
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

} catch (PDOException $e) {

    die("Errore connessione DB: " . $e->getMessage());
}


if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id'])
) {

// reindirizza l'utente
    header('Content-Type: application/json');

    try {

        $id = (int) $_POST['id'];

// prepara la query sql
        $stmt = $pdo->prepare("
            DELETE FROM segnalazioni
            WHERE id = ?
        ");

// esegue la query
        $stmt->execute([$id]);

// restituisce una risposta json
        echo json_encode([
            'success' => true
        ]);

    } catch (PDOException $e) {

// restituisce una risposta json
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}


if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['ids'])
) {

// reindirizza l'utente
    header('Content-Type: application/json');

    try {

        $ids = json_decode($_POST['ids'], true);

        if (!is_array($ids) || empty($ids)) {

// restituisce una risposta json
            echo json_encode([
                'success' => false,
                'message' => 'Nessun ID valido'
            ]);

            exit;
        }

        $placeholders = implode(
            ',',
            array_fill(0, count($ids), '?')
        );

// prepara la query sql
        $stmt = $pdo->prepare("
            DELETE FROM segnalazioni
            WHERE id IN ($placeholders)
        ");

// esegue la query
        $stmt->execute($ids);

// restituisce una risposta json
        echo json_encode([
            'success' => true
        ]);

    } catch (PDOException $e) {

// restituisce una risposta json
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}


$order = isset($_GET['ordine']) &&
         $_GET['ordine'] === 'asc'
    ? 'ASC'
    : 'DESC';

$order_label = $order === 'DESC'
    ? 'asc'
    : 'desc';

$order_text = $order === 'DESC'
    ? '↑ Meno recenti'
    : '↓ Più recenti';

$segnalazioni = $pdo->query("
    SELECT *
    FROM segnalazioni
    ORDER BY id $order
// recupera i dati dal database
")->fetchAll(PDO::FETCH_ASSOC);

$totale = count($segnalazioni);
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registro Segnalazioni</title>

    <link rel="stylesheet" href="stile.css">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
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
            background: #1976d2;
            color: white;
        }

        th,
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #dce3ed;
            text-align: left;
        }

        tbody tr:hover {
            background: #f0f5fb;
        }

        .td-id {
            font-weight: bold;
            color: #1976d2;
            width: 70px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .toolbar-info {
            color: #777;
            font-size: 14px;
        }

        .toolbar-info strong {
            color: #333;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sort-btn,
        .delete-selected-btn {

            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .sort-btn {
            background: #1976d2;
        }

        .sort-btn:hover {
            background: #0d47a1;
        }

        .delete-selected-btn {
            background: #e53935;
        }

        .delete-selected-btn:hover {
            background: #b71c1c;
        }

        .action-link {

            width: 32px;
            height: 32px;

            border: none;
            border-radius: 7px;

            background: transparent;

            cursor: pointer;

            color: #e53935;

            transition: 0.2s;
        }

        .action-link:hover {
            background: #fde8e8;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }

        .checkbox-cell {
            width: 40px;
            text-align: center;
        }

    </style>
</head>

<body>

<div class="top-bar">
    <a href="admin.php" class="back-btn">←</a>
</div>

<header class="main-header">
    <h1>Registro Segnalazioni</h1>
</header>

<main class="content-wrapper">

    <div class="card-form" style="max-width:900px;">

        <div class="toolbar">

            <span class="toolbar-info">

                Totale:

                <strong id="totale-segnalazioni">
                    <?= $totale ?>
                </strong>

                segnalazioni ·

                Ordine:

                <strong>
                    <?= $order === 'DESC'
                        ? 'più recenti prima'
                        : 'meno recenti prima' ?>
                </strong>

            </span>

            <div class="toolbar-actions">

                <button
                    class="delete-selected-btn"
                    onclick="eliminaMultiple()"
                >
                    <i class="fas fa-trash"></i>
                    Elimina selezionate
                </button>

                <a href="?ordine=<?= $order_label ?>"
                   class="sort-btn">

                    <?= $order_text ?>

                </a>

            </div>

        </div>

        <?php if ($totale > 0): ?>

<!-- tabella dati -->
        <table id="tabella-segnalazioni">

            <thead>
            <tr>

                <th class="checkbox-cell">
                    <input
                        type="checkbox"
                        id="select-all"
                        onclick="toggleSelectAll(this)"
                    >
                </th>

                <th>#</th>

                <th>Testo Segnalazione</th>

                <th style="text-align:center;">
                    Azioni
                </th>

            </tr>
            </thead>

            <tbody>

            <?php foreach ($segnalazioni as $s): ?>

                <tr id="riga-seg-<?= $s['id'] ?>">

                    <td class="checkbox-cell">

                        <input
                            type="checkbox"
                            class="checkbox-segnalazione"
                            value="<?= $s['id'] ?>"
                        >

                    </td>

                    <td class="td-id">

                        <?= str_pad(
                            $s['id'],
                            4,
                            '0',
                            STR_PAD_LEFT
                        ) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $s['testo'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                    <td style="text-align:center;">

                        <button
                            class="action-link"
                            data-id="<?= $s['id'] ?>"
                            onclick="eliminaSegnalazione(this)"
                        >
                            <i class="fas fa-trash-alt"></i>
                        </button>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <?php endif; ?>

        <div
            id="empty-message"
            class="empty"
            style="<?= $totale > 0 ? 'display:none;' : '' ?>"
        >
            <h2>Nessuna segnalazione</h2>
        </div>

    </div>

</main>

<!-- script javascript -->
<script>

// funzione javascript
function toggleSelectAll(el) {

    const checkboxes =
        document.querySelectorAll(
            '.checkbox-segnalazione'
        );

    checkboxes.forEach(cb => {
        cb.checked = el.checked;
    });
}


// funzione javascript
function eliminaSegnalazione(el) {

    if (!confirm(
        'Sei sicuro di voler eliminare questa segnalazione?'
    )) {
        return;
    }

    const id = el.dataset.id;

    eliminaDalDatabase([id]);
}


// funzione javascript
function eliminaMultiple() {

    const selezionate =
        document.querySelectorAll(
            '.checkbox-segnalazione:checked'
        );

    if (selezionate.length === 0) {

        alert(
            'Seleziona almeno una segnalazione'
        );

        return;
    }

    if (!confirm(
        'Eliminare le segnalazioni selezionate?'
    )) {
        return;
    }

    const ids = [];

    selezionate.forEach(cb => {
        ids.push(cb.value);
    });

    eliminaDalDatabase(ids);
}


// funzione javascript
function eliminaDalDatabase(ids) {

    const formData = new URLSearchParams();

    if (ids.length === 1) {

        formData.append('id', ids[0]);

    } else {

        formData.append(
            'ids',
            JSON.stringify(ids)
        );
    }

// invia una richiesta al server
    fetch('', {

        method: 'POST',

        headers: {
            'Content-Type':
                'application/x-www-form-urlencoded'
        },

        body: formData.toString()

    })

    .then(r => r.json())

    .then(data => {

        if (data.success) {

            ids.forEach(id => {

                const riga =
                    document.getElementById(
                        'riga-seg-' + id
                    );

                if (riga) {

                    riga.style.transition =
                        'opacity 0.3s';

                    riga.style.opacity = '0';

                    setTimeout(() => {
                        riga.remove();
                        aggiornaTotale();
                    }, 300);
                }
            });

        } else {

            alert(
                'Errore: ' + data.message
            );
        }

    })

    .catch(() => {

        alert('Errore di rete');

    });
}


// funzione javascript
function aggiornaTotale() {

    const righe =
        document.querySelectorAll(
            '#tabella-segnalazioni tbody tr'
        );

    const totale = righe.length;

    document.getElementById(
        'totale-segnalazioni'
    ).textContent = totale;

    if (totale === 0) {

        const tabella =
            document.getElementById(
                'tabella-segnalazioni'
            );

        if (tabella) {
            tabella.style.display = 'none';
        }

        document.getElementById(
            'empty-message'
        ).style.display = 'block';
    }
}

</script>

</body>
</html>