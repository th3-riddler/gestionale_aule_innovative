function getPos() {
    let form = document.getElementById('formHour');

    if (this.classList.contains('selected')) {
        this.classList.remove('selected');
        this.firstElementChild.tagName === 'BUTTON' ? this.firstElementChild.classList.add('btn-disabled') : null;
        this.firstElementChild.classList.remove('btn-accent');

        form.removeChild(form.querySelector('input[name="hour[]"][value="' + this.id.split("")[0] + '"]'));
        form.removeChild(form.querySelector('input[name="weekday[]"][value="' + document.querySelectorAll('th')[parseInt(this.id.split("")[1])].textContent + '"]'));
        
        if (form.querySelectorAll('input[name="hour[]"]').length === 0) {
            document.getElementById('submitter').setAttribute('disabled', 'true');
        }
        return;
    }

    if (this.firstElementChild.classList.contains('btn-disabled')) { this.firstElementChild.classList.remove('btn-disabled'); }

    this.firstElementChild.classList.add('btn-accent');

    let columnPos = parseInt(this.id.split("")[1]);
    let headers = document.querySelectorAll('th');
    
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
    document.getElementById('submitter').removeAttribute('disabled');

    let inputHour = document.createElement('input'); 
    let inputDay = document.createElement('input');

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
    if (!td.id) { return; }
    td.addEventListener('click', getPos);
});

