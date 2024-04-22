document.addEventListener("DOMContentLoaded", function() {
    let current = document.getElementById("current");
    let current_date = window.location.search.split("=")[1] ? new Date(window.location.search.split("=")[1]) : new Date();
    let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 1);
    let saturday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - current_date.getDay() + 6);
    current.textContent = monday_week.toDateString() + " - " + saturday_week.toDateString();

    document.getElementById("previous").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() - 7);
        window.location.href = window.location.href.split("?")[0] + "?data=" + monday_week.toISOString().split("T")[0];
    });

    document.getElementById("next").addEventListener("click", function() {
        let current = document.getElementById("current");
        let current_date = new Date(current.textContent.split(" - ")[0]);
        let monday_week = new Date(current_date.getFullYear(), current_date.getMonth(), current_date.getDate() + 7);
        
        window.location.href = window.location.href.split("?")[0] + "?data=" + monday_week.toISOString().split("T")[0];
    });
});
