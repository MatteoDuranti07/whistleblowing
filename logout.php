<?php

// avvia la sessione utente
session_start();

// elimina tutti i dati della sessione
session_destroy();

// reindirizza alla pagina di login
header("Location: login.html");

// termina lo script
exit;

?>