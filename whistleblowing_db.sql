CREATE DATABASE IF NOT EXISTS whistleblowing_db;
USE whistleblowing_db;

CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ruolo ENUM('admin', 'utente') DEFAULT 'utente',
    data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE segnalazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    testo TEXT NOT NULL,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE avvisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(200) NOT NULL,
    testo TEXT NOT NULL,
    id_autore INT NOT NULL,
    data_pubblicazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_autore) REFERENCES utenti(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE incarichi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    id_assegnato_da INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    descrizione TEXT NOT NULL,
    scadenza DATE,
    stato ENUM('in_attesa', 'in_corso', 'completato', 'scaduto') DEFAULT 'in_attesa',
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE,
    FOREIGN KEY (id_assegnato_da) REFERENCES utenti(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- INSERT segnalazioni di esempio
INSERT INTO segnalazioni (testo) VALUES
('bob'), ('bob'), ('poi'), ('dico'), ('tre'),
('giovedi'), ('popolo'), ('bla'), ('mod'), ('nd'), ('asdfghjklò');

-- INSERT utenti
INSERT INTO utenti (nome, username, email, password, ruolo, data_registrazione) VALUES
('Matteo Duranti', 'Duro', 'matteoduranti1354@gmail.com', '$2y$10$UK8CTs1y7aqXXdnVZxN3Q.XcULMWTuupBF8e2bu91JDkCtMxwynE6', 'admin', '2026-04-13 07:17:30'),
('Bogdan', 'bobo', 'elessandro654321@gmail.com', '$2y$10$3iaJWtnlC09UHZTEfvnlSeuKPGQhbvMwcaSXb1KbyjBb72wQ5dAvu', 'utente', '2026-04-13 07:19:11'),
('tito', 'tito', 'mariavittoriafiorucci@gmail.com', '$2y$10$X8/FVnOrWUvk.O.iakvHnuD4cimMm1eJ.YwreWe5PvaePzWTjcPi6', 'utente', '2026-04-15 06:49:02');