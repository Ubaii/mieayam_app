const currency = (amount) => `Rp ${new Intl.NumberFormat('id-ID').format(amount)}`;

const showToast = (message) => {
    let toast = document.querySelector('[data-toast]');

    if (!toast) {
        toast = document.createElement('div');
        toast.dataset.toast = '';
        toast.className = 'app-toast';
        toast.setAttribute('role', 'status');
        document.body.append(toast);
    }

    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(toast.hideTimer);
    toast.hideTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
};

const initLoginEffects = () => {
    const page = document.querySelector('.login-page');
    const visual = page?.querySelector('.login-visual');
    const card = page?.querySelector('.login-card');
    if (!page || !visual || !card || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    page.classList.add('has-scroll-effects');
    const reveal = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            card.classList.add('is-visible');
            reveal.disconnect();
        }
    }, { threshold: 0.12 });
    reveal.observe(card);

    let frame = 0;
    const updateParallax = () => {
        if (frame) return;
        frame = window.requestAnimationFrame(() => {
            const offset = Math.min(window.scrollY, 240) * 0.12;
            visual.style.setProperty('--login-parallax', `${offset.toFixed(1)}px`);
            frame = 0;
        });
    };

    window.addEventListener('scroll', updateParallax, { passive: true });
    updateParallax();
};

const initSidebar = () => {
    const sidebar = document.querySelector('#app-sidebar');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');

    if (!sidebar || !backdrop) return;

    const close = () => {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-visible');
    };

    document.querySelector('[data-sidebar-open]')?.addEventListener('click', () => {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-visible');
    });
    backdrop.addEventListener('click', close);
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') close();
    });
};

const initModals = () => {
    const closeModal = (modal) => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    document.querySelectorAll('[data-modal-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.getElementById(button.dataset.modalOpen);
            if (!modal) return;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            modal.querySelector('input, button, select, textarea')?.focus();
        });
    });
    document.querySelectorAll('[data-modal]').forEach((modal) => {
        modal.querySelectorAll('[data-modal-close]').forEach((button) => button.addEventListener('click', () => closeModal(modal)));
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeModal(modal);
        });
        modal.addEventListener('modal:close', () => closeModal(modal));
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') document.querySelectorAll('[data-modal].is-open').forEach(closeModal);
    });

};

