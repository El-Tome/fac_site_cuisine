async function rerollHero() {
    const wrapper = document.getElementById('hero-wrapper');
    const button  = document.getElementById('btn-hero-reroll');
    if (!wrapper || !button) return;

    button.disabled    = true;
    button.textContent = '…';

    const exclude = wrapper.dataset.exclude || '';
    const res     = await fetch(`/hero/random?exclude=${exclude}`);

    if (res.status === 204) {
        button.disabled    = false;
        button.textContent = '↻';
        return;
    }

    const html = await res.text();
    const tmp  = document.createElement('div');
    tmp.innerHTML = html;
    const newHero = tmp.firstElementChild;

    if (newHero) {
        const oldHero = wrapper.querySelector('.hero-card');
        if (oldHero) oldHero.replaceWith(newHero);

        const newId      = newHero.dataset.recipeId;
        const currentIds = exclude.split(',').filter(Boolean);
        currentIds.push(newId);
        wrapper.dataset.exclude = currentIds.join(',');
    }

    button.disabled    = false;
    button.textContent = '↻';
}

document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('btn-hero-reroll');
    if (button) button.addEventListener('click', rerollHero);
});
