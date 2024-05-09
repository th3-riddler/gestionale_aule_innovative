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
    })
  })
    .then((response) => response.json())
    .then((data) => {
      location.reload();
      console.log(data);
    });
});



document.getElementById("delete").addEventListener("click", function () {
  let selected = Array.from(document.querySelectorAll("input[type='checkbox']:checked")).map(
    (checkbox) => checkbox.value
  );

  fetch(`http://${server}/API/deleteTeacher.php?token=${token}`, {
    method: "POST",
    body: JSON.stringify({
      teachers: selected,
    })
  })
    .then((response) => response.json())
    .then((response) => {
      location.reload();
      console.log(response);
    }
  );
});


function checkSelect(){
  let select = document.querySelector("select");
  let add = document.getElementById("add");
  let subjects = Array.from(document.getElementById("modalAddTeacher").querySelectorAll("#subjects li")).map(
    (li) => li.textContent
  );

  if(select.value == "" || subjects.includes(select.value)) {
    add.classList.add("btn-disabled");
  } else{
    add.classList.remove("btn-disabled");
  }
}


document.getElementById("add").addEventListener("click", () => {
  let content = document.querySelector("select").value;
  if (content == "") return;
  let li = document.createElement("li");
  li.classList.add("btn", "btn-neutral", "m-2", "no-animation");
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

  let selected = Array.from(document.querySelectorAll("input[type='checkbox']:checked")).map(
    (checkbox) => checkbox.value
  );

  if(selected.length == 0){
    deleteButton.classList.add("btn-disabled");
  }
  else{
    deleteButton.classList.remove("btn-disabled");
  }
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

if (localStorage.getItem("theme")) {
  // find the corresponding input radio and check it
  document
    .querySelectorAll("input[name='theme']")
    .forEach((theme) => (theme.checked = false));
  document.querySelector(
    `input[value='${localStorage.getItem("theme")}']`
  ).checked = true;
}

document.querySelectorAll(".theme-controller").forEach((theme) => {
  theme.addEventListener("click", setThemeLocalStorage);
});

document
  .querySelector("input[type='file']")
  .addEventListener("change", function () {
    this.form.submit();
  });
