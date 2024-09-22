document.addEventListener('DOMContentLoaded', function () {
    var dropdowns = document.getElementsByClassName("dropdown-btn");

    for (var i = 0; i < dropdowns.length; i++) {
        dropdowns[i].addEventListener("click", function () {
            // Close any currently open dropdowns
            for (var j = 0; j < dropdowns.length; j++) {
                if (dropdowns[j] !== this) {
                    dropdowns[j].classList.remove("active");
                    dropdowns[j].nextElementSibling.style.display = "none";
                }
            }

            // Toggle the active class on the clicked button
            this.classList.toggle("active");

            // Toggle the dropdown content visibility
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    }

    // Close all dropdowns if the user clicks outside of any dropdown
    window.onclick = function (event) {
        if (!event.target.matches('.dropdown-btn')) {
            var dropdownContents = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdownContents.length; i++) {
                var openDropdown = dropdownContents[i];
                if (openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                    // Also remove the 'active' class from the button
                    openDropdown.previousElementSibling.classList.remove('active');
                }
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    var alertIcon = document.querySelector('.alert-icon');
    var alertDropdown = document.querySelector('.alert-dropdown');

    alertIcon.addEventListener('click', function (event) {
        event.preventDefault();
        alertDropdown.style.display = (alertDropdown.style.display === 'block') ? 'none' : 'block';
    });

    // Close the dropdown if the user clicks outside of it
    window.addEventListener('click', function (event) {
        if (!event.target.closest('.alert-icon') && !event.target.closest('.alert-dropdown')) {
            alertDropdown.style.display = 'none';
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    var textCells = document.getElementsByClassName('text-skip');

    for (var i = 0; i < textCells.length; i++) {
        var text = textCells[i].textContent || textCells[i].innerText;
        var words = text.split(' ').slice(0, 5).join(' ');
        textCells[i].textContent = words + '...';
    }
});


const dropdown = document.querySelector('.input-dropdown-content');
dropdown.addEventListener('click', function (event) {
    const target = event.target;
    if (target.tagName === 'INPUT' && target.type === 'checkbox') {
        target.checked = !target.checked; // Toggle checkbox state
    }
});



