@extends('layouts.app')

@section('title', 'طلب جديد')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<style>
.pos-wrapper {
    display: flex;
    gap: 20px;
    height: calc(100vh - 140px);
    min-height: 500px;
}
.pos-products { flex: 7; display: flex; flex-direction: column; gap: 16px; }
.pos-cart { flex: 3; display: flex; flex-direction: column; gap: 16px; }
.pos-cart-panel {
    background: var(--card-bg);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    transition: var(--transition);
}
.pos-cart-panel:hover {
    box-shadow: var(--shadow-md);
    border-color: rgba(201, 169, 97, 0.1);
}
.category-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 4px 0;
    flex-shrink: 0;
}
.category-tabs .btn { white-space: nowrap; flex-shrink: 0; }
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
    border-radius: var(--radius-md);
    padding: 20px 12px 16px;
    text-align: center;
    cursor: pointer;
    transition: var(--transition-bounce);
    user-select: none;
    position: relative;
    overflow: hidden;
}
.product-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(201, 169, 97, 0.04) 0%, transparent 100%);
    opacity: 0;
    transition: var(--transition);
}
.product-card:hover {
    border-color: var(--gold);
    box-shadow: 0 8px 30px rgba(201, 169, 97, 0.18);
    transform: translateY(-4px);
}
.product-card:hover::before { opacity: 1; }
.product-card:active { transform: scale(0.95); }
.product-card .product-name {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 6px;
    color: var(--text-primary);
    position: relative;
}
.product-card .product-price {
    font-size: 15px;
    font-weight: 800;
    color: var(--gold-dark);
    direction: ltr;
    position: relative;
    transition: var(--transition);
}
.product-card:hover .product-price { color: var(--gold); }
.cart-header {
    padding: 16px 18px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.cart-header h5 { margin: 0; font-weight: 700; }
.cart-items {
    flex: 1;
    overflow-y: auto;
    padding: 8px 12px;
}
.cart-items::-webkit-scrollbar { width: 3px; }
.cart-items::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
.cart-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 8px;
    border-bottom: 1px solid var(--border-light);
    animation: fadeSlideUp 0.25s ease;
    border-radius: var(--radius-xs);
    transition: var(--transition);
}
.cart-item:hover {
    background: rgba(201, 169, 97, 0.04);
}
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-name { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
.cart-item-price { font-size: 12px; color: var(--text-muted); direction: ltr; }
.cart-qty { display: flex; align-items: center; gap: 4px; }
.cart-qty button {
    width: 30px; height: 30px; border-radius: 50%;
    border: 1.5px solid var(--border-color);
    background: var(--body-bg);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 12px;
    transition: var(--transition-bounce);
    color: var(--text-primary);
}
.cart-qty button:hover {
    border-color: var(--gold);
    color: var(--gold);
    background: rgba(201, 169, 97, 0.06);
    transform: scale(1.1);
}
.cart-qty button:active { transform: scale(0.9); }
.cart-qty span { width: 28px; text-align: center; font-weight: 700; font-size: 14px; }
.cart-item-total {
    font-weight: 700; font-size: 13px;
    direction: ltr; min-width: 70px; text-align: left;
    color: var(--text-primary);
    transition: var(--transition);
}
.cart-item:hover .cart-item-total { color: var(--gold-dark); }
.cart-item-delete {
    background: none; border: none;
    color: #ef4444; cursor: pointer; font-size: 14px;
    padding: 4px; transition: var(--transition-bounce);
    opacity: 0.4;
    border-radius: 50%; width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
}
.cart-item-delete:hover {
    opacity: 1;
    background: rgba(239, 68, 68, 0.1);
    transform: scale(1.15);
}
.cart-summary {
    padding: 14px 18px;
    border-top: 1px solid var(--border-color);
    display: flex; flex-direction: column;
    gap: 8px; flex-shrink: 0;
}
.cart-footer {
    padding: 14px 18px;
    border-top: 1px solid var(--border-color);
    display: flex; flex-direction: column; gap: 10px; flex-shrink: 0;
}
.cart-footer .form-control { font-size: 13px; }
.cart-footer label {
    font-size: 12px; font-weight: 600;
    color: var(--text-muted); margin-bottom: 2px;
}
.empty-cart { text-align: center; padding: 40px 20px; color: var(--text-muted); }
.empty-cart i { font-size: 40px; margin-bottom: 8px; opacity: 0.2; }
.empty-cart p { margin: 0; font-size: 14px; }
.session-alert {
    padding: 12px 18px;
    border-radius: var(--radius-sm);
    margin-bottom: 18px;
    display: flex; align-items: center;
    gap: 10px; font-size: 14px; font-weight: 500;
    animation: fadeSlideUp 0.4s ease;
}
.session-alert.open {
    background: rgba(52, 211, 153, 0.08);
    color: #6ee7b7; border: 1px solid rgba(52, 211, 153, 0.12);
}
.session-alert.closed {
    background: rgba(251, 191, 36, 0.08);
    color: #fde68a; border: 1px solid rgba(251, 191, 36, 0.12);
}
@media (max-width: 992px) {
    .pos-wrapper { flex-direction: column; height: auto; }
    .pos-products { flex: none; height: 50vh; }
    .pos-cart { flex: none; }
}
</style>

<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-cash-register text-gold me-2"></i>طلب جديد</h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('sales.orders.index') }}" class="btn btn-outline-gold btn-sm">
                <i class="fas fa-arrow-right me-1"></i>عودة للطلبات
            </a>
        </div>
    </div>
</div>

@if($openSession)
    <div class="session-alert open">
        <i class="fas fa-check-circle"></i>
        <span>الفترة الحالية: #{{ $openSession->session_number }} ({{ $openSession->created_at->format('Y-m-d H:i') }})</span>
    </div>
@else
    <div class="session-alert closed">
        <i class="fas fa-exclamation-triangle"></i>
        <span>لا توجد فترة مفتوحة. يرجى بدء فترة جديدة أولاً.</span>
    </div>
@endif

<div class="pos-wrapper">
    <div class="pos-products">
        <div class="category-tabs" id="categoryTabs">
            <button class="btn btn-gold btn-sm category-btn active" data-category="all">الكل</button>
            @foreach($categories as $category)
                <button class="btn btn-outline-gold btn-sm category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>

        <div class="product-grid" id="productGrid">
            @foreach($categories as $category)
                @foreach($category->products as $product)
                    <div class="product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ (float) $product->price }}" data-category="{{ $product->category_id }}">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-price">{{ CurrencyHelper::formatDual($product->price, $exchangeRate) }}</div>
                    </div>
                @endforeach
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
                <div class="summary-row total">
                    <span>الإجمالي</span>
                    <span class="summary-value" id="totalDisplay">$0.00 / 0 ل.ل</span>
                </div>
            </div>

            <div class="cart-footer" id="cartFooter" style="display:none;">
                <div>
                    <label>ملاحظات (اختياري)</label>
                    <textarea class="form-control" id="notesInput" rows="2" placeholder="ملاحظات الطلب"></textarea>
                </div>

                <button type="button" class="btn btn-gold w-100" id="submitOrder" {{ !$openSession ? 'disabled' : '' }}>
                    <i class="fas fa-check-circle me-1"></i>إنشاء الطلب
                </button>
            </div>
        </div>
    </div>
</div>

<form id="orderForm" method="POST" action="{{ route('sales.orders.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="session_id" value="{{ $openSession ? $openSession->id : '' }}">
    <input type="hidden" name="items" id="formItems">
    <input type="hidden" name="notes" id="formNotes">
</form>

<script>
(function() {
    const EXCHANGE_RATE = {{ $exchangeRate }};
    function formatDual(amount) {
        amount = parseFloat(amount) || 0;
        return '$' + amount.toFixed(2) + ' / ' + Math.round(amount * EXCHANGE_RATE).toLocaleString('en-US') + ' ل.ل';
    }
    let cart = [];

    const productGrid = document.getElementById('productGrid');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const cartSummary = document.getElementById('cartSummary');
    const cartFooter = document.getElementById('cartFooter');
    const cartCount = document.getElementById('cartCount');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const notesInput = document.getElementById('notesInput');
    const submitOrder = document.getElementById('submitOrder');
    const orderForm = document.getElementById('orderForm');

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
        subtotalDisplay.textContent = formatDual(subtotal);
        totalDisplay.textContent = formatDual(subtotal);
    }

    submitOrder.addEventListener('click', function() {
        if (cart.length === 0) {
            alert('يرجى إضافة منتجات إلى الطلب');
            return;
        }

        var items = cart.map(function(item) {
            return { product_id: item.id, quantity: item.qty };
        });

        document.getElementById('formItems').value = JSON.stringify(items);
        document.getElementById('formNotes').value = notesInput.value;
        orderForm.submit();
    });
})();
</script>
@endsection