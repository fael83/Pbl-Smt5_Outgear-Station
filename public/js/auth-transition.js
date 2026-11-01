
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('[data-auth-transition]');

    links.forEach(link => {
        link.addEventListener('click', event => {
            if (
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }

            event.preventDefault();

            const destination = link.href;
            const card = document.querySelector('.login-card, .register-card');

            if (!card) {
                window.location.href = destination;
                return;
            }

            card.classList.add('auth-leaving');

            setTimeout(() => {
                window.location.href = destination;
            }, 250);
        });
    });
});
