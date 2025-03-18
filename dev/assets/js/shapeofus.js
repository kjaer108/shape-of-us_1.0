let currentUrl = new URL(window.location.href);
console.log("Current URL:", currentUrl.toString());

// Check if running on localhost (assumes port 8000, 8080, etc., or "localhost" in hostname)
let isLocalhost = currentUrl.hostname === "localhost" || currentUrl.hostname.startsWith("127.") || currentUrl.port;
console.log("Running on localhost?", isLocalhost);


/* *********************************************************************
    Handle language changes
   ********************************************************************* */

function setLanguage(lang) {
    console.log("Setting language to", lang);

    // Create request data object
    const requestData = {
        cmd: "set_language",
        lang: lang
    };

    // Submit data via AJAX
    fetch("src/xhr/shapeofus.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(requestData).toString(),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                changeLanguage(lang);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An unexpected error occurred.");
        });
}

function changeLanguage(lang) {
    let url = new URL(window.location.href);
    let isLocalhost = url.hostname === "localhost" || url.hostname.startsWith("127.") || url.port;

    if (isLocalhost) {
        // Use query parameter for localhost
        url.searchParams.set('lang', lang);
    } else {
        // Use subdirectory for live site
        let pathParts = url.pathname.split('/');

        // Ensure we have a valid language structure in the URL
        if (pathParts.length > 1 && /^[a-z]{2}$/.test(pathParts[1])) {
            pathParts[1] = lang; // Replace the first segment (language)
        } else {
            pathParts.splice(1, 0, lang); // Insert language at the beginning if missing
        }

        url.pathname = pathParts.join('/');
    }

    window.location.href = url.toString(); // Redirect with updated language
}




