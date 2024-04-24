function activate() {
    if (this.classList.contains("reserved")) {
        return;
    }

    if (this.classList.contains("selected")) {
        this.classList.remove("selected");
        return;
    }

    document.querySelectorAll(".selected").forEach(function(item) {
        item.classList.remove("selected");
    });

    this.classList.add("selected");
    document.getElementById("inputPcQt").max = parseInt(this.querySelector(".maxPc").textContent);
    let weekday = document.querySelectorAll("th")[parseInt(this.id.split("")[1])].querySelector("span").textContent;
    
    let current = document.getElementById("current").textContent;
    let monday_week = new Date(current.split(" - ")[0]);
    let date = new Date(monday_week.getFullYear(), monday_week.getMonth(), monday_week.getDate() + parseInt(this.id.split("")[1]));

    let presentInputs = document.getElementById("formReservation").querySelectorAll("input[type='hidden']");
    presentInputs.forEach(function(item) { item.remove(); });

    for(let i = 0; i < 5; i++) {
        let input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", ["date", "room", "weekday", "hour", "cart_id"][i]);
        input.setAttribute("value", [date.toISOString().split("T")[0], this.querySelector(".room").textContent, weekday, this.id.split("")[0], this.value][i]);
        document.getElementById("formReservation").appendChild(input);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let current = document.getElementById("current");
    let dateQuery = new URLSearchParams(window.location.search).get("date");
    let current_date = dateQuery ? new Date(dateQuery) : new Date();
    let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 1);
    let saturday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 6);
    current.textContent = monday_week.toDateString() + " - " + saturday_week.toDateString();

    document.getElementById("previous").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - 6);
        window.location.href = window.location.href.split("?")[0] + "?date=" + monday_week.toISOString().split("T")[0];
    });

    document.getElementById("next").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() + 8);
        
        window.location.href = window.location.href.split("?")[0] + "?date=" + monday_week.toISOString().split("T")[0];
    });

    scriptValues.forEach(function(item) {
        document.getElementById(item["hour"] + item["weekdayNumber"]).innerHTML = item["class"] + item["section"] + " <span class='room'>" + item["room"] + "</span><br>PC disponibili: <span class='maxPc'>" + item["final_pc_number"] + "</span><br>" + (item["final_note"] ? "Nota tecnico: " + item["final_note"] : "");
        item["had_reservation"] ? document.getElementById(item["hour"] + item["weekdayNumber"]).classList.add("reserved") : null;
        document.getElementById(item["hour"] + item["weekdayNumber"]).addEventListener("click", activate);
        document.getElementById(item["hour"] + item["weekdayNumber"]).value = item["cart_id"];
    });
});
