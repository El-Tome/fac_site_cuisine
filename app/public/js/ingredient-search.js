let cachedCategories = null;

async function fetchCategories() {
    if (cachedCategories) return cachedCategories;
    const res      = await fetch('/api/ingredient/categories');
    cachedCategories = await res.json();
    return cachedCategories;
}

class CategoryPicker {
    constructor(widget) {
        this.selectedChips = widget.querySelector('.selected-categories');
        this.searchInput   = widget.querySelector('.category-search-input');
        this.resultsBox    = widget.querySelector('.category-results');
        this.selected      = new Map(); // id -> name

        if (this.searchInput) this._bind();
    }

    async open() {
        const categories = await fetchCategories();
        this._allCategories = categories;
        this._renderResults(categories);
        this.searchInput.value = '';
        this.searchInput.focus();
    }

    _bind() {
        this.searchInput.addEventListener('input', () => {
            const q = this.searchInput.value.toLowerCase();
            const filtered = (this._allCategories || []).filter(c =>
                c.name.toLowerCase().includes(q)
            );
            this._renderResults(filtered);
        });

        this.searchInput.addEventListener('blur', () => {
            setTimeout(() => { this.resultsBox.style.display = 'none'; }, 200);
        });

        this.searchInput.addEventListener('focus', () => {
            if (this._allCategories) {
                this._renderResults(this._allCategories.filter(c =>
                    c.name.toLowerCase().includes(this.searchInput.value.toLowerCase())
                ));
            }
        });
    }

    _renderResults(list) {
        if (!this.resultsBox) return;

        const q          = this.searchInput ? this.searchInput.value.trim() : '';
        const unselected = list.filter(c => !this.selected.has(c.id));
        const rows       = [];

        unselected.forEach(c => {
            rows.push(`<div class="category-result-item" data-id="${c.id}" data-name="${c.name}">${c.name}</div>`);
        });

        if (q.length >= 2) {
            rows.push(`<div class="category-create-item" data-name="${q}">+ Créer « ${q} »</div>`);
        }

        if (rows.length === 0) {
            this.resultsBox.style.display = 'none';
            return;
        }

        this.resultsBox.innerHTML = rows.join('');

        this.resultsBox.querySelectorAll('.category-result-item').forEach(el => {
            el.addEventListener('mousedown', () => {
                this._addChip(parseInt(el.dataset.id), el.dataset.name);
                this.searchInput.value = '';
                this._renderResults(this._allCategories);
                this.searchInput.focus();
            });
        });

        this.resultsBox.querySelectorAll('.category-create-item').forEach(el => {
            el.addEventListener('mousedown', () => this._createCategory(el.dataset.name));
        });

        this.resultsBox.style.display = 'block';
    }

    async _createCategory(name) {
        const res = await fetch('/api/ingredient/category', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ name }),
        });

        if (!res.ok) return;

        const cat = await res.json();

        if (cachedCategories) {
            cachedCategories.push({ id: cat.id, name: cat.name });
            cachedCategories.sort((a, b) => a.name.localeCompare(b.name));
        }
        if (this._allCategories) {
            this._allCategories = cachedCategories;
        }

        this._addChip(cat.id, cat.name);
        if (this.searchInput) this.searchInput.value = '';
        this._renderResults(this._allCategories || []);
        if (this.searchInput) this.searchInput.focus();
    }

    _addChip(id, name) {
        this.selected.set(id, name);

        const chip = document.createElement('span');
        chip.className       = 'category-chip';
        chip.dataset.id      = id;
        chip.innerHTML       = `${name}<button type="button" class="chip-remove">×</button>`;
        chip.querySelector('.chip-remove').addEventListener('click', () => {
            this.selected.delete(id);
            chip.remove();
            if (this._allCategories) this._renderResults(this._allCategories);
        });

        this.selectedChips.appendChild(chip);
    }

    getSelectedIds() {
        return [...this.selected.keys()];
    }

    reset() {
        this.selected.clear();
        if (this.selectedChips) this.selectedChips.innerHTML  = '';
        if (this.resultsBox)    this.resultsBox.innerHTML     = '';
        if (this.resultsBox)    this.resultsBox.style.display = 'none';
        if (this.searchInput)   this.searchInput.value        = '';
        this._allCategories = null;
    }
}

