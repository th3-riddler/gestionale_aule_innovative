function activate() {
  if (this.classList.contains("btn-secondary")) {
    let teacherNote =
      this.querySelector("input[name='teacherNote']").value ||
      "Nessuna nota da parte del docente";
    document.getElementById("modalTeacherNote").querySelector("p").innerHTML =
      teacherNote;
    return;
  }
  if (this.classList.contains("btn-accent")) {
    this.classList.remove("btn-accent");
    document
      .getElementById("formReservation")
      .querySelector("button[type='submit']")
      .setAttribute("disabled", "true");
    document.getElementById("technicianNote").setAttribute("disabled", "true");
    document.getElementById("technicianNote").value = "";
    document.getElementById("modalTeacherNote").querySelector("p").innerHTML =
      "";
    document.getElementById("teacherNote").classList.add("hidden");
    return;
  }

  document
    .querySelectorAll(".btn-accent")
    .forEach((item) => item.classList.remove("btn-accent"));

  this.classList.add("btn-accent");
  document
    .getElementById("formReservation")
    .querySelector("button[type='submit']")
    .removeAttribute("disabled");
  document.getElementById("technicianNote").removeAttribute("disabled");

  document.getElementById("modalTeacherNote").querySelector("p").innerHTML =
    this.querySelector("input[type='hidden']").value;
  if (this.querySelector("input[type='hidden']").value === "") {
    document.getElementById("teacherNote").classList.add("hidden");
  } else {
    document.getElementById("teacherNote").classList.remove("hidden");
  }

  document
    .getElementById("formReservation")
    .querySelectorAll("input[type='hidden']")
    .forEach((item) => item.remove());
  let columnIndex =
    Array.from(this.parentElement.parentNode.children).findIndex(
      (element) => element === this.parentElement
    ) - 1;
  console.log([columnIndex, this.parentElement.parentNode.rowIndex]);
  let date = document.querySelectorAll(".date")[columnIndex].textContent;
  let room = this.querySelector("span.room").textContent;
  let day = document.querySelectorAll(".day")[columnIndex].textContent;
  let hour = this.parentElement.parentNode.rowIndex;

  for (let i = 0; i < 4; i++) {
    let input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("name", ["date", "room", "weekday", "hour"][i]);
    input.setAttribute("value", [date, room, day, hour][i]);
    document.getElementById("formReservation").appendChild(input);
  }
}

function setThemeLocalStorage() {
  console.log(this.value);
  localStorage.setItem("theme", this.value);
}

function switchStack() {
  let cell = this

  if(cell.classList.contains("arrow")){
    cell = cell.parentElement;
  }

  if(cell.firstElementChild.classList.contains("arrow")){
    return;
  }

  let firstEl = cell.firstElementChild.cloneNode(true);
  let lastEl = cell.lastElementChild
  cell.insertBefore(firstEl, lastEl);
  firstEl.addEventListener("click", activate);
  cell.removeChild(cell.firstElementChild);

  let columnIndex =
    Array.from(cell.parentNode.children).findIndex(
      (element) => element === cell
    ) - 1;

  let toast = document.querySelector(".toast");
  let alert = document.createElement("div");
  alert.classList.add("alert", "alert-info");
  let span = document.createElement("span");
  span.textContent = `Nuova prenotazione visualizzata: ${
    cell.firstElementChild.querySelector("span.room").textContent
  } - ${document.querySelectorAll(".date")[columnIndex].textContent} `;
  alert.appendChild(span);
  toast.appendChild(alert);
  setTimeout(() => {
    alert.remove();
  }, 3000);
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

  document.querySelectorAll(".reservation").forEach((reservation) => {
    reservation.addEventListener("click", activate);
  });

  document.querySelectorAll(".stack").forEach((stack) => {
    stack.addEventListener("mouseenter", function () {
      /*let firstEl = stack.firstElementChild.cloneNode(true);
      stack.appendChild(firstEl);
      firstEl.addEventListener("click", activate);
      stack.removeChild(stack.firstElementChild);

      let columnIndex =
        Array.from(stack.parentNode.children).findIndex(
          (element) => element === stack
        ) - 1;

      let toast = document.querySelector(".toast");
      let alert = document.createElement("div");
      alert.classList.add("alert", "alert-info");
      let span = document.createElement("span");
      span.textContent = `Nuova prenotazione visualizzata: ${
        stack.firstElementChild.querySelector("span.room").textContent
      } - ${document.querySelectorAll(".date")[columnIndex].textContent} `;
      alert.appendChild(span);
      toast.appendChild(alert);
      setTimeout(() => {
        alert.remove();
      }, 3000);*/
    });
    /*stack.addEventListener("mouseleave", function () {
      stack.removeChild(stack.querySelector(".arrow"));
    });*/
  });

  document.querySelectorAll("td").forEach((td) => {
    if (!td.id) {
      return;
    }
    if (td.firstElementChild.tagName === "BUTTON") {
      return;
    }
    if (td.classList.contains("stack")) {
      let arrow = document.createElement("div");
      arrow.classList.add(
        "btn",
        "w-fit",
        "btn-ghost",
        "arrow",
        "p-0",
        "m-1",
        "ml-auto"
      );
      arrow.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 320 512">
                          <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
                         </svg>`;

      td.appendChild(arrow);
      setTimeout(() => {
        arrow.classList.add("z-10");
      }, 300);
      arrow.addEventListener("click", switchStack);
    }
  });

  /*document.querySelectorAll(".stack").forEach((stack) => {
    stack.addEventListener("wheel", function () {
      let firstEl = stack.firstElementChild.cloneNode(true);
      stack.appendChild(firstEl);
      firstEl.addEventListener("click", activate);
      stack.removeChild(stack.firstElementChild);

      let columnIndex =
        Array.from(stack.parentNode.children).findIndex(
          (element) => element === stack
        ) - 1;

      let toast = document.querySelector(".toast");
      let alert = document.createElement("div");
      alert.classList.add("alert", "alert-info");
      let span = document.createElement("span");
      span.textContent = `Nuova prenotazione visualizzata: ${
        stack.firstElementChild.querySelector("span.room").textContent
      } - ${document.querySelectorAll(".date")[columnIndex].textContent} `;
      alert.appendChild(span);
      toast.appendChild(alert);
      setTimeout(() => {
        alert.remove();
      }, 3000);
    });
  });*/

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
