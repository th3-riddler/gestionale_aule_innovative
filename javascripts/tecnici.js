function getPos(e) {
    document.querySelectorAll('.selected').forEach(function(td) {
        td.classList.remove('selected');
    });
    this.classList.add('selected');
    let columnPos = parseInt(this.id.split("")[1]);
    let headers = document.querySelectorAll('th');

    let hour = parseInt(this.id.split("")[0]);
    let day = headers[columnPos].textContent;

    let form = document.getElementById('form_hour');

    let inputHour = form.querySelector('input[name="hour"]') ?? document.createElement('input'); // ?? = se non esiste crea un elemento input
    let inputDay = form.querySelector('input[name="day"]') ?? document.createElement('input');

    inputHour.setAttribute('type', 'hidden');
    inputHour.setAttribute('name', 'hour');
    inputHour.setAttribute('value', hour);

    inputDay.setAttribute('type', 'hidden');
    inputDay.setAttribute('name', 'day');
    inputDay.setAttribute('value', day);

    form.appendChild(inputHour);
    form.appendChild(inputDay);
}

document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) {
        return;
    }

    td.addEventListener('click', getPos);
});