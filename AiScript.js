document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.feature-checkbox');
    const cards = document.querySelectorAll('.grid .card');
    const noResultsMessage = document.getElementById('no-results');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterTools);
    });

    function filterTools() {
        const checkedFeatures = Array.from(checkboxes)
            .filter(i => i.checked)
            .map(i => i.value.toLowerCase().trim());

        let visibleCardsCount = 0;

        cards.forEach(card => {
            const cardFeaturesAttr = card.getAttribute('data-features') || '';
            const cardFeatures = cardFeaturesAttr.toLowerCase().split('\n').map(f => f.trim());
            const matchesAll = checkedFeatures.every(feature => cardFeatures.includes(feature));

            if (matchesAll) {
                card.classList.remove('hidden');
                visibleCardsCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (visibleCardsCount === 0 && cards.length > 0) {
            noResultsMessage.style.display = 'block';
        } else {
            noResultsMessage.style.display = 'none';
        }
    }
});