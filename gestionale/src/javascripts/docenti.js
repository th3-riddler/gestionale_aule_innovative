function activate() {
    if (this.style.backgroundColor == "red") {
        this.style.backgroundColor = "white";
        return;
    } else {
        this.style.backgroundColor = "red";
        document.getElementById("inp_n_pc").max = parseInt(this.textContent.split(" ").pop());
        let day_of_the_week = document.querySelectorAll("th")[parseInt(this.id.split("")[1])].textContent;
        
        let current = document.getElementById("current").textContent;
        let monday_week = new Date(current.split(" - ")[0]);
        let date = new Date(monday_week.getFullYear(), monday_week.getMonth(), monday_week.getDate() + parseInt(this.id.split("")[1]));

        let input1 = document.createElement("input");
        input1.setAttribute("type", "hidden");
        input1.setAttribute("name", "data");
        input1.setAttribute("value", date.toISOString().split("T")[0]);
        document.getElementById("form_prenotazione").appendChild(input1);

        let input2 = document.createElement("input");
        input2.setAttribute("type", "hidden");
        input2.setAttribute("name", "giorno");
        input2.setAttribute("value", day_of_the_week);
        document.getElementById("form_prenotazione").appendChild(input2);

        let input3 = document.createElement("input");  
        input3.setAttribute("type", "hidden");
        input3.setAttribute("name", "aula");
        input3.setAttribute("value", this.textContent.split(" ")[1]);
        document.getElementById("form_prenotazione").appendChild(input3);

        let input4 = document.createElement("input");
        input4.setAttribute("type", "hidden");
        input4.setAttribute("name", "ora");
        input4.setAttribute("value", this.id.split("")[0]);
        document.getElementById("form_prenotazione").appendChild(input4);

        let input5 = document.createElement("input");
        input5.setAttribute("type", "hidden");
        input5.setAttribute("name", "id_carrello");
        input5.setAttribute("value", this.value);
        document.getElementById("form_prenotazione").appendChild(input5);


    }
}




document.addEventListener("DOMContentLoaded", function() {
    let current = document.getElementById("current");
    let current_date = new Date();
    let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 1);
    let saturday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 6);
    current.textContent = monday_week.toDateString() + " - " + saturday_week.toDateString();

    document.getElementById("previous").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - 7);
        let saturday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - 2);
        current.textContent = monday_week.toDateString() + " - " + saturday_week.toDateString();
    });

    document.getElementById("next").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() + 7);
        let saturday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() + 12);
        current.textContent = monday_week.toDateString() + " - " + saturday_week.toDateString();
    });
});

