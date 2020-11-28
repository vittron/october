<?php

namespace Vittron\Mallplus\Components;


use OFFLINE\Mall\Components\Cart as BaseCart;
use Auth;
use OFFLINE\Mall\Models\Cart as CartModel;

class Cart extends BaseCart
{
    /**
     * The user removed an item from the cart.
     *
     * @return array
     */
    public function onRemoveProduct()
    {
        $id = $this->decode(input('id'));

        $cart = CartModel::byUser(Auth::getUser());

        $product = $this->getProductFromCart($cart, $id);

        $cart->removeProduct($product);
        $cart->refresh();

        $this->setData();

        return [
            'item'     => $this->dataLayerArray($product->product, $product->variant),
            'quantity' => $product->quantity,
            'new_items_count' => optional($cart->products)->count() ?? 0,
            'new_items_quantity' => optional($cart->products)->sum('quantity') ?? 0,
        ];
    }

     /**
     * Return the dataLayer representation of an item.
     *
     * @param null $product
     * @param null $variant
     *
     * @return array
     */
    private function dataLayerArray($product = null, $variant = null)
    {
        $item = $variant ?? $product;

        return [
            'id'       => $item->prefixedId,
            'name'     => $product->name,
            'price'    => $item->price()->decimal,
            'brand'    => optional($item->brand)->name,
            'category' => $item->categories->first()->name,
            'variant'  => optional($variant)->name,
        ];
    }
}
