@extends('layouts.app')

@section('title', 'نقطة البيع')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<style>
.pos-wrapper {
    display: flex;
    gap: 20px;
    height: calc(100vh - 140px);
    min-height: 500px;
}
.pos-products {
    flex: 7;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.pos-cart {
    flex: 3;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.pos-cart-panel {
    background: var(--card-bg);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}
.category-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 4px 0;
    flex-shrink: 0;
}
.category-tabs .btn {
    white-space: nowrap;
    flex-shrink: 0;
}
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
    overflow-y: auto;
    flex: 1;
    padding: 4px 0;
}
.product-card {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 16px 12px;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    user-select: none;
}
.product-card:hover {
    border-color: var(--gold);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.product-card:active {
    transform: scale(0.96);
}
.product-card .product-name {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 6px;
    color: var(--text-primary);
}
.product-card .product-price {
    font-size: 15px;
    font-weight: 800;
    color: var(--gold-dark);
    direction: ltr;
}
.cart-header {
    padding: 16px 18px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.cart-header h5 {
    margin: 0;
    font-weight: 700;
}
.cart-items {
    flex: 1;
    overflow-y: auto;
    padding: 8px 12px;
}
.cart-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 8px;
    border-bottom: 1px solid var(--border-color);
    animation: fadeInUp 0.2s ease;
}
.cart-item-info {
    flex: 1;
    min-width: 0;
}
.cart-item-name {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 2px;
}
.cart-item-price {
    font-size: 12px;
    color: var(--text-muted);
    direction: ltr;
}
.cart-qty {
    display: flex;
    align-items: center;
    gap: 4px;
}
.cart-qty button {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px solid var(--border-color);
    background: var(--body-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: var(--transition);
    color: var(--text-primary);
}
.cart-qty button:hover {
    border-color: var(--gold);
    color: var(--gold);
}
.cart-qty span {
    width: 28px;
    text-align: center;
    font-weight: 700;
    font-size: 14px;
}
.cart-item-total {
    font-weight: 700;
    font-size: 13px;
    direction: ltr;
    min-width: 60px;
    text-align: left;
}
.cart-item-delete {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 14px;
    padding: 4px;
    transition: var(--transition);
    opacity: 0.5;
}
.cart-item-delete:hover {
    opacity: 1;
}
.cart-summary {
    padding: 14px 18px;
    border-top: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
}
.summary-row.total {
    font-size: 18px;
    font-weight: 800;
    color: var(--gold-dark);
    padding-top: 8px;
    border-top: 2px solid var(--border-color);
}
.cart-footer {
    padding: 14px 18px;
    border-top: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex-shrink: 0;
}
.cart-footer .form-control, .cart-footer .form-select {
    font-size: 13px;
}
.cart-footer label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 2px;
}
.payment-methods {
    display: flex;
    gap: 6px;
}
.payment-methods .btn {
    flex: 1;
    font-size: 12px;
    padding: 6px 8px;
}
.order-type {
    display: flex;
    gap: 6px;
}
.order-type .btn {
    flex: 1;
    font-size: 12px;
    padding: 6px 8px;
}
.change-due {
    text-align: center;
    padding: 8px;
    border-radius: var(--radius-sm);
    background: #d1fae5;
    color: #065f46;
    font-weight: 700;
    font-size: 18px;
    direction: ltr;
}
.empty-cart {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
}
.empty-cart i {
    font-size: 40px;
    margin-bottom: 8px;
    opacity: 0.3;
}
.empty-cart p {
    margin: 0;
    font-size: 14px;
}
@media (max-width: 992px) {
    .pos-wrapper {
        flex-direction: column;
        height: auto;
    }
    .pos-products {
        flex: none;
        height: 50vh;
    }
    .pos-cart {
        flex: none;
    }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-cash-register text-gold me-2"></i>نقطة البيع</h1>
        </div>
        <div class="page-actions">
            <span class="time-cell" id="posClock"></span>
        </div>
    </div>
</div>

<div class="pos-wrapper">
    <div class="pos-products">
        <div class="category-tabs" id="categoryTabs">
            <button class="btn btn-gold btn-sm category-btn active" data-category="all">الكل</button>
            @foreach($categories as $category)
                <button class="btn btn-outline-gold btn-sm category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>

        <div class="product-grid" id="productGrid">
            @foreach($products as $product)
                <div class="product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-category="{{ $product->category_id }}">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price">{{ CurrencyHelper::formatDual($product->price, $exchangeRate) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="pos-cart">
        <div class="pos-cart-panel">
            <div class="cart-header">
                <h5><i class="fas fa-shopping-cart me-1"></i>الطلب</h5>
                <span class="text-muted" id="cartCount">0</span>
            </div>

            <div class="cart-items" id="cartItems">
                <div class="empty-cart" id="emptyCart">
                    <i class="fas fa-cart-plus"></i>
                    <p>أضف منتجات إلى الطلب</p>
                </div>
            </div>

            <div class="cart-summary" id="cartSummary" style="display:none;">
                <div class="summary-row">
                    <span>المجموع الفرعي</span>
                    <span class="summary-value" id="subtotalDisplay">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>الخصم</span>
                    <span class="summary-value" id="discountDisplay">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>الضريبة (15%)</span>
                    <span class="summary-value" id="taxDisplay">$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>الإجمالي</span>
                    <span class="summary-value" id="totalDisplay">$0.00 / 0 ل.ل</span>
                </div>
            </div>

            <div class="cart-footer" id="cartFooter" style="display:none;">
                <div>
                    <label>نوع الطلب</label>
                    <div class="order-type">
                        <input type="radio" name="order_type" id="typeDineIn" value="dine_in" checked class="btn-check">
                        <label for="typeDineIn" class="btn btn-outline-gold btn-sm"><i class="fas fa-chair me-1"></i>مباشر</label>
                        <input type="radio" name="order_type" id="typeTakeaway" value="takeaway" class="btn-check">
                        <label for="typeTakeaway" class="btn btn-outline-gold btn-sm"><i class="fas fa-shopping-bag me-1"></i>توصيل</label>
                    </div>
                </div>

                <div>
                    <label>اسم العميل (اختياري)</label>
                    <input type="text" class="form-control" id="customerName" placeholder="أدخل اسم العميل">
                </div>

                <div>
                    <label>الخصم</label>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control" id="discountInput" value="0" min="0" step="0.5">
                        <span class="input-group-text">$</span>
                    </div>
                </div>

                <div>
                    <label>طريقة الدفع</label>
                    <div class="payment-methods">
                        <input type="radio" name="payment_method" id="pmCash" value="cash" checked class="btn-check">
                        <label for="pmCash" class="btn btn-outline-gold btn-sm"><i class="fas fa-money-bill-wave me-1"></i>نقداً</label>
                        <input type="radio" name="payment_method" id="pmCard" value="card" class="btn-check">
                        <label for="pmCard" class="btn btn-outline-gold btn-sm"><i class="fas fa-credit-card me-1"></i>بطاقة</label>
                        <input type="radio" name="payment_method" id="pmOther" value="other" class="btn-check">
                        <label for="pmOther" class="btn btn-outline-gold btn-sm"><i class="fas fa-ellipsis-h me-1"></i>أخرى</label>
                    </div>
                </div>

                <div>
                    <label>المبلغ المدفوع</label>
                    <input type="number" class="form-control" id="amountReceived" value="0" min="0" step="0.5">
                </div>

                <div id="changeDueContainer" style="display:none;">
                    <div class="change-due" id="changeDue">$0.00 / 0 ل.ل</div>
                </div>

                <button type="button" class="btn btn-gold w-100" id="completeOrder">
                    <i class="fas fa-check-circle me-1"></i>إتمام الطلب
                </button>
            </div>
        </div>
    </div>
</div>

<form id="orderForm" method="POST" action="{{ route('sales.orders.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="items" id="formItems">
    <input type="hidden" name="order_type" id="formOrderType" value="dine_in">
    <input type="hidden" name="customer_name" id="formCustomerName">
    <input type="hidden" name="discount" id="formDiscount" value="0">
    <input type="hidden" name="payment_method" id="formPaymentMethod" value="cash">
    <input type="hidden" name="amount_received" id="formAmountReceived" value="0">
    <input type="hidden" name="subtotal" id="formSubtotal" value="0">
    <input type="hidden" name="tax" id="formTax" value="0">
    <input type="hidden" name="total" id="formTotal" value="0">
</form>

<script>
(function() {
    const EXCHANGE_RATE = {{ $exchangeRate }};
    function formatDual(amount) {
        return '$' + amount.toFixed(2) + ' / ' + Math.round(amount * EXCHANGE_RATE).toLocaleString('en-US') + ' ل.ل';
    }
    const TAX_RATE = 0.15;
    let cart = [];

    const productGrid = document.getElementById('productGrid');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const cartSummary = document.getElementById('cartSummary');
    const cartFooter = document.getElementById('cartFooter');
    const cartCount = document.getElementById('cartCount');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const discountDisplay = document.getElementById('discountDisplay');
    const taxDisplay = document.getElementById('taxDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const discountInput = document.getElementById('discountInput');
    const amountReceived = document.getElementById('amountReceived');
    const changeDue = document.getElementById('changeDue');
    const changeDueContainer = document.getElementById('changeDueContainer');
    const completeOrder = document.getElementById('completeOrder');
    const orderForm = document.getElementById('orderForm');

    function updateClock() {
        const now = new Date();
        const clock = document.getElementById('posClock');
        if (clock) clock.textContent = now.toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 30000);

    document.querySelectorAll('.category-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(function(b) { b.className = 'btn btn-outline-gold btn-sm category-btn'; });
            this.className = 'btn btn-gold btn-sm category-btn active';
            var cat = this.getAttribute('data-category');
            document.querySelectorAll('.product-card').forEach(function(card) {
                card.style.display = (cat === 'all' || card.getAttribute('data-category') === cat) ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('.product-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var id = parseInt(this.getAttribute('data-id'));
            var name = this.getAttribute('data-name');
            var price = parseFloat(this.getAttribute('data-price'));
            addToCart(id, name, price);
        });
    });

    function addToCart(id, name, price) {
        var existing = cart.find(function(item) { return item.id === id; });
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id: id, name: name, price: price, qty: 1 });
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(function(item) { return item.id !== id; });
        renderCart();
    }

    function updateQty(id, delta) {
        var item = cart.find(function(i) { return i.id === id; });
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            removeFromCart(id);
        } else {
            renderCart();
        }
    }

    function renderCart() {
        cartItems.innerHTML = '';
        var hasItems = cart.length > 0;
        emptyCart.style.display = hasItems ? 'none' : '';
        cartSummary.style.display = hasItems ? '' : 'none';
        cartFooter.style.display = hasItems ? '' : 'none';

        cart.forEach(function(item) {
            var lineTotal = item.price * item.qty;
            var div = document.createElement('div');
            div.className = 'cart-item';
            div.innerHTML = '<div class="cart-item-info"><div class="cart-item-name">' + item.name + '</div><div class="cart-item-price">' + formatDual(item.price) + '</div></div>' +
                '<div class="cart-qty"><button type="button" class="qty-dec" data-id="' + item.id + '"><i class="fas fa-minus"></i></button><span>' + item.qty + '</span><button type="button" class="qty-inc" data-id="' + item.id + '"><i class="fas fa-plus"></i></button></div>' +
                '<div class="cart-item-total">' + formatDual(lineTotal) + '</div>' +
                '<button type="button" class="cart-item-delete" data-id="' + item.id + '"><i class="fas fa-trash-alt"></i></button>';
            cartItems.appendChild(div);
        });

        cartCount.textContent = cart.reduce(function(sum, item) { return sum + item.qty; }, 0);
        calculateTotals();
        attachCartEvents();
    }

    function attachCartEvents() {
        document.querySelectorAll('.qty-inc').forEach(function(btn) {
            btn.addEventListener('click', function() { updateQty(parseInt(this.getAttribute('data-id')), 1); });
        });
        document.querySelectorAll('.qty-dec').forEach(function(btn) {
            btn.addEventListener('click', function() { updateQty(parseInt(this.getAttribute('data-id')), -1); });
        });
        document.querySelectorAll('.cart-item-delete').forEach(function(btn) {
            btn.addEventListener('click', function() { removeFromCart(parseInt(this.getAttribute('data-id'))); });
        });
    }

    function calculateTotals() {
        var subtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);
        var discount = parseFloat(discountInput.value) || 0;
        var afterDiscount = Math.max(0, subtotal - discount);
        var tax = afterDiscount * TAX_RATE;
        var total = afterDiscount + tax;

        subtotalDisplay.textContent = formatDual(subtotal);
        discountDisplay.textContent = formatDual(discount);
        taxDisplay.textContent = formatDual(tax);
        totalDisplay.textContent = formatDual(total);

        var received = parseFloat(amountReceived.value) || 0;
        var change = received - total;
        if (received >= total && total > 0) {
            changeDueContainer.style.display = '';
            changeDue.textContent = '$' + change.toFixed(2) + ' / ' + Math.round(change * EXCHANGE_RATE).toLocaleString('en-US') + ' ل.ل';
        } else {
            changeDueContainer.style.display = 'none';
        }
    }

    discountInput.addEventListener('input', calculateTotals);
    amountReceived.addEventListener('input', calculateTotals);

    document.querySelectorAll('input[name="order_type"]').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('formOrderType').value = this.value;
        });
    });

    document.querySelectorAll('input[name="payment_method"]').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('formPaymentMethod').value = this.value;
        });
    });

    completeOrder.addEventListener('click', function() {
        if (cart.length === 0) {
            alert('يرجى إضافة منتجات إلى الطلب');
            return;
        }

        var subtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);
        var discount = parseFloat(discountInput.value) || 0;
        var afterDiscount = Math.max(0, subtotal - discount);
        var tax = afterDiscount * TAX_RATE;
        var total = afterDiscount + tax;
        var received = parseFloat(amountReceived.value) || 0;

        if (document.querySelector('input[name="payment_method"]:checked').value === 'cash' && received < total) {
            alert('المبلغ المدفوع أقل من الإجمالي');
            return;
        }

        var items = cart.map(function(item) {
            return { product_id: item.id, quantity: item.qty, unit_price: item.price };
        });

        document.getElementById('formItems').value = JSON.stringify(items);
        document.getElementById('formCustomerName').value = document.getElementById('customerName').value;
        document.getElementById('formDiscount').value = discount.toFixed(2);
        document.getElementById('formAmountReceived').value = received.toFixed(2);
        document.getElementById('formSubtotal').value = subtotal.toFixed(2);
        document.getElementById('formTax').value = tax.toFixed(2);
        document.getElementById('formTotal').value = total.toFixed(2);

        orderForm.submit();
    });
})();
</script>
@endsection
