document.querySelectorAll('td').forEach(function(td) {
    if (!td.id) {
        return;
    }
    td.addEventListener('click', getPos);
});