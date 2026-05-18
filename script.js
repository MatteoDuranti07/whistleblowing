/* MENU LATERALE */
// funzione javascript
function Menu() {
   document.getElementById("sideMenu").classList.toggle("active");
}

/* LOGOUT */
// funzione javascript
function logout() {
  if (confirm("Sei sicuro di voler uscire?")) {
    window.location.href = "logout.php";
  }
}


/* MOSTRA / NASCONDI PASSWORD */
// funzione javascript
function vediPassword() {
  const input = document.getElementById("password");
  const button = document.querySelector(".toggle-password");

  if (!input || !button) return;

  if (input.type === "password") {
    input.type = "text";
    button.classList.add("visible");
  } else {
    input.type = "password";
    button.classList.remove("visible");
  }
}

/* SEGNALAZIONE ANONIMA */
// funzione javascript
function inviaSegnalazione() {
  const descrizione = document.getElementById("descrizione").value;

  if (!descrizione) {
    alert("Compila tutti i campi!");
    return;
  }

  document.getElementById("risultato").innerText =
    "Segnalazione anonima inviata. Grazie.";

  document.getElementById("moduloSegnalazione").reset();
}
