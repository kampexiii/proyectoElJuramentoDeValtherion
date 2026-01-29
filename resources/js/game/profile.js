function setAvailability(target, ok, message) {
    if (!target) {
        return;
    }

    target.textContent = message || '';
    target.classList.remove('text-success', 'text-danger');
    if (message) {
        target.classList.add(ok ? 'text-success' : 'text-danger');
    }
}

function checkAvailability(url, field, value, target) {
    if (!value) {
        setAvailability(target, false, '');
        return;
    }

    fetch(`${url}?field=${encodeURIComponent(field)}&value=${encodeURIComponent(value)}`, {
        headers: {
            'Accept': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            setAvailability(target, !!data.available, data.message || '');
        })
        .catch(() => {
            setAvailability(target, false, 'No disponible.');
        });
}

function initProfileAvailability() {
    const form = document.getElementById('profile-form');
    if (!form) {
        return;
    }

    const url = form.dataset.availabilityUrl;
    if (!url) {
        return;
    }

    const nameInput = document.getElementById('profile_name');
    const emailInput = document.getElementById('profile_email');
    const nameStatus = document.getElementById('name-availability');
    const emailStatus = document.getElementById('email-availability');

    if (nameInput) {
        nameInput.addEventListener('blur', () => {
            const value = nameInput.value.trim();
            const current = nameInput.dataset.current || '';
            if (value === current) {
                setAvailability(nameStatus, true, 'Disponible');
                return;
            }
            checkAvailability(url, 'name', value, nameStatus);
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            const value = emailInput.value.trim();
            const current = emailInput.dataset.current || '';
            if (value === current) {
                setAvailability(emailStatus, true, 'Disponible');
                return;
            }
            checkAvailability(url, 'email', value, emailStatus);
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProfileAvailability);
} else {
    initProfileAvailability();
}
