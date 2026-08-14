<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.storefront')]
#[Title('Secure Checkout | Sneakyard')]
final class Checkout extends Component
{
    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $shipping_address = '';

    public string $shipping_city = '';

    public string $shipping_province = '';

    public string $shipping_postal_code = '';

    public string $payment_method = 'cod';

    public string $customer_notes = '';

    public function mount(CartService $cart): void
    {
        if ($cart->items()->isEmpty()) {
            $this->redirectRoute('cart');
        }
    }

    public function placeOrder(OrderService $orders): void
    {
        $data = $this->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_province' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'payment_method' => ['required', 'in:cod'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = $orders->place($data, auth()->user());
        session()->flash('order_placed', true);
        $this->redirectRoute('checkout.success', ['order' => $order->public_id]);
    }

    public function render(CartService $cart): View
    {
        return view('livewire.storefront.checkout', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'shipping' => $cart->shippingTotal(),
            'total' => $cart->total(),
        ]);
    }
}