class IngredientSearch {
    constructor(widget) {
        this.widget       = widget;
        this.input        = widget.querySelector('.ingredient-search-input');
        this.hiddenField  = widget.querySelector('.ingredient-id-field');
        this.resultsBox   = widget.querySelector('.ingredient-results');
        this.selectedBox  = widget.querySelector('.ingredient-selected');
        this.selectedName = widget.querySelector('.ingredient-selected-name');
        this.noResultBox  = widget.querySelector('.ingredient-no-result');
        this.searchTerm   = widget.querySelector('.ingredient-search-term');
        this.quickForm    = widget.querySelector('.ingredient-quick-form');
        this.quickInput   = widget.querySelector('.ingredient-quick-input');

        this.categoryPicker = new CategoryPicker(widget);
        this.debounceTimer  = null;

        const currentId   = widget.dataset.ingredientId;
        const currentName = widget.dataset.ingredientName;
        if (currentId && currentName) {
            this._select(currentId, currentName);
        }

        this._bind();
    }

    _bind() {
        this.input.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            const q = this.input.value.trim();
            if (q.length < 2) {
                this._closeResults();
                if (this.noResultBox) this.noResultBox.style.display = 'none';
                return;
            }
            this.debounceTimer = setTimeout(() => this._search(q), 300);
        });

        this.input.addEventListener('blur', () => {
            setTimeout(() => this._closeResults(), 200);
        });

        const showQuickFormBtn = this.widget.querySelector('.btn-show-quick-form');
        if (showQuickFormBtn) {
            showQuickFormBtn.addEventListener('click', () => {
                this.quickInput.value        = this.input.value.trim();
                this.quickForm.style.display = 'block';
                this.categoryPicker.open();
            });

            this.widget.querySelector('.btn-quick-submit').addEventListener('click', () => {
                this._create(this.quickInput.value.trim());
            });

            this.widget.querySelector('.btn-quick-cancel').addEventListener('click', () => {
                this.quickForm.style.display = 'none';
                this.categoryPicker.reset();
            });
        }

        this.widget.querySelector('.btn-clear').addEventListener('click', () => {
            this._clear();
        });
    }

    async _search(q) {
        const res  = await fetch(`/api/ingredient/search?q=${encodeURIComponent(q)}`);
        const list = await res.json();
        this._renderResults(list, q);
    }

    _renderResults(list, q) {
        this.resultsBox.innerHTML = '';

        if (list.length === 0) {
            this.resultsBox.style.display = 'none';
            if (this.searchTerm)   this.searchTerm.textContent    = q;
            if (this.noResultBox)  this.noResultBox.style.display = 'block';
            return;
        }

        if (this.noResultBox) this.noResultBox.style.display = 'none';
        list.forEach(item => {
            const div       = document.createElement('div');
            div.className   = 'ingredient-result-item';
            div.textContent = item.nom;
            div.addEventListener('mousedown', () => this._select(item.id, item.nom));
            this.resultsBox.appendChild(div);
        });
        this.resultsBox.style.display = 'block';
    }

    _select(id, nom) {
        this.hiddenField.value         = id;
        this.selectedName.textContent  = nom;
        this.selectedBox.style.display = 'flex';
        this.input.style.display       = 'none';
        if (this.noResultBox) this.noResultBox.style.display = 'none';
        if (this.quickForm)   this.quickForm.style.display   = 'none';
        this.categoryPicker.reset();
        this._closeResults();
    }

    _clear() {
        this.hiddenField.value         = '';
        this.selectedBox.style.display = 'none';
        this.input.style.display       = 'block';
        this.input.value               = '';
        this.input.focus();
    }

    _closeResults() {
        this.resultsBox.style.display = 'none';
        this.resultsBox.innerHTML     = '';
    }

    async _create(nom) {
        if (!nom) return;

        const res  = await fetch('/api/ingredient', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ nom, categoryIds: this.categoryPicker.getSelectedIds() }),
        });
        const item = await res.json();
        this._select(item.id, item.nom);
    }
}

function initIngredientWidgets(root) {
    root.querySelectorAll('.ingredient-search-widget').forEach(w => new IngredientSearch(w));
}

document.addEventListener('DOMContentLoaded', () => initIngredientWidgets(document));
