document.addEventListener('DOMContentLoaded', () => {
    const btn   = document.getElementById('like-btn');
    const count = document.getElementById('like-count');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        btn.disabled = true;

        const body = new URLSearchParams({ _token: btn.dataset.token });
        const res  = await fetch(`/recipe/${btn.dataset.recipeId}/like`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    body.toString(),
        });

        if (!res.ok) { btn.disabled = false; return; }

        const data = await res.json();
        btn.classList.toggle('liked', data.liked);
        count.textContent = data.count;
        btn.disabled = false;
    });
});