document.addEventListener("DOMContentLoaded", function() {
    console.log(`
 ____  _                       ___   __ _   _                 
/ ___|| |__   __ _ _ __   ___ / _ \\ / _| | | |___   ___ _   _ 
\\___ \\| '_ \\ / _\` | '_ \\ / _ \\ | | | |_| | | / __| / _ \\ | | |
 ___) | | | | (_| | |_) |  __/ |_| |  _| |_| \\__ \\|  __/ |_| |
|____/|_| |_|\\__,_| .__/ \\___|\\___/|_|  \\___/|___(_)___|\\__,_|
                  |_|                                          
`);



    /* *********************************************************************
        Info and Confirm Modals
       ********************************************************************* */

    function show_info_modal(title, body, buttons = ["OK"], callback = null) {
        const modal = document.getElementById('exampleModal');
        const modalTitle = modal.querySelector(".modal-title");
        const modalBody = modal.querySelector(".modal-body");
        const modalLogout = modal.querySelector(".login-button");

        const loginButtonOutlineHTML = `
    <button data-bs-toggle="modal" class="btn btn-lg btn-outline-primary w-100 m-0 login-button">
        Log ind
    </button>`;

        const loginButtonPrimaryHTML = `
    <button data-bs-toggle="modal" class="btn btn-lg btn-primary w-100 m-0 login-button">
        Log ind
    </button>`;

        const registerButtonHTML = `
    <button class="btn btn-lg btn-brown w-100 m-0 register-button">
        Opret Profil
    </button>`;

        const okButtonHTML = `
    <button class="btn btn-lg btn-primary w-100 m-0">
        OK
    </button>`;

        // Set modal title
        modalTitle.innerHTML = title;

        // Clear existing body content
        modalBody.innerHTML = '';

        console.log(body);
        // Check if bodyContent is an array
        if (Array.isArray(body)) {
            // If it's an array, loop through the items and create <p> elements
            body.forEach(content => {
                const paragraph = document.createElement('p');
                paragraph.innerHTML = content;
                paragraph.classList.add('mb-2');
                modalBody.appendChild(paragraph);
            });
        } else {
            // If it's a single string, create a <p> element for it
            const paragraph = document.createElement('p');
            paragraph.innerHTML = body;
            paragraph.classList.add('mb-2');
            modalBody.appendChild(paragraph);
        }

        // Initialize an empty string to store the final innerHTML
        let modalFooterHTML = '';

        console.log(buttons);

        // Loop through the buttonOrder array and build innerHTML
        buttons.forEach(button => {
            if (button === 'LOGINO') {
                modalFooterHTML += loginButtonOutlineHTML;
            } else if (button === 'LOGINP') {
                modalFooterHTML += loginButtonPrimaryHTML;
            } else if (button === 'REGISTER') {
                modalFooterHTML += registerButtonHTML;
            } else if (button === 'OK') {
                modalFooterHTML += okButtonHTML;
            }
        });

        // Get the modal footer element and insert buttons into the modal footer
        const modalFooter = document.querySelector('#modal-info .modal-footer');
        modalFooter.innerHTML = modalFooterHTML;

        const modalInstance = new bootstrap.Modal(modal, {});

        // Add an event listener to the modal's backdrop (click outside modal)
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                // If the click target is the modal's backdrop, treat it like clicking "OK"
                modalInstance.hide();
                if (typeof callback === "function") {
                    callback();
                }
            }
        });

        // Add an event listener to the "OK" button
        if (buttons.includes('OK')) {
            modal.querySelector(".btn-primary").addEventListener("click", function () {
                modalInstance.hide();
                if (typeof callback === "function") {
                    callback();
                }
            });
        }

        // Add an event listener to the "Log ind" button

        // Check if "LOGIN" is present in the buttons array
        if (buttons.includes('LOGINO') || buttons.includes('LOGINP')) {
            // Get the modal element
            const modal = document.querySelector('#modal-info');

            // Add an event listener for the "LOGIN" button
            modal.querySelector(".login-button").addEventListener("click", function(e){
                bootstrap.Modal.getInstance(modal).hide();
                handle_login_button_click(e);
            });
        }

        // Check if "REGISTRE" is present in the buttons array
        if (buttons.includes('REGISTER')) {
            // Get the modal element
            const modal = document.querySelector('#modal-info');

            // Add an event listener for the "LOGIN" button
            modal.querySelector(".register-button").addEventListener("click", function(e) {
                bootstrap.Modal.getInstance(modal).hide();
                handle_register_button_click(e);
            });
        }

        modalInstance.show();
    }

    function show_confirm_modal(title, body, callbackYes = null, callbackNo = null) {
        const modal = document.getElementById('modal-confirm');
        const modalTitle = modal.querySelector(".modal-title");
        const modalBody = modal.querySelector(".modal-body");
        const yesButton = modal.querySelector(".confirm-yes-button");
        const noButton = modal.querySelector(".confirm-no-button");

        // Set modal title
        modalTitle.innerHTML = title;

        // Clear existing body content
        modalBody.innerHTML = '';

        // Check if bodyContent is an array
        if (Array.isArray(body)) {
            // If it's an array, loop through the items and create <p> elements
            body.forEach(content => {
                const paragraph = document.createElement('p');
                paragraph.innerHTML = content;
                paragraph.classList.add('mb-2');
                modalBody.appendChild(paragraph);
            });
        } else {
            // If it's a single string, create a <p> element for it
            const paragraph = document.createElement('p');
            paragraph.innerHTML = body;
            paragraph.classList.add('mb-2');
            modalBody.appendChild(paragraph);
        }

        // Add event listeners to "Ja" and "Nej" buttons
        yesButton.addEventListener('click', function (event) {
            event.preventDefault();
            modalInstance.hide();
            if (typeof callbackYes === "function") {
                callbackYes();
            }
        });

        noButton.addEventListener('click', function (event) {
            event.preventDefault();
            modalInstance.hide();
            if (typeof callbackNo === "function") {
                callbackNo();
            }
        });

        // Show the modal
        const modalInstance = new bootstrap.Modal(modal, {});
        modalInstance.show();
    }



    /* *********************************************************************
        Handle form.newsletter-signup form submissions
       ********************************************************************* */

    const newsletterForms = document.querySelectorAll("form.newsletter-signup");

    if (newsletterForms) newsletterForms.forEach((form) => {
        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission
            console.log("Newsletter form submitted!", form);

            const emailInput = form.querySelector("input[type='email']");

            if (!emailInput.value.trim()) {
                alert("Please enter an email address!");
                return;
            }

            // Create request data object
            const requestData = {
                cmd: "newsletter-signup",
                email: emailInput.value.trim(),
            };

            // Submit data via AJAX
            fetch("src/xhr/shapeofus.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams(requestData).toString(),
            })
                .then((response) => response.json())
                .then((data) => {
                    console.log(data);

                    if (data.success) {
                        //alert("Thank you for signing up!");
                        form.reset(); // Clear only this form

                        show_info_modal(
                            "Tak for din tilmelding!",
                            "Du er nu tilmeldt vores nyhedsbrev.",
                            ["OK"]
                        )
                    } else {
                        alert(data.error || "An error occurred. Please try again.");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An unexpected error occurred.");
                });
        });
    });
});