function getPos() {
    let cell = this;

    if (cell.parentNode.tagName === 'DIV') {
        cell = this.parentNode.parentNode.parentNode;
    }

    let form = document.getElementById('formHour');

    if (cell.classList.contains('selected')) { // if the cell is already selected, i'm trying to deselect it
        cell.classList.remove('selected');
        cell.firstElementChild.tagName === 'BUTTON' ? cell.firstElementChild.classList.add('btn-disabled') : null;
        cell.firstElementChild.classList.remove('btn-accent', 'hover:bg-accent/50');
        cell.firstElementChild.classList.add('bg-primary', 'hover:bg-primary/50');
        if(cell.firstElementChild.tagName !== 'BUTTON'){
            cell.firstElementChild.lastElementChild.firstElementChild.classList.add('btn-accent');
            cell.firstElementChild.lastElementChild.firstElementChild.classList.remove('btn-info');
        }
        
        form.removeChild(form.querySelector('input[name="hour[]"][value="' + cell.id.split("")[0] + '"]'));
        form.removeChild(form.querySelector('input[name="weekday[]"][value="' + document.querySelectorAll('th')[parseInt(cell.id.split("")[1])].textContent + '"]'));
        
        if (form.querySelectorAll('input[name="hour[]"]').length === 0) {
            document.getElementById('submitter').setAttribute('disabled', 'true');
        }
        return;
    }

    if (cell.firstElementChild.classList.contains('btn-disabled')) { cell.firstElementChild.classList.remove('btn-disabled'); }

    cell.firstElementChild.classList.add('btn-accent', 'hover:bg-accent/50');
    cell.firstElementChild.classList.remove('bg-primary', 'hover:bg-primary/50');

    if(cell.firstElementChild.tagName !== 'BUTTON'){
        cell.firstElementChild.lastElementChild.firstElementChild.classList.remove('btn-accent');
        cell.firstElementChild.lastElementChild.firstElementChild.classList.add('btn-info');
    }

    let columnPos = parseInt(cell.id.split("")[1]);
    let headers = document.querySelectorAll('th');
    
    let hour = parseInt(cell.id.split("")[0]);
    let weekday = headers[columnPos].textContent;

    if(cell.classList.contains('selected')) {
        cell.classList.remove('selected');
        let inputHour = form.querySelector('input[name="hour[]"][value="' + hour + '"]');
        let inputDay = form.querySelector('input[name="weekday[]"][value="' + weekday + '"]');
        form.removeChild(inputHour);
        form.removeChild(inputDay);
        return;
    }
    
    cell.classList.add('selected');
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


function viewOptions() {
    let customizationDiv = document.createElement('div');
    customizationDiv.classList.add('flex', 'absolute', 'w-36', 'h-fit', 'p-1', 'justify-center', 'items-center', 'gap-1')

    customizationDiv.innerHTML = `<div class='btn btn-square btn-outline ${this.classList.contains('btn-accent') ? 'btn-info' : 'btn-accent'} border-2 btn-md'>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 512 512" stroke="currentColor">
                                            <path fill="currentColor" d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z"/>
                                        </svg>
                                    </div>
                                  <a href="../API/deleteTeacherSchedule.php?hour=${this.parentNode.id.split("")[0]}&weekday=${document.querySelectorAll('th')[parseInt(this.parentNode.id.split("")[1])].textContent}&aula=${document.getElementsByClassName('tabs-boxed')[0].querySelector('.tab-active').textContent}" class='btn btn-square btn-outline btn-error border-2 btn-md'>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 384 512" stroke="currentColor">
                                            <path fill="currentColor" d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
                                        </svg>
                                    </div>
                                  </a>`;

    customizationDiv.firstElementChild.addEventListener('click', getPos);
    this.appendChild(customizationDiv);
}

/*document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) { return; }
    td.addEventListener('click', getPos);
});*/

document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) { return; }
    if (td.firstElementChild.tagName === 'BUTTON') {
        td.addEventListener('click', getPos);
    }
});

document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) { return; }
    if (td.firstElementChild.tagName === 'BUTTON') { return; }
    td.firstElementChild.addEventListener('mouseenter', viewOptions);
});

document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) { return; }
    td.firstElementChild.addEventListener('mouseleave', function() {
        //this.classList.remove('opacity-50');
        children = this.querySelectorAll('div'); // takes all the children of the first element of the td
        children.forEach(function(child) { // for each child
            child.parentNode.removeChild(child); // remove it
        });
    });
});