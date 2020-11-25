<?php

namespace Vittron\Mallplus\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use October\Rain\Support\Facades\Url;
use OFFLINE\Mall\Classes\Payments\PaymentRedirector;
use OFFLINE\Mall\Components\SignUp as BaseSignUp;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Order;
use OFFLINE\Mall\Models\PaymentMethod;
use Vittron\Mallplus\Classes\SignUpHandler;

class SignUp extends BaseSignUp
{
    /**
     * @var Collection<PaymentMethod>
     */
    public $paymentMethods;

    public function init()
    {
        parent::init();

        $this->paymentMethods = PaymentMethod::orderBy('sort_order', 'ASC')->get();
    }

     /**
     * The user signs up for a new account and makes the order.
     *
     * @return \Illuminate\Http\RedirectResponse|array
     * @throws \Exception
     */
    public function onSignUp()
    {
        $data                          = post();
        $data['requires_confirmation'] = $this->requiresConfirmation;

        if ($user = app(SignUpHandler::class)->handle($data, (bool)post('as_guest'))) {
            if ($this->requiresConfirmation) {
                return ['.mall-signup-form' => $this->renderPartial($this->alias . '::confirm.htm')];
            }

            $cart = Cart::byUser($user);
            $cart->payment_method_id = $data['payment_method'];
            $cart->save();

            $order = DB::transaction(function() use ($cart) {
                return Order::fromCart($cart);
            });
            $order->customer_notes = $data['customer_notes'];
            $order->save();

            $payment = new PaymentRedirector('');
            $payment->order = $order;

            Event::fire('mall.order.filled', [$payment]);

            $this->setProperty('redirect', Url::to('checkout/done?order=' . $this->encode($order->id)));
            
            return $this->redirect();
        }
    }

}
