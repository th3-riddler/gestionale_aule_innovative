function getPos(e) {
    let columnPos = parseInt(this.id.split("")[1]);
    let headers = document.querySelectorAll('th');
    let form = document.getElementById('formHour');
    
    let hour = parseInt(this.id.split("")[0]);
    let weekday = headers[columnPos].textContent;

    if(this.classList.contains('selected')) {
        this.classList.remove('selected');
        let inputHour = form.querySelector('input[name="hour[]"][value="' + hour + '"]');
        let inputDay = form.querySelector('input[name="weekday[]"][value="' + weekday + '"]');
        form.removeChild(inputHour);
        form.removeChild(inputDay);
        return;
    }
    this.classList.add('selected');


    let inputHour = form.querySelector('input[name="hour"]') ?? document.createElement('input'); // ?? = se non esiste crea un elemento input
    let inputDay = form.querySelector('input[name="weekday"]') ?? document.createElement('input');

    inputHour.setAttribute('type', 'hidden');
    inputHour.setAttribute('name', 'hour[]');
    inputHour.setAttribute('value', hour);

    inputDay.setAttribute('type', 'hidden');
    inputDay.setAttribute('name', 'weekday[]');
    inputDay.setAttribute('value', weekday);

    form.appendChild(inputHour);
    form.appendChild(inputDay);
}

document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) {
        return;
    }

    td.addEventListener('click', getPos);
});

