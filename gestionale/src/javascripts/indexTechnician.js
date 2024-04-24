function activate() {
    if (this.classList.contains("selected")) {
        this.classList.remove("selected");
        return;
    }

    document.querySelectorAll(".selected").forEach((item) => item.classList.remove("selected"));
    this.classList.add("selected");

    document.getElementById("form_prenotazione").querySelectorAll("input[type='hidden']").forEach((item) => item.remove());
    let columnIndex = Array.from(this.parentElement.parentNode.children).findIndex((element) => element === this.parentElement) - 1;
    let date = document.querySelectorAll(".date")[columnIndex].textContent;
    let room = this.children[1].querySelector("span").textContent;
    let day = document.querySelectorAll(".day")[columnIndex].textContent;
    let hour = this.parentElement.parentNode.rowIndex;
    
    for (let i = 0; i < 4; i++) {
        let input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", ["data", "aula", "giorno", "ora"][i]);
        input.setAttribute("value", [date, room, day, hour][i]);
        document.getElementById("form_prenotazione").appendChild(input);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let current = document.getElementById("current");
    let dateQuery = new URLSearchParams(window.location.search).get("data");
    let current_date = dateQuery ? new Date(dateQuery) : new Date();
    let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 1);
    let saturday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 6);
    current.textContent = monday_week.toDateString() + " - " + saturday_week.toDateString();

    document.getElementById("previous").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - 6);
        window.location.href = window.location.href.split("?")[0] + "?data=" + monday_week.toISOString().split("T")[0];
    });

    document.getElementById("next").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() + 8);
        
        window.location.href = window.location.href.split("?")[0] + "?data=" + monday_week.toISOString().split("T")[0];
    });

    document.querySelectorAll(".prenotazione").forEach(prenotazione => { prenotazione.addEventListener("click", activate); });
});