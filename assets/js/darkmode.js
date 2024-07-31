document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('#darkModeToggle');
    const body = document.body;
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('body-dark');
        body.classList.remove('body-light');
    } else {
        body.classList.add('body-light');
        body.classList.remove('body-dark');
    }
    toggle.addEventListener('click', () => {
        if (body.classList.contains('body-light')) {
            body.classList.add('body-dark');
            body.classList.remove('body-light');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            body.classList.add('body-light');
            body.classList.remove('body-dark');
            localStorage.setItem('darkMode', 'disabled');
        }
    });
});