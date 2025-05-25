// Main JavaScript file for OpenCart Manager

document.addEventListener('DOMContentLoaded', function () {
    console.log('OpenCart Manager JS Initialized');

    // Theme toggle functionality
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');
    const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;

    if (currentTheme) {
        document.documentElement.setAttribute('data-bs-theme', currentTheme);
        if (themeIcon) {
            if (currentTheme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    } else { // Default to light theme if no preference stored
        document.documentElement.setAttribute('data-bs-theme', 'light');
         if (themeIcon) {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
         }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            let theme = document.documentElement.getAttribute('data-bs-theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                localStorage.setItem('theme', 'light');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                 if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
            }
        });
    }

    // Handle language change (basic example - sends user to new URL)
    // More sophisticated handling might involve AJAX or storing preference
    const languageLinks = document.querySelectorAll('#languageDropdown .dropdown-item');
    languageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // const langCode = this.getAttribute('href').split('lang=')[1];
            // console.log(`Language changed to: ${langCode}`);
            window.location.href = this.getAttribute('href');
        });
    });

});

// Example function for modal dialogs (you'll need Bootstrap's JS for this)
function showModal(modalId) {
    var myModal = new bootstrap.Modal(document.getElementById(modalId));
    myModal.show();
}

// Example function for progress indicators (very basic)
function showProgress(elementId, message = "Loading...") {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = `<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">${message}</div></div>`;
        element.style.display = 'block';
    }
}

function hideProgress(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = 'none';
        element.innerHTML = '';
    }
}
