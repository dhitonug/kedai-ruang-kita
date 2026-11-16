const dialog = document.querySelector('#cartDialog');

if (dialog) {
    const cart = new Map();
    const cartDock = document.querySelector('#cartDock');
    const cartLines = document.querySelector('#cartLines');
    const itemInputs = document.querySelector('#itemInputs');
    const cartCount = document.querySelector('#cartCount');
    const cartTotal = document.querySelector('#cartTotal');
    const modalTotal = document.querySelector('#modalTotal');
    const checkoutButton = document.querySelector('#checkoutButton');

    const rupiah = (amount) => `Rp ${new Intl.NumberFormat('id-ID').format(amount)}`;

    const makeQuantityButton = (label, symbol, onClick) => {
        const button = document.createElement('button');
        button.className = 'quantity-button';
        button.type = 'button';
        button.setAttribute('aria-label', label);
        button.textContent = symbol;
        button.addEventListener('click', onClick);
        return button;
    };

    const renderCart = () => {
        const items = [...cart.values()];
        const quantity = items.reduce((sum, item) => sum + item.quantity, 0);
        const total = items.reduce((sum, item) => sum + item.price * item.quantity, 0);

        cartDock.hidden = quantity === 0;
        cartCount.textContent = `${quantity} item${quantity === 1 ? '' : 's'}`;
        cartTotal.textContent = rupiah(total);
        modalTotal.textContent = rupiah(total);
        checkoutButton.disabled = quantity === 0;
        cartLines.replaceChildren();
        itemInputs.replaceChildren();

        if (items.length === 0) {
            const emptyMessage = document.createElement('p');
            emptyMessage.className = 'cart-empty';
            emptyMessage.textContent = 'Keranjang masih kosong. Pilih menu yang kamu suka.';
            cartLines.append(emptyMessage);
            return;
        }

        items.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'cart-line';

            const details = document.createElement('div');
            details.className = 'cart-line-details';
            const name = document.createElement('div');
            name.className = 'cart-line-name';
            name.textContent = item.name;
            const price = document.createElement('div');
            price.className = 'cart-line-price';
            price.textContent = `${rupiah(item.price)} per porsi`;
            details.append(name, price);

            const subtotal = document.createElement('div');
            subtotal.className = 'cart-line-subtotal';
            const subtotalLabel = document.createElement('span');
            subtotalLabel.className = 'cart-line-subtotal-label';
            subtotalLabel.textContent = 'Subtotal';
            const subtotalValue = document.createElement('strong');
            subtotalValue.textContent = rupiah(item.price * item.quantity);
            subtotal.append(subtotalLabel, subtotalValue);

            const quantityControl = document.createElement('div');
            quantityControl.className = 'quantity-control';
            quantityControl.append(
                makeQuantityButton(`Kurangi ${item.name}`, '−', () => updateQuantity(item.id, -1)),
            );
            const quantityLabel = document.createElement('span');
            quantityLabel.className = 'quantity-value';
            quantityLabel.textContent = String(item.quantity);
            quantityControl.append(quantityLabel);
            quantityControl.append(
                makeQuantityButton(`Tambah ${item.name}`, '+', () => updateQuantity(item.id, 1)),
            );

            row.append(details, subtotal, quantityControl);
            cartLines.append(row);

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = `items[${index}][id]`;
            idInput.value = String(item.id);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = String(item.quantity);
            itemInputs.append(idInput, quantityInput);
        });
    };

    const updateQuantity = (id, amount) => {
        const item = cart.get(id);
        if (!item) return;

        item.quantity += amount;
        if (item.quantity < 1) cart.delete(id);
        renderCart();
    };

    document.querySelectorAll('.add-menu-button').forEach((button) => {
        button.addEventListener('click', () => {
            const id = Number(button.dataset.id);
            const item = cart.get(id) ?? {
                id,
                name: button.dataset.name,
                price: Number(button.dataset.price),
                quantity: 0,
            };

            item.quantity += 1;
            cart.set(id, item);
            renderCart();
        });
    });

    document.querySelectorAll('.category-button').forEach((button) => {
        button.addEventListener('click', () => {
            const category = button.dataset.filter;

            document.querySelectorAll('.category-button').forEach((filterButton) => {
                filterButton.setAttribute('aria-pressed', String(filterButton === button));
            });

            document.querySelectorAll('.menu-card').forEach((card) => {
                card.hidden = category !== 'Semua' && card.dataset.category !== category;
            });
        });
    });

    cartDock.addEventListener('click', () => dialog.showModal());
    document.querySelector('#closeCart').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });

    document.querySelectorAll('.menu-image img').forEach((image) => {
        image.addEventListener('error', () => {
            image.hidden = true;
        }, { once: true });
    });

    renderCart();
}
