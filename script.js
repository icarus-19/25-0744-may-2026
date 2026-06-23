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
    // ===== ADDED: Form validation functions =====
    function showError(input, message) {
        // Remove any existing error for this input
        removeError(input);
        
        const error = document.createElement('div');
        error.className = 'error-message';
        error.style.color = 'red';
        error.style.fontSize = '14px';
        error.style.marginTop = '4px';
        error.style.marginBottom = '8px';
        error.textContent = message;
        
        input.style.borderColor = 'red';
        input.style.borderWidth = '2px';
        
        input.parentNode.insertBefore(error, input.nextSibling);
    }

    function removeError(input) {
        const error = input.parentNode.querySelector('.error-message');
        if (error) error.remove();
        input.style.borderColor = '';
        input.style.borderWidth = '';
    }

    function getErrorMessage(input) {
        const name = input.getAttribute('data-label') || 
                     input.placeholder || 
                     input.name || 
                     input.id || 
                     'This field';
        
        // Custom messages based on field name/id
        if (name.toLowerCase().includes('name') || input.id === 'fullname' || input.id === 'username') {
            return 'Please write their name';
        }
        if (name.toLowerCase().includes('email')) return 'Please enter an email address';
        if (name.toLowerCase().includes('phone')) return 'Please enter a phone number';
        if (name.toLowerCase().includes('message')) return 'Please write a message';
        if (name.toLowerCase().includes('password')) return 'Please enter a password';
        
        return `Please fill out the ${name} field`;
    }
    // ===== END ADDED =====

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

            // ===== CHANGED: Show error message instead of just border =====
            // Check if field is empty (if required)
            if (input.hasAttribute('required') && !input.value.trim()) {
                console.warn(`⚠️ "${input.name || input.id}" is required but empty`);
                showError(input, getErrorMessage(input));
                allValid = false;
            } else {
                removeError(input);
            }
            // ===== END CHANGED =====
        });

        if (allValid) {
            console.log('✅ All fields are valid!');
            // ===== ADDED: Auto-submit if using localStorage =====
            // Check if this is the registration form
            if (form.id === 'registerForm') {
                localStorage.setItem('registered', 'true');
                window.location.href = 'index.html';
            }
            // form.submit(); // Uncomment to actually submit
        } else {
            console.log('❌ Fix the errors above');
        }
    });

    // ===== ADDED: Clear errors on typing =====
    document.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                removeError(this);
            }
        });
    });
    // ===== END ADDED =====

    // ===== ADDED: Check for duplicate form variable =====
    // Your existing registerForm code - removed duplicate const form declaration
    const registerForm = document.getElementById('registerForm');

    if (registerForm) {
        if (localStorage.getItem('registered') === 'true') {
            registerForm.style.display = 'none';
            const doneMessage = document.createElement('p');
            doneMessage.textContent = 'Done! You are already registered.';
            doneMessage.className = 'success-message';
            doneMessage.style.display = 'block';
            registerForm.parentElement.appendChild(doneMessage);
        } else {
            // Remove any existing submit listeners to avoid conflicts
            registerForm.removeEventListener('submit', function() {});
            registerForm.addEventListener('submit', function(event) {
                event.preventDefault();
                // Check if all fields are valid before saving
                const inputs = registerForm.querySelectorAll('input[required], textarea[required], select[required]');
                let allValid = true;
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        showError(input, getErrorMessage(input));
                        allValid = false;
                    }
                });
                if (allValid) {
                    localStorage.setItem('registered', 'true');
                    window.location.href = 'index.html';
                }
            });
        }
    }
    // ===== END ADDED =====
});