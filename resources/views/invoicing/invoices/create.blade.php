@extends('layouts.app')

@section('title', 'إنشاء فاتورة جديدة')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-plus-circle text-gold me-2"></i> إنشاء فاتورة جديدة</h1>
            <p class="text-muted">إنشاء فاتورة للعميل</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('invoicing.invoices.index') }}" class="btn btn-outline-gold"><i class="fas fa-arrow-right"></i> العودة للفواتير</a>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom">
        <form method="POST" action="{{ route('invoicing.invoices.store') }}" id="invoiceForm">
            @csrf

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="اسم العميل">
                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" placeholder="رقم الهاتف">
                    @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card-header-custom px-0 pt-0">
                <h5 class="mb-0"><i class="fas fa-shopping-cart text-gold me-1"></i> المنتجات</h5>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35%">المنتج</th>
                            <th style="width:15%">الكمية</th>
                            <th style="width:20%">سعر الوحدة</th>
                            <th style="width:15%">الإجمالي</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer">
                        <tr class="item-row">
                            <td>
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control qty" value="1" required></td>
                            <td><input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control unit-price" value="0" required></td>
                            <td><input type="text" class="form-control item-total" value="0.00" readonly dir="ltr"></td>
                            <td><button type="button" class="btn btn-outline-danger btn-sm remove-item" disabled><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-outline-gold btn-sm mb-4" id="addItem"><i class="fas fa-plus"></i> إضافة منتج</button>

            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label">الإجمالي الفرعي</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                        <input type="text" id="subtotal" class="form-control amount-cell" value="0.00" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الخصم</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                        <input type="number" step="0.01" min="0" name="discount" class="form-control" id="discountInput" value="{{ old('discount', 0) }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الضريبة</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-percent"></i></span>
                        <input type="number" step="0.01" min="0" name="tax" class="form-control" id="taxInput" value="{{ old('tax', 0) }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">الإجمالي</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                        <input type="text" id="totalAmount" class="form-control amount-cell fw-bold" value="0.00" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="ملاحظات إضافية">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> إنشاء الفاتورة</button>
                <a href="{{ route('invoicing.invoices.index') }}" class="btn btn-outline-gold"><i class="fas fa-times"></i> إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let itemIndex = 1;

document.getElementById('addItem').addEventListener('click', function() {
    const row = document.querySelector('.item-row').cloneNode(true);
    const inputs = row.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) input.name = input.name.replace(/\[\d+\]/g, '[' + itemIndex + ']');
        if (input.type !== 'text' || !input.classList.contains('item-total')) {
            if (input.type === 'number') {
                if (input.classList.contains('qty')) input.value = 1;
                else if (input.classList.contains('unit-price')) input.value = 0;
            }
        }
    });
    row.querySelector('.item-total').value = '0.00';
    row.querySelector('.remove-item').disabled = false;
    row.querySelectorAll('.product-select').forEach(sel => {
        sel.addEventListener('change', function() {
            const price = this.options[this.selectedIndex]?.dataset?.price || 0;
            const priceInput = this.closest('tr').querySelector('.unit-price');
            if (priceInput && !priceInput.dataset.manual) priceInput.value = price;
            calcRow(this.closest('tr'));
        });
        sel.value = '';
    });
    row.querySelector('.qty').addEventListener('input', function() { calcRow(this.closest('tr')); });
    row.querySelector('.unit-price').addEventListener('input', function() { this.dataset.manual = '1'; calcRow(this.closest('tr')); });
    row.querySelector('.remove-item').addEventListener('click', function() { this.closest('tr').remove(); calcAll(); });
    document.getElementById('itemsContainer').appendChild(row);
    itemIndex++;
});

document.querySelectorAll('.product-select').forEach(sel => {
    sel.addEventListener('change', function() {
        const price = this.options[this.selectedIndex]?.dataset?.price || 0;
        const priceInput = this.closest('tr').querySelector('.unit-price');
        if (priceInput && !priceInput.dataset.manual) priceInput.value = price;
        calcRow(this.closest('tr'));
    });
});
document.querySelectorAll('.qty, .unit-price').forEach(inp => {
    inp.addEventListener('input', function() {
        if (inp.classList.contains('unit-price')) inp.dataset.manual = '1';
        calcRow(this.closest('tr'));
    });
});
document.getElementById('discountInput').addEventListener('input', calcAll);
document.getElementById('taxInput').addEventListener('input', calcAll);

function calcRow(row) {
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const price = parseFloat(row.querySelector('.unit-price').value) || 0;
    const total = qty * price;
    row.querySelector('.item-total').value = total.toFixed(2);
    calcAll();
}

function calcAll() {
    let sub = 0;
    document.querySelectorAll('.item-total').forEach(inp => { sub += parseFloat(inp.value) || 0; });
    document.getElementById('subtotal').value = sub.toFixed(2);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const tax = parseFloat(document.getElementById('taxInput').value) || 0;
    const afterDiscount = sub - discount;
    const total = afterDiscount + tax;
    document.getElementById('totalAmount').value = Math.max(0, total).toFixed(2);
}
</script>
@endsection
