window.onload = function() {
    const overlay = document.createElement('div');
    overlay.id = 'welcomePopup';
    overlay.className = 'popup-overlay';

    const box = document.createElement('div');
    box.className = 'popup-box';

    const heading = document.createElement('h2');
    heading.textContent = 'Welcome to A.A.S!';

    const message = document.createElement('p');
    message.textContent = 'Discover amazing African art, connect with artists, and explore unique pieces.';

    const button = document.createElement('button');
    button.textContent = 'Continue';
    button.onclick = function() {
        overlay.style.display = 'none';
    };

    box.appendChild(heading);
    box.appendChild(message);
    box.appendChild(button);
    overlay.appendChild(box);
    document.body.appendChild(overlay);

    overlay.style.display = 'flex';
};

document.addEventListener('DOMContentLoaded', function() {
     const form = document.querySelector('form');
    
    if (!form) {
        console.error('No form found on page');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop actual submission for testing
        
        const inputs = form.querySelectorAll('input, textarea, select');
        let allValid = true;

        inputs.forEach(input => {
            // Check if input has a label
            const label = document.querySelector(`label[for="${input.id}"]`);
            
            if (!label) {
                console.warn(`⚠️ Input "${input.name || input.id}" has no label`);
                allValid = false;
            }

            // Check if field is empty (if required)
            if (input.hasAttribute('required') && !input.value.trim()) {
                console.warn(`⚠️ "${input.name || input.id}" is required but empty`);
                input.style.borderColor = 'red';
                allValid = false;
            } else {
                input.style.borderColor = ''; // Reset
            }
        });

        if (allValid) {
            console.log('✅ All fields are valid!');
            // form.submit(); // Uncomment to actually submit
        } else {
            console.log('❌ Fix the errors above');
        }
    });
    const form = document.getElementById('registerForm');

    if (form) {
        if (localStorage.getItem('registered') === 'true') {
            form.style.display = 'none';
            const doneMessage = document.createElement('p');
            doneMessage.textContent = 'Done! You are already registered.';
            doneMessage.className = 'success-message';
            doneMessage.style.display = 'block';
            form.parentElement.appendChild(doneMessage);
        } else {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                localStorage.setItem('registered', 'true');
                window.location.href = 'index.html';
            });
        }
    }
});

form.addEventListener('submit', function(event) {
    event.preventDefault();

    const inputs = form.querySelectorAll('input, select');
    let hasEmpty = false;

    inputs.forEach(function(input) {
        if (input.value.trim() === '') {
            input.style.border = '2px solid red';
            hasEmpty = true;
        } else {
            input.style.border = '';
        }
    });

    if (hasEmpty) {
        let warning = document.getElementById('warningText');
        if (!warning) {
            warning = document.createElement('p');
            warning.id = 'warningText';
            warning.style.color = 'red';
            warning.textContent = "Don't leave blanks!";
            form.appendChild(warning);
        }
        return;
    }

    localStorage.setItem('registered', 'true');
    window.location.href = 'index.html';
});
