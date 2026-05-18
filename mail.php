<?php

// importa le classi principali di phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// include i file necessari di phpmailer
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// funzione per configurare il mailer
function getMailer() {

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';

    // abilita autenticazione smtp
    $mail->SMTPAuth = true;
    $mail->Username = 'matteoduranti1354@gmail.com';
    $mail->Password = 'jrkw xlbz dvwx iayh';

    // tipo di crittografia
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom(
        'matteoduranti1354@gmail.com',
        'Whistleblowing System'
    );
    return $mail;
}

// funzione invio email registrazione
function emailRegistrazione($email, $nome) {

    $mail = getMailer();

    try {

        $mail->addAddress($email, $nome);
        $mail->isHTML(true);
        $mail->Subject = "Registrazione completata";
        $mail->Body = "
            <h2>Ciao $nome!</h2>
            <p>Il tuo account è stato creato con successo.</p>
            <p>Ora puoi accedere al sistema.</p>
        ";
        $mail->AltBody =
            "Ciao $nome! Registrazione completata.";
        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;
    }
}

// funzione invio email cambio password
function emailCambioPassword(
    $email,
    $nome,
    $tipo = 'utente'
) {

    $mail = getMailer();

    try {

        $mail->addAddress($email, $nome);
        if ($tipo === 'admin') {
            $messaggio =
                "L'admin ha aggiornato la tua password come da sua richiesta.";

        } else {
            $messaggio =
                "La tua password è stata aggiornata correttamente.";
        }
        $mail->isHTML(true);
        $mail->Subject = "Password aggiornata";
        $mail->Body = "
            <h2>Ciao $nome</h2>
            <p>$messaggio</p>
            <p>Se non riconosci questa operazione, contatta subito il supporto.</p>
        ";
        $mail->AltBody = $messaggio;
        $mail->send();

        return true;

    } catch (Exception $e) {

        // restituisce errore invio
        return false;
    }
}

// funzione invio email modifica dati
function emailModificaDati($email, $nome) {

    $mail = getMailer();

    try {
        $mail->addAddress($email, $nome);
        $mail->isHTML(true);
        $mail->Subject = "Dati aggiornati";
        $mail->Body = "
            <h2>Ciao $nome</h2>
            <p>I tuoi dati sono stati aggiornati correttamente.</p>
        ";
        $mail->AltBody =
            "I tuoi dati sono stati aggiornati.";

        $mail->send();

        return true;

    } catch (Exception $e) {

        // restituisce errore invio
        return false;
    }
}

?>