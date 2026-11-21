/* PustakaKu: vanilla JavaScript — filter interaktif, modal CRUD, validasi klien. */
(() => {
    'use strict';
    const $ = (selector, parent = document) => parent.querySelector(selector);
    const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];
    const getDialog = id => document.getElementById(id);

    function showDialog(dialog) {
        if (!dialog) return;
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else dialog.setAttribute('open', '');
    }
    function closeDialog(dialog) {
        if (!dialog) return;
        if (typeof dialog.close === 'function') dialog.close();
        else dialog.removeAttribute('open');
    }
    function clearValidation(form) {
        if (!form) return;
        $$('input, textarea, select', form).forEach(field => field.setCustomValidity(''));
    }

    document.addEventListener('click', event => {
        const open = event.target.closest('[data-open]');
        if (open) {
            const dialog = getDialog(open.dataset.open);
            const form = $('form', dialog);
            if (form) { form.reset(); clearValidation(form); }
            if (dialog?.id === 'bookDialog') {
                $('#bookDialogTitle').textContent = 'Tambah E-Book';
                $('[name="id"]', form).value = '';
            }
            if (dialog?.id === 'memberDialog') {
                $('#memberDialogTitle').textContent = 'Tambah Anggota';
                $('[name="id"]', form).value = '';
            }
            showDialog(dialog);
        }
        const close = event.target.closest('[data-close-dialog]');
        if (close) closeDialog(close.closest('dialog'));
        const dismiss = event.target.closest('[data-dismiss-alert]');
        if (dismiss) dismiss.closest('.alert')?.remove();

        const editBook = event.target.closest('[data-edit-book]');
        if (editBook) {
            const dialog = getDialog('bookDialog');
            const form = $('#bookForm');
            clearValidation(form);
            $('#bookDialogTitle').textContent = 'Edit E-Book';
            for (const key of ['id', 'title', 'author', 'category', 'year', 'description', 'accent', 'kind']) {
                const input = $(`[name="${key}"]`, form);
                if (input) input.value = editBook.dataset[key] ?? '';
            }
            showDialog(dialog);
        }
        const editMember = event.target.closest('[data-edit-member]');
        if (editMember) {
            const dialog = getDialog('memberDialog');
            const form = $('#memberForm');
            clearValidation(form);
            $('#memberDialogTitle').textContent = 'Edit Anggota';
            for (const key of ['id', 'name', 'email']) {
                const input = $(`[name="${key}"]`, form);
                if (input) input.value = editMember.dataset[key] ?? '';
            }
            showDialog(dialog);
        }
        const borrow = event.target.closest('[data-borrow-book]');
        if (borrow && !borrow.disabled) {
            $('#borrowBookId').value = borrow.dataset.id;
            $('#borrowBookName').textContent = `"${borrow.dataset.title}"`;
            $('#borrowDuration').textContent = `${borrow.dataset.days} hari`;
            $('#borrowMember').value = '';
            showDialog(getDialog('borrowDialog'));
        }
    });

    $$('dialog').forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target === dialog) closeDialog(dialog);
        });
    });

    // Konfirmasi aksi yang menghapus data / mengembalikan buku.
    $$('form[data-confirm]').forEach(form => form.addEventListener('submit', event => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    }));

    $$('.modal-form').forEach(form => {
        const fields = $$('input:not([type]), input[type="email"], textarea', form);
        fields.forEach(field => {
            field.addEventListener('input', () => field.setCustomValidity(''));
        });
        form.addEventListener('submit', event => {
            fields.forEach(field => {
                const value = field.value.trim();
                field.value = value;
                if (field.minLength > 0 && [...value].length < field.minLength) {
                    field.setCustomValidity(`Masukkan minimal ${field.minLength} karakter.`);
                }
            });
            if (!form.reportValidity()) event.preventDefault();
        });
    });

    const bookSearch = $('#catalogSearch');
    const catalogCards = $$('[data-book-card]');
    const categoryButtons = $$('[data-category]');
    let category = 'all';
    function filterBooks() {
        const query = (bookSearch?.value || '').trim().toLocaleLowerCase('id-ID');
        let found = 0;
        catalogCards.forEach(card => {
            const matches = (category === 'all' || card.dataset.categoryValue === category) &&
                (card.dataset.searchValue || '').toLocaleLowerCase('id-ID').includes(query);
            card.hidden = !matches;
            if (matches) found++;
        });
        const count = $('#catalogResultCount');
        if (count) count.textContent = `${found} e-book ditampilkan`;
        const empty = $('#catalogEmpty');
        if (empty) empty.hidden = found > 0 || catalogCards.length === 0;
    }
    if (bookSearch) bookSearch.addEventListener('input', filterBooks);
    categoryButtons.forEach(button => button.addEventListener('click', () => {
        category = button.dataset.category;
        categoryButtons.forEach(chip => {
            const active = chip === button;
            chip.classList.toggle('active', active);
            chip.setAttribute('aria-pressed', String(active));
        });
        filterBooks();
    }));

    function tableSearch(searchId, selector, resultId, emptyId, statusId) {
        const search = document.getElementById(searchId);
        if (!search) return;
        const rows = $$(selector);
        const status = statusId ? document.getElementById(statusId) : null;
        function update() {
            const query = search.value.trim().toLocaleLowerCase('id-ID');
            let matched = 0;
            rows.forEach(row => {
                const textMatch = (row.dataset.searchValue || '').toLocaleLowerCase('id-ID').includes(query);
                const statusMatch = !status || status.value === 'all' || row.dataset.status === status.value;
                const visible = textMatch && statusMatch;
                row.hidden = !visible;
                if (visible) matched++;
            });
            const result = document.getElementById(resultId);
            if (result) result.textContent = `${matched} ${status ? 'transaksi' : 'anggota'} ditampilkan`;
            const empty = document.getElementById(emptyId);
            if (empty) empty.hidden = matched > 0 || rows.length === 0;
        }
        search.addEventListener('input', update);
        if (status) status.addEventListener('change', update);
    }
    tableSearch('memberSearch', '[data-member-row]', 'memberResults', 'memberEmpty');
    tableSearch('loanSearch', '[data-loan-row]', 'loanResults', 'loanEmpty', 'loanStatus');
})();
