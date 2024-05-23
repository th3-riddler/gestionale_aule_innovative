function activate() {
  let cell = this;

  if (cell.parentNode.tagName === "DIV") {
    cell = this.parentNode.parentNode.parentNode;
  }

  if (cell.classList.contains("selected")) {
    cell.classList.remove("selected");
    cell.firstElementChild.classList.remove("btn-accent");
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

  cell.classList.add("selected");
  cell.firstElementChild.classList.add("btn-accent");
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
    cell.querySelector(".maxPc").textContent
  );
  let weekday = document
    .querySelectorAll("th")
    [parseInt(cell.id.split("")[1])].querySelector("span").textContent;

  let current = document.getElementById("current");
  let monday_week = new Date(current.dataset.monday_week);
  let date = new Date(
    monday_week.getFullYear(),
    monday_week.getMonth(),
    monday_week.getDate() + parseInt(cell.id.split("")[1])
  );

  let presentInputs = document
    .getElementById("formReservation")
    .querySelectorAll("input[type='hidden']");
  presentInputs.forEach(function (item) {
    if (item.name == "teacher_email") {
      return;
    }
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
        cell.querySelector(".room").textContent,
        weekday,
        cell.id.split("")[0],
        cell.value,
      ][i]
    );
    document.getElementById("formReservation").appendChild(input);
  }
}

function setThemeLocalStorage() {
  console.log(this.value);
  localStorage.setItem("theme", this.value);
}

function deleteReservation(event) {
  event.preventDefault();

  fetch(`http://${server}/API/deleteReservation.php?token=${token}`, {
    method: "POST",
    body: JSON.stringify({
      hour: params["hour"],
      weekday: params["weekday"],
      room: params["room"],
      date: params["date"],
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      window.location.href = window.location.href + "&error=" + data["status"];
      console.log(data);
    });
}

let params = {};

function assignParams(hour, weekday, room, date) {
  params = {
    hour: hour,
    weekday: weekday,
    room: room,
    date: date,
  };
  document.getElementById("modalDeleteReservation").showModal();
}

function viewOptions() {
  let customizationDiv = document.createElement("div");
  customizationDiv.classList.add(
    "flex",
    "absolute",
    "w-24",
    "h-fit",
    "p-1",
    "justify-evenly",
    "items-center",
    "gap-1"
  );

  customizationDiv.innerHTML = `<div class='btn btn-square btn-outline btn-info border-2 btn-sm'>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 512 512" stroke="currentColor">
                                            <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                                        </svg>
                                    </div>
                                  <a class='btn btn-square btn-outline btn-error border-2 btn-sm' onclick="assignParams(${
                                    this.parentNode.id.split("")[0]
                                  }, '${
    document
      .querySelectorAll("th")
      [parseInt(this.parentNode.id.split("")[1])].querySelector("span")
      .textContent
  }', '${this.firstElementChild.querySelector("span").textContent}', '${
    document.querySelectorAll("th")[parseInt(this.parentNode.id.split("")[1])]
      .lastChild.textContent
  }')">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 384 512" stroke="currentColor">
                                            <path fill="currentColor" d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
                                        </svg>
                                    </div>
                                  </a>`;

  customizationDiv.firstElementChild.addEventListener("click", function () {
    let modalId = "modal" + this.parentNode.parentNode.parentNode.id; // id del td
    document.getElementById(modalId).showModal(); //mostra il modale associato al td
  });
  this.appendChild(customizationDiv);
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
  let options = { year: "numeric", month: "long", day: "numeric" };
  current.textContent =
    monday_week.toLocaleDateString("it-IT", options) +
    " - " +
    saturday_week.toLocaleDateString("it-IT", options);

  current.dataset.monday_week = monday_week.toISOString();
  current.dataset.saturday_week = saturday_week.toISOString();
  document.getElementById("previous").addEventListener("click", function () {
    let current = document.getElementById("current");
    let current_date = new Date(current.dataset.monday_week);
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
    let current_date = new Date(current.dataset.monday_week);
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
        if (
          document
            .getElementById(item["hour"] + item["weekdayNumber"])
            .classList.contains("bg-secondary")
        ) {
          return;
        }
        document
          .getElementById(item["hour"] + item["weekdayNumber"])
          .addEventListener("click", activate);
      }
      return;
    }

    document.getElementById(
      item["hour"] + item["weekdayNumber"]
    ).innerHTML = `<div class="btn btn-wide bg-secondary text-black hover:bg-secondary/30 hover:text-black/30 transition-all duration-200">
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

    document.querySelectorAll("td").forEach(function (td) {
      if (!td.id) {
        return;
      }
      if (td.firstElementChild.tagName === "BUTTON") {
        return;
      }
      if (td.firstElementChild.classList.contains("bg-secondary")) {
        td.firstElementChild.addEventListener("mouseenter", viewOptions);
      }
    });

    document.querySelectorAll("td").forEach(function (td) {
      if (!td.id) {
        return;
      }
      td.firstElementChild.addEventListener("mouseleave", function () {
        //this.classList.remove('opacity-50');
        children = this.querySelectorAll("div"); // takes all the children of the first element of the td
        children.forEach(function (child) {
          // for each child
          child.parentNode.removeChild(child); // remove it
        });
      });
    });
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

let server = window.location.hostname;
let token = document.cookie
  .split(";")
  .find((cookie) => {
    return cookie.includes("token");
  })
  .split("=")[1];

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
  toast.classList.remove("-z-10");
  setTimeout(() => {
    alert.classList.add("opacity-0");
    setTimeout(() => {
      toast.classList.add("-z-10");
    }, 1000);
  }, 3000);
}
