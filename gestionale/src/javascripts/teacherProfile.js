function changePassword(event) {
  event.preventDefault();

  let oldPassword = document.getElementById("passwordCorrente").value;
  let newPassword = document.getElementById("nuovaPassword").value;
  let confirmPassword = document.getElementById("confermaPassword").value;
  let technicianEmail = document.querySelector(
    "input[name=technicianEmail]"
  ).value;
  let alertPsw = document.querySelectorAll(".alertPsw");

  if (oldPassword == "" || newPassword == "" || confirmPassword == "") {
    return;
  }

  if (newPassword != confirmPassword) {
    alertPsw[2].innerHTML = "Le password non coincidono.";
    alertPsw[2].classList.add("input-error");
    alertPsw[2].classList.remove("hidden");
    document.getElementById("confirmPswLabel").classList.add("input-error");
    return;
  } else {
    alertPsw[2].classList.add("hidden");
    alertPsw[2].classList.remove("input-error");
    document.getElementById("confirmPswLabel").classList.remove("input-error");
  }

  if(newPassword.length < 8) {
    alertPsw[1].innerHTML = "Password minore di 8 caratteri.";
    alertPsw[1].classList.add("input-error");
    alertPsw[1].classList.remove("hidden");
    document.getElementById("newPswLabel").classList.add("input-error");
    return;
  }
  else{
    alertPsw[1].classList.add("hidden");
    alertPsw[1].classList.remove("input-error");
    document.getElementById("newPswLabel").classList.remove("input-error");
  }

  fetch(`http://${server}/API/changePassword.php?token=${token}`, {
    method: "POST",
    body: JSON.stringify({
      oldPassword: oldPassword,
      newPassword: newPassword,
      confirmPassword: confirmPassword,
      technicianEmail: technicianEmail,
    }),
  })
    .then((response) => response.json())
    .then((response) => {
      if(response["status"] == "error") {
        alertPsw[0].innerHTML = response["message"];
        alertPsw[0].classList.add("input-error");
        alertPsw[0].classList.remove("hidden");
        document.getElementById("currentPswLabel").classList.add("input-error");
        return;
      }
      location.reload();
      console.log(response);
    });
}

let server = window.location.hostname;
let token = document.cookie
  .split(";")
  .find((cookie) => {
    return cookie.includes("token");
  })
  .split("=")[1];

function setThemeLocalStorage() {
  localStorage.setItem("theme", this.value);
}

document.querySelectorAll(".theme-controller").forEach((theme) => {
  theme.addEventListener("click", setThemeLocalStorage);
});

if (localStorage.getItem("theme")) {
  // find the corresponding input radio and check it
  document
    .querySelectorAll("input[name='theme']")
    .forEach((theme) => (theme.checked = false));
  document.querySelector(
    `input[value='${localStorage.getItem("theme")}']`
  ).checked = true;
}

document
  .querySelector("input[type='file']")
  .addEventListener("change", function () {
    this.form.submit();
  });
