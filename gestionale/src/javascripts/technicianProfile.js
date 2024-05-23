document.getElementById("confirm").addEventListener("click", function () {
  let popup = document.getElementById("modalAddTeacher");
  popup.close();

  let name = popup.querySelector("#name").value;
  let surname = popup.querySelector("#surname").value;
  let email = popup.querySelector("#email").value;

  let subjects = Array.from(popup.querySelectorAll("#subjects li")).map(
    (li) => li.textContent
  );

  console.log({
    name: name,
    surname: surname,
    email: email,
    subjects: subjects,
  });

  fetch(`http://${server}/API/setTeacher.php?token=${token}`, {
    method: "POST",
    body: JSON.stringify({
      name: name,
      surname: surname,
      email: email,
      subjects: subjects,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data["status"] == "successfully added teacher") {
        localStorage.setItem("teacherAdded", "true");
      }
      location.reload();
      console.log(data);
    });
});

function deleteTeachers() {
  let selected = Array.from(
    document.querySelectorAll("input[type='checkbox']:checked")
  ).map((checkbox) => checkbox.value);

  fetch(`http://${server}/API/deleteTeacher.php?token=${token}`, {
    method: "POST",
    body: JSON.stringify({
      teachers: selected,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data["status"] == "successfully deleted teachers") {
        localStorage.setItem("teacherRemove", "true");
      }
      location.reload();
      console.log(data);
    });
}

function checkSelect() {
  let select = document.querySelector("select");
  let add = document.getElementById("add");
  let subjects = Array.from(
    document.getElementById("modalAddTeacher").querySelectorAll("#subjects li")
  ).map((li) => li.textContent);

  if (select.value == "" || subjects.includes(select.value)) {
    add.classList.add("btn-disabled");
  } else {
    add.classList.remove("btn-disabled");
  }
}

document.getElementById("add").addEventListener("click", () => {
  let content = document.querySelector("select").value;
  if (content == "") return;
  let li = document.createElement("li");
  li.classList.add(
    "btn",
    "btn-neutral",
    "m-2",
    "no-animation",
    "hover:border-2",
    "hover:border-red-500",
    "hover:text-red-400"
  );
  li.innerHTML = content;
  li.addEventListener("click", () => {
    li.remove();
    checkSelect();
  });
  document.getElementById("subjects").appendChild(li);
  checkSelect();
});

function checkDeleteButton() {
  let deleteButton = document.getElementById("delete");

  let selected = Array.from(
    document.querySelectorAll("input[type='checkbox']:checked")
  ).map((checkbox) => checkbox.value);

  if (selected.length == 0) {
    deleteButton.classList.add("btn-disabled");
  } else {
    deleteButton.classList.remove("btn-disabled");
  }
}

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

  if (newPassword.length < 8) {
    alertPsw[1].innerHTML = "Password minore di 8 caratteri.";
    alertPsw[1].classList.add("input-error");
    alertPsw[1].classList.remove("hidden");
    document.getElementById("newPswLabel").classList.add("input-error");
    return;
  } else {
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
      if (response["status"] == "error") {
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

function createAlert(message, type) {
  if (message == "") {
    return;
  }
  let toast = document.querySelector(".toast");
  let alert = toast.querySelector(".alert");
  let span = alert.querySelector("span");

  try {
    alert.classList.remove(
      Array.from(alert.classList).filter((c) => c.startsWith("alert-"))
    );
  } catch (e) {}
  alert.classList.add(`alert-${type}`);
  span.textContent = message;
  alert.classList.remove("opacity-0");
  setTimeout(() => {
    alert.classList.add("opacity-0");
  }, 3000);
}

function openStatsModal(name, surname, email) {
  let modal = document.getElementById("modalStats");
  let title = modal.querySelector("h1");
  title.textContent = `Statistiche di ${surname} ${name}`;

  fetch(
    `http://${server}/API/getStats.php?email=${email}&token=${token}`,
    {
      method: "GET",
    }
  )
    .then((response) => response.json())
    .then((data) => {
      document.querySelectorAll(".stat")[0].querySelector(".stat-value").textContent = data["pc"]["teacher_pc"];
      document.querySelectorAll(".stat")[0].querySelector(".stat-desc").querySelector("span").textContent = data["pc"]["percentage"] + "%";

      document.querySelectorAll(".stat")[1].querySelector(".stat-value").textContent = data["reservation"]["teacher_reservation"];
      document.querySelectorAll(".stat")[1].querySelector(".stat-desc").querySelector("span").textContent = data["reservation"]["percentage"] + "%";

      document.querySelectorAll(".stat")[2].querySelector(".stat-value").textContent = data["completed"]["percentage"] + "%";
      document.querySelectorAll(".stat")[2].querySelector(".stat-desc").querySelector("span").textContent = data["completed"]["uncompleted"];
    });
  modal.showModal();
}

document.addEventListener("DOMContentLoaded", () => {
  console.log(teacherAdded);

  if (teacherAdded) {
    createAlert("Docente aggiunto con successo", "success");
    localStorage.setItem("teacherAdded", "false");
  }
  if (teacherRemove) {
    createAlert("Docente rimosso con successo", "error");
    localStorage.setItem("teacherRemove", "false");
  }
});

function setThemeLocalStorage() {
  localStorage.setItem("theme", this.value);
}

function checkKeysDown(event) {
  if (event.key == "Shift") {
    if (document.querySelector("dialog[open]")) return;
    multipleCheckboxSelection = true;
  }
  if (event.key == "Delete") {
    if(Array.from(document.querySelectorAll("input[type='checkbox']:checked")).length == 0) return;
    document.getElementById("confirmDelete").showModal();
  }
}

function checkKeysUp(event) {
  if (event.key == "Shift") {
    if (document.querySelector("dialog[open]")) return;
    multipleCheckboxSelection = false;
  }
}

document.querySelectorAll(".theme-controller").forEach((theme) => {
  theme.addEventListener("click", setThemeLocalStorage);
});

document.addEventListener("keydown", checkKeysDown);
document.addEventListener("keyup", checkKeysUp);

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

let multipleCheckboxSelection = false;
let lastCheckboxChecked = [];
let server = window.location.hostname;
let token = document.cookie
  .split(";")
  .find((cookie) => {
    return cookie.includes("token");
  })
  .split("=")[1];

/*setInterval(() => {
  console.log(multipleCheckboxSelection, lastCheckboxChecked);
}, 10);*/

let teacherAdded = localStorage.getItem("teacherAdded") === "true";
let teacherRemove = localStorage.getItem("teacherRemove") === "true";

document.querySelectorAll(".teacherCheckbox").forEach((checkbox) => {
  checkbox.addEventListener("click", (e) => {
    if (
      multipleCheckboxSelection &&
      lastCheckboxChecked[1] + 5000 > e.timeStamp
    ) {
      // get checkboxes between lastCheckboxChecked and e.target
      let checkboxes = Array.from(
        document.querySelectorAll(".teacherCheckbox")
      );
      let lastCheckboxIndex = checkboxes.indexOf(lastCheckboxChecked[0]);
      let currentCheckboxIndex = checkboxes.indexOf(e.target);
      let checkboxesToCheck = checkboxes.slice(
        Math.min(lastCheckboxIndex, currentCheckboxIndex),
        Math.max(lastCheckboxIndex, currentCheckboxIndex) + 1
      );
      checkboxesToCheck.forEach((tocheck) => {
        tocheck.checked = e.target.checked;
      });
    }
    // save the last checkbox checked and timestamp
    lastCheckboxChecked = [e.target, e.timeStamp];
  });
});
