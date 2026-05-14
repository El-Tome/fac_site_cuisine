function getCurrentRecipeIds() {
    return Array.from(document.querySelectorAll('#meal-plan-list [data-recipe-id]'))
        .map(el => parseInt(el.dataset.recipeId));
}

function getItemIndex(item) {
    const day = item.querySelector('.meal-plan-item__day');
    return day ? parseInt(day.textContent.replace('Plat ', '')) : 1;
}

function syncSaveForm() {
    const form = document.getElementById('save-form');
    if (!form) return;

    form.querySelectorAll('input[name="recipe_ids[]"]').forEach(el => el.remove());

    getCurrentRecipeIds().forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'recipe_ids[]';
        input.value = id;
        form.appendChild(input);
    });
}

async function rerollRecipe(button) {
    const item      = button.closest('.meal-plan-item');
    const currentId = parseInt(button.dataset.recipeId);
    const index     = getItemIndex(item);
    const exclude   = getCurrentRecipeIds().join(',');

    button.disabled    = true;
    button.textContent = '…';

    const url = `/meal-plan/recipe/random?exclude=${exclude}&index=${index}`;
    const res = await fetch(url);

    if (res.status === 204) {
        button.disabled    = false;
        button.textContent = '↻';
        return;
    }

    const html = await res.text();
    const tmp  = document.createElement('ol');
    tmp.innerHTML = html;
    const newItem = tmp.firstElementChild;

    if (newItem) {
        item.replaceWith(newItem);
        attachRerollHandler(newItem.querySelector('.btn-reroll'));
        syncSaveForm();
    }
}

function attachRerollHandler(button) {
    if (!button) return;
    button.addEventListener('click', () => rerollRecipe(button));
}

function initMealPlan() {
    document.querySelectorAll('.btn-reroll').forEach(attachRerollHandler);
    syncSaveForm();
}

document.addEventListener('DOMContentLoaded', initMealPlan);
