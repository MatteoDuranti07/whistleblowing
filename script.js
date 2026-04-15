/* MENU LATERALE */
function Menu() {
   document.getElementById("sideMenu").classList.toggle("active");
}

/* LOGOUT */
function logout() {
  if (confirm("Sei sicuro di voler uscire?")) {
    window.location.href = "login.html";
  }
}


/* MOSTRA / NASCONDI PASSWORD */
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

