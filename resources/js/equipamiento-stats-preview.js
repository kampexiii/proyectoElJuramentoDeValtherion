// Actualiza los stats en tiempo real en la pantalla de equipamiento
// Requiere que el backend exponga un endpoint para calcular stats dados los IDs de equipo seleccionados

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