const initPos = () => {
    const grid = document.querySelector('[data-product-grid]');
    const orderItems = document.querySelector('[data-order-items]');
    if (!grid || !orderItems) return;

    const cart = new Map();
    const totalNode = document.querySelector('[data-order-total]');
    const emptyNode = document.querySelector('[data-order-empty]');
    const payButton = document.querySelector('[data-payment-open]');
    const modal = document.querySelector('#payment-modal');
    const search = document.querySelector('[data-menu-search]');
    const categorySelect = document.querySelector('[data-category-select]');
    const emptyProducts = document.querySelector('[data-products-empty]');
    const amountField = document.querySelector('[data-payment-amount]');
    const paymentTotal = document.querySelector('[data-payment-total]');
    const paymentChange = document.querySelector('[data-payment-change]');
    const changeDisplay = document.querySelector('[data-change-display]');
    const paymentInputs = document.querySelector('[data-payment-items]');
    const paymentTable = document.querySelector('[data-payment-table]');
    const tablePicker = document.querySelector('[data-cafe-table-picker]');
    const tableDisplay = document.querySelector('[data-order-table-display]');
    const tunaiFields = document.querySelector('[data-tunai-fields]');
    let activeCategory = '';

    const total = () => [...cart.values()].reduce((sum, item) => sum + item.price * item.quantity, 0);

    const updateChange = () => {
        const sum = total();
        const method = document.querySelector('[name="payment_method"]:checked')?.value;
        const paid = Number(amountField?.value || 0);
        if (paymentTotal) paymentTotal.textContent = currency(sum);

        if (method === 'Tunai') {
            const change = paid - sum;
            if (paymentChange) {
                if (change === 0) {
                    paymentChange.textContent = 'Uang pas';
                    if (changeDisplay) changeDisplay.classList.add('tone-green');
                    if (changeDisplay) changeDisplay.classList.remove('tone-red');
                } else if (change > 0) {
                    paymentChange.textContent = currency(change);
                    if (changeDisplay) changeDisplay.classList.remove('tone-green', 'tone-red');
                } else {
                    paymentChange.textContent = currency(Math.abs(change)) + ' kurang';
                    if (changeDisplay) changeDisplay.classList.add('tone-red');
                    if (changeDisplay) changeDisplay.classList.remove('tone-green');
                }
            }
        } else {
            if (paymentChange) paymentChange.textContent = '—';
            if (changeDisplay) changeDisplay.classList.remove('tone-green', 'tone-red');
        }
    };

    const togglePaymentFields = () => {
        const method = document.querySelector('[name="payment_method"]:checked')?.value;
        if (tunaiFields) tunaiFields.hidden = method !== 'Tunai';
        if (changeDisplay) changeDisplay.hidden = method !== 'Tunai';
        updateChange();
    };

    const renderCart = () => {
        orderItems.querySelectorAll('.order-item').forEach((item) => item.remove());
        if (emptyNode) emptyNode.hidden = cart.size > 0;
        if (payButton) payButton.disabled = cart.size === 0;

        cart.forEach((item, id) => {
            const row = document.createElement('article');
            row.className = 'order-item';
            row.dataset.orderId = String(id);

            const header = document.createElement('div');
            header.className = 'order-item-top';
            const details = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent = item.name;
            const price = document.createElement('div');
            price.className = 'order-item-sub';
            price.textContent = `${currency(item.price)} × ${item.quantity}`;
            details.append(title, price);
            const lineTotal = document.createElement('b');
            lineTotal.textContent = currency(item.price * item.quantity);
            header.append(details, lineTotal);

            const controls = document.createElement('div');
            controls.className = 'order-item-controls';
            const quantity = document.createElement('div');
            quantity.className = 'qty-control';
            quantity.innerHTML = '<button type="button" aria-label="Kurangi jumlah" data-quantity-change="-1">−</button>';
            const quantityValue = document.createElement('span');
            quantityValue.textContent = String(item.quantity);
            const increment = document.createElement('button');
            increment.type = 'button';
            increment.setAttribute('aria-label', 'Tambah jumlah');
            increment.dataset.quantityChange = '1';
            increment.textContent = '+';
            quantity.append(quantityValue, increment);

            const note = document.createElement('label');
            note.className = 'item-note';
            const noteInput = document.createElement('input');
            noteInput.className = 'input-control';
            noteInput.placeholder = 'Catatan...';
            noteInput.setAttribute('aria-label', `Catatan ${item.name}`);
            noteInput.value = item.note;
            noteInput.dataset.itemNote = '';
            note.append(noteInput);

            const remove = document.createElement('button');
            remove.className = 'remove-item';
            remove.type = 'button';
            remove.setAttribute('aria-label', `Hapus ${item.name}`);
            remove.dataset.removeItem = '';
            remove.innerHTML = '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>';

            controls.append(quantity, note, remove);
            row.append(header, controls);
            orderItems.append(row);
        });

        if (totalNode) totalNode.textContent = currency(total());
        updateChange();
    };

    const filterProducts = () => {
        const query = search?.value.trim().toLocaleLowerCase('id') || '';
        let visible = 0;
        grid.querySelectorAll('[data-product-name]').forEach((product) => {
            const matchesText = product.dataset.productName.toLocaleLowerCase('id').includes(query);
            const matchesCategory = !activeCategory || product.dataset.productCategory === activeCategory;
            product.hidden = !(matchesText && matchesCategory);
            if (!product.hidden) visible += 1;
        });
        if (emptyProducts) emptyProducts.hidden = visible > 0;
    };

    grid.addEventListener('click', (event) => {
        const product = event.target.closest('[data-product-name]');
        if (!product) return;
        const id = Number(product.dataset.productId);
        const current = cart.get(id) || { menuId: id, name: product.dataset.productName, price: Number(product.dataset.productPrice), quantity: 0, note: '' };
        current.quantity += 1;
        cart.set(id, current);
        renderCart();
    });

    orderItems.addEventListener('click', (event) => {
        const row = event.target.closest('[data-order-id]');
        if (!row) return;
        const item = cart.get(Number(row.dataset.orderId));
        if (!item) return;
        if (event.target.closest('[data-remove-item]')) {
            cart.delete(Number(row.dataset.orderId));
        } else {
            const change = event.target.closest('[data-quantity-change]');
            if (!change) return;
            item.quantity += Number(change.dataset.quantityChange);
            if (item.quantity <= 0) cart.delete(Number(row.dataset.orderId));
        }
        renderCart();
    });
    orderItems.addEventListener('input', (event) => {
        const row = event.target.closest('[data-order-id]');
        if (row && event.target.matches('[data-item-note]')) {
            const item = cart.get(Number(row.dataset.orderId));
            if (item) item.note = event.target.value;
        }
    });

    document.querySelector('[data-clear-order]')?.addEventListener('click', () => {
        cart.clear();
        renderCart();
    });
    tablePicker?.addEventListener('change', () => {
        if (tableDisplay) tableDisplay.value = tablePicker.value;
    });
    tableDisplay?.addEventListener('change', () => {
        if (tablePicker) tablePicker.value = tableDisplay.value;
    });
    search?.addEventListener('input', filterProducts);
    categorySelect?.addEventListener('change', () => {
        activeCategory = categorySelect.value;
        document.querySelectorAll('[data-category-chip]').forEach((chip) => chip.classList.toggle('active', chip.dataset.categoryChip === activeCategory));
        filterProducts();
    });
    document.querySelectorAll('[data-category-chip]').forEach((chip) => {
        chip.addEventListener('click', () => {
            activeCategory = chip.dataset.categoryChip;
            if (categorySelect) categorySelect.value = activeCategory;
            document.querySelectorAll('[data-category-chip]').forEach((item) => item.classList.toggle('active', item === chip));
            filterProducts();
        });
    });
    payButton?.addEventListener('click', () => {
        if (cart.size === 0 || !modal) return;
        if (amountField) amountField.value = String(total());
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        togglePaymentFields();
    });
    amountField?.addEventListener('input', updateChange);
    document.querySelectorAll('[name="payment_method"]').forEach((radio) => radio.addEventListener('change', togglePaymentFields));
    document.querySelector('[data-payment-form]')?.addEventListener('submit', (event) => {
        const method = document.querySelector('[name="payment_method"]:checked')?.value;
        if (method === 'Tunai' && Number(amountField?.value || 0) < total()) {
            event.preventDefault();
            showToast('Jumlah bayar belum mencukupi.');
            return;
        }
        if (!cart.size || !paymentInputs) {
            event.preventDefault();
            showToast('Tambahkan menu ke pesanan sebelum membayar.');
            return;
        }

        paymentInputs.replaceChildren();
        if (paymentTable && tableDisplay) {
            const tableValue = tableDisplay.value;
            if (tableValue === 'dine-in') {
                paymentTable.value = '';
            } else {
                paymentTable.value = tableValue;
            }
        }
        cart.forEach((item) => {
            const fields = [
                ['menu_id', item.menuId],
                ['quantity', item.quantity],
                ['note', item.note],
            ];
            fields.forEach(([field, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `items[${item.menuId}][${field}]`;
                input.value = String(value);
                paymentInputs.append(input);
            });
        });
    });

    renderCart();
};

const initFilters = () => {
    const applyTableFilters = (tbody) => {
        if (!tbody) return;
        const selector = `#${tbody.id}`;
        const query = document.querySelector(`[data-table-search="${selector}"]`)?.value.trim().toLocaleLowerCase('id') || '';
        const filters = [...document.querySelectorAll(`[data-filter-target="${selector}"]`)];
        const method = filters.find((filter) => filter.dataset.rowFilter === 'method')?.value || '';
        const status = filters.find((filter) => filter.dataset.rowFilter === 'status')?.value || '';
        const fromDate = filters.find((filter) => filter.dataset.dateFilter === 'from')?.value || '';
        const toDate = filters.find((filter) => filter.dataset.dateFilter === 'to')?.value || '';

        tbody.querySelectorAll('tr').forEach((row) => {
            const matchesSearch = row.textContent.toLocaleLowerCase('id').includes(query);
            const matchesMethod = !method || row.dataset.method === method;
            const matchesStatus = !status || row.dataset.status === status;
            const matchesFrom = !fromDate || !row.dataset.date || row.dataset.date >= fromDate;
            const matchesTo = !toDate || !row.dataset.date || row.dataset.date <= toDate;
            row.hidden = !(matchesSearch && matchesMethod && matchesStatus && matchesFrom && matchesTo);
        });
    };

    document.querySelectorAll('[data-table-search]').forEach((input) => {
        input.addEventListener('input', () => {
            const tbody = document.querySelector(input.dataset.tableSearch);
            applyTableFilters(tbody);
        });
    });
    document.querySelectorAll('[data-row-filter], [data-date-filter]').forEach((input) => {
        input.addEventListener('change', () => applyTableFilters(document.querySelector(input.dataset.filterTarget)));
    });

    document.querySelectorAll('[data-card-search]').forEach((input) => {
        input.addEventListener('input', () => {
            const selector = input.dataset.cardSearch;
            const statusFilter = document.querySelector('[data-status-filter]')?.value || '';
            document.querySelectorAll(selector).forEach((card) => {
                const matchesSearch = card.textContent.toLocaleLowerCase('id').includes(input.value.trim().toLocaleLowerCase('id'));
                const matchesStatus = !statusFilter || card.dataset.status === statusFilter;
                card.hidden = !(matchesSearch && matchesStatus);
            });
        });
    });

    const applyMenuFilters = () => {
        const body = document.querySelector('#menu-table');
        if (!body) return;
        const query = document.querySelector('[data-table-search="#menu-table"]')?.value.trim().toLocaleLowerCase('id') || '';
        const category = document.querySelector('#menu-category')?.value || '';
        const status = document.querySelector('#menu-status')?.value || '';
        body.querySelectorAll('tr').forEach((row) => {
            const matchesSearch = row.textContent.toLocaleLowerCase('id').includes(query);
            row.hidden = !(matchesSearch && (!category || row.dataset.category === category) && (!status || row.dataset.status === status));
        });
    };
    document.querySelector('[data-table-search="#menu-table"]')?.addEventListener('input', applyMenuFilters);
    document.querySelector('#menu-category')?.addEventListener('change', applyMenuFilters);
    document.querySelector('#menu-status')?.addEventListener('change', applyMenuFilters);

    document.querySelector('[data-status-filter]')?.addEventListener('change', (event) => {
        const query = document.querySelector('[data-card-search=".table-management-card"]')?.value.trim().toLocaleLowerCase('id') || '';
        document.querySelectorAll('.table-management-card').forEach((card) => {
            const matchesSearch = card.textContent.toLocaleLowerCase('id').includes(query);
            card.hidden = !(matchesSearch && (!event.target.value || card.dataset.status === event.target.value));
        });
    });
    document.querySelector('[data-room-filter]')?.addEventListener('change', (event) => {
        document.querySelectorAll('[data-room-status]').forEach((room) => {
            room.hidden = Boolean(event.target.value) && room.dataset.roomStatus !== event.target.value;
        });
    });

    document.querySelectorAll('[data-submit-change]').forEach((select) => {
        select.addEventListener('change', () => select.form?.requestSubmit());
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });

    document.querySelector('[data-notifications]')?.addEventListener('click', () => showToast('Belum ada notifikasi baru.'));
};

const initPasswordToggle = () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = button.closest('.password-field')?.querySelector('[data-password-input]');
            if (!input) return;
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
            button.innerHTML = visible
                ? '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.3A10.7 10.7 0 0 1 12 5c6.1 0 9.5 7 9.5 7a15 15 0 0 1-3 3.7M6.2 6.2C3.9 7.8 2.5 12 2.5 12s3.4 7 9.5 7a10.7 10.7 0 0 0 2.1-.3"/></svg>'
                : '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>';
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initLoginEffects();
    document.querySelectorAll('[data-history-back]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (window.history.length < 2 || !document.referrer) return;

            if (new URL(document.referrer, window.location.href).origin === window.location.origin) {
                event.preventDefault();
                window.history.back();
            }
        });
    });
    initSidebar();
    initModals();
    initPos();
    initFilters();
    initPasswordToggle();
});
