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
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const name = document.getElementById('name').value;
            const message = document.getElementById('successMessage');

            message.textContent = 'Thank you, ' + name + '! You have successfully registered.';
            message.style.display = 'block';

            form.reset();
        });
    }
});