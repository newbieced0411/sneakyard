<x-layouts.storefront meta-title="Order received | Sneakyard">
    <section class="success-page">
        <flux:icon.check-circle class="success-icon" />
        <p class="eyebrow">Order received</p>
        <h1>Thanks, {{ $order->customer_name }}.</h1>
        <p>Your order <strong>{{ $order->order_number }}</strong> is now in our queue. We’ll send updates to {{ $order->customer_email }}.</p>
        <div class="success-summary"><span>Total</span><strong>{{ $order->formatted_total }}</strong><span>Payment</span><strong>Cash on delivery</strong></div>
        <a class="primary-button" href="{{ route('shop') }}">Continue shopping</a>
    </section>
</x-layouts.storefront>
