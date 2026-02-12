// Vista previa de stats en equipamiento.
// Requiere el endpoint /game/equipamiento/stats-preview.

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('equipamiento-form');
    if (!form) return;

    const statBox = document.getElementById('stats-preview');
    const selects = form.querySelectorAll('select');

    function getSelectedEquipment() {
        const data = {};
        selects.forEach(sel => {
            data[sel.name] = sel.value || null;
        });
        return data;
    }

    function updateStatsPreview() {
        const data = getSelectedEquipment();
        fetch('/game/equipamiento/stats-preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(data),
        })
            .then(res => res.json())
            .then(json => {
                if (json.stats && statBox) {
                    statBox.innerHTML = Object.entries(json.stats).map(([k, v]) =>
                        `<span class='me-2'>${k.charAt(0).toUpperCase() + k.slice(1)}: <strong>${v}</strong></span>`
                    ).join(' ');
                }
            });
    }

    selects.forEach(sel => {
        sel.addEventListener('change', updateStatsPreview);
    });
});
