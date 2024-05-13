function activate() {
  console.log(this);

  if (this.classList.contains("selected")) {
    this.classList.remove("selected");
    this.firstElementChild.classList.remove("btn-accent");
    document.getElementById("inputPcQt").setAttribute("disabled", "true");
    document.getElementById("inputPcQt").value = "";
    document.getElementById("inputPcQt").max = 0;

    document.getElementById("teacherNote").setAttribute("disabled", "true");
    document.getElementById("teacherNote").classList.add("input-disabled");
    document
      .getElementById("teacherNote")
      .parentElement.classList.add("input-disabled");
    document.getElementById("teacherNote").value = "";

    document
      .getElementById("formReservation")
      .querySelector("button[type='submit']")
      .setAttribute("disabled", "true");
    return;
  }

  document.querySelectorAll(".selected").forEach(function (item) {
    item.classList.remove("selected");
    item.firstElementChild.classList.remove("btn-accent");
  });

  this.classList.add("selected");
  this.firstElementChild.classList.add("btn-accent");
  document.getElementById("inputPcQt").removeAttribute("disabled");
  document.getElementById("teacherNote").removeAttribute("disabled");
  document.getElementById("teacherNote").classList.remove("input-disabled");
  document
    .getElementById("teacherNote")
    .parentElement.classList.remove("input-disabled");
  document
    .getElementById("formReservation")
    .querySelector("button[type='submit']")
    .removeAttribute("disabled");
  document.getElementById("inputPcQt").max = parseInt(
    this.querySelector(".maxPc").textContent
  );
  let weekday = document
    .querySelectorAll("th")
    [parseInt(this.id.split("")[1])].querySelector("span").textContent;

  let current = document.getElementById("current").textContent;
  let monday_week = new Date(current.split(" - ")[0]);
  let date = new Date(
    monday_week.getFullYear(),
    monday_week.getMonth(),
    monday_week.getDate() + parseInt(this.id.split("")[1])
  );

  let presentInputs = document
    .getElementById("formReservation")
    .querySelectorAll("input[type='hidden']");
  presentInputs.forEach(function (item) {
    item.remove();
  });

  for (let i = 0; i < 5; i++) {
    let input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute(
      "name",
      ["date", "room", "weekday", "hour", "cart_id"][i]
    );
    input.setAttribute(
      "value",
      [
        date.toISOString().split("T")[0],
        this.querySelector(".room").textContent,
        weekday,
        this.id.split("")[0],
        this.value,
      ][i]
    );
    document.getElementById("formReservation").appendChild(input);
  }
}

function setThemeLocalStorage() {
  console.log(this.value);
  localStorage.setItem("theme", this.value);
}

document.addEventListener("DOMContentLoaded", function () {
  let current = document.getElementById("current");
  let dateQuery = new URLSearchParams(window.location.search).get("date");
  let current_date = dateQuery ? new Date(dateQuery) : new Date();
  let monday_week = new Date(
    current_date.getFullYear(),
    current_date.getMonth(),
    current_date.getDate() - current_date.getDay() + 1
  );
  let saturday_week = new Date(
    current_date.getFullYear(),
    current_date.getMonth(),
    current_date.getDate() - current_date.getDay() + 6
  );
  current.textContent =
    monday_week.toDateString() + " - " + saturday_week.toDateString();

  document.getElementById("previous").addEventListener("click", function () {
    let current = document.getElementById("current");
    let current_date = new Date(current.textContent.split(" - ")[0]);
    let monday_week = new Date(
      current_date.getFullYear(),
      current_date.getMonth(),
      current_date.getDate() - 6
    );
    window.location.href =
      window.location.href.split("?")[0] +
      "?date=" +
      monday_week.toISOString().split("T")[0];
  });

  document.getElementById("next").addEventListener("click", function () {
    let current = document.getElementById("current");
    let current_date = new Date(current.textContent.split(" - ")[0]);
    let monday_week = new Date(
      current_date.getFullYear(),
      current_date.getMonth(),
      current_date.getDate() + 8
    );

    window.location.href =
      window.location.href.split("?")[0] +
      "?date=" +
      monday_week.toISOString().split("T")[0];
  });

  scriptValues.forEach(function (item) {
    document.getElementById(item["hour"] + item["weekdayNumber"]).value =
      item["cart_id"];

    if (!item["had_reservation"]) {
      document.getElementById(
        item["hour"] + item["weekdayNumber"]
      ).innerHTML = `<div class="btn btn-wide btn-${
        item["final_pc_number"] == 0 ? "error" : "primary"
      }">
                <h2 class="card-title">${
                  item["class"] + item["section"]
                }<span class='room'>${item["room"]}</span></h2>
                <p>PC disponibili: <span class='maxPc'>${
                  item["final_pc_number"]
                }</span></p>
            </div>`;
      if (item["final_pc_number"] > 0) {
        document
          .getElementById(item["hour"] + item["weekdayNumber"])
          .addEventListener("click", activate);
      }
      return;
    }

    document.getElementById(
      item["hour"] + item["weekdayNumber"]
    ).innerHTML = `<div class="btn btn-wide btn-secondary" onclick="modal${
      item["hour"] + item["weekdayNumber"]
    }.showModal()">
            <h2 class="card-title">${
              item["class"] + item["section"]
            }<span class='room'>${item["room"]}</span></h2>
            <p>PC prenotati: ${item["final_pc_reserved"]}</p>
        </div>`;

    let modal = document.createElement("dialog");
    modal.id = "modal" + item["hour"] + item["weekdayNumber"];
    modal.classList.add("modal");
    modal.innerHTML = `<div class="modal-box">
            <h3 class="font-bold text-lg">Nota del Tecnico</h3>
            <p class="py-4">${item["final_note"]}</p>
            <div class="modal-action">
            <form method="dialog">
                <button class="btn">Chiudi</button>
            </form>
            </div>
        </div>`;
    document.body.appendChild(modal);
  });

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
});
