<?php

namespace Vittron\Mallplus\Components;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use October\Rain\Exception\ValidationException;
use OFFLINE\Mall\Classes\Payments\PaymentRedirector;
use OFFLINE\Mall\Components\Checkout as BaseCheckout;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Order;
use OFFLINE\Mall\Models\PaymentMethod;
use RainLab\User\Facades\Auth;
use Vittron\Mallplus\Classes\SignUpHandler;

class Checkout extends BaseCheckout
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
    public function onCheckout()
    {
        if (!($user = Auth::user())) {
            return false;
        }
        $data = post();

        $this->validate($data);

        if ($data['firstname'] != $user->name 
                || $data['lastname'] != $user->surname
                || $data['phone'] != $user->phone) {
            $user->name = $data['firstname'];
            $user->surname = $data['lastname'];
            $user->phone = $data['phone'];
            $user->save();
            $user->customer->firstname = $data['firstname'];
            $user->customer->lastname = $data['lastname'];
            $user->customer->save();
        }

        $address = $user->customer->billingAddress;
        if ($data['billing_city'] != $address->city || $data['billing_lines'] != $address->lines) {
            $address->city = $data['billing_city'];
            $address->lines = $data['billing_lines'];
            $address->save();
        }

        $this->cart = Cart::byUser($user);
        $this->cart->payment_method_id = $data['payment_method'];
        $this->cart->save();

        $order = DB::transaction(function() {
            return Order::fromCart($this->cart);
        });
        $order->customer_notes = $data['customer_notes'];
        $order->save();

        $payment = new PaymentRedirector('');
        $payment->order = $order;

        Event::fire('mall.order.filled', [$payment]);

        // $this->setVar('step', 'done');


        return redirect()->to('checkout/done?' . http_build_query(['order' => $this->encode($order->id)]));
    }

    /**
     * @throws ValidationException
     */
    protected function validate(array $data)
    {
        $rules = self::rules();

        $messages = SignUpHandler::messages();

        $validation = Validator::make($data, $rules, $messages);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
    }

    public static function rules(): array
    {
        $rules = [
            'firstname'           => 'required',
            'lastname'            => 'required',
            'phone'               => 'required|digits:10',
            'billing_lines'       => 'required',
            'billing_city'        => 'required',
            'terms_accepted'      => 'required',
        ];

        return $rules;
    }
}
