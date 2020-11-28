<?php

namespace Vittron\Mallplus\Classes;

use DB;
use Event;
use Illuminate\Support\Facades\Validator;
use October\Rain\Exception\ValidationException;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use OFFLINE\Mall\Models\Wishlist;
use RainLab\Location\Models\Country;
use RainLab\User\Facades\Auth;
use RainLab\User\Models\UserGroup;
use Vittron\Mallplus\Models\Address;

class SignUpHandler extends \OFFLINE\Mall\Classes\Customer\DefaultSignUpHandler
{
    public function handle(array $data, bool $createAccount = true): ?User
    {
        // fix since we changed asGuest checkbock to the asSignedUpUser
        $this->asGuest = !$createAccount;

        return $this->signUp($data);
    }
    
    /**
     * @throws ValidationException
     */
    protected function signUp(array $data): ?User
    {
        if ($this->asGuest) {
            $data['password'] = $data['password_repeat'] = str_random(30);
        }

        // choose Ukraine as default country for all orders, for now
        if (empty($data['billing_country_id'])) {
            $data['billing_country_id'] = Country::firstWhere('name', 'Ukraine')->id;
        }

        $this->validate($data);

        $requiresConfirmation = ($data['requires_confirmation'] ?? false);

        Event::fire('mall.customer.beforeSignup', [$this, $data]);

        $user = DB::transaction(function () use ($data, $requiresConfirmation) {

            $user = $this->createUser($data, $requiresConfirmation);

            $customer            = new Customer();
            $customer->firstname = $data['firstname'];
            $customer->lastname  = $data['lastname'];
            $customer->user_id   = $user->id;
            $customer->is_guest  = $this->asGuest;
            $customer->save();

            $addressData = $this->transformAddressKeys($data, 'billing');
            $fullname    = $data['firstname'] . ' ' . $data['lastname'];

            $billing = new Address();
            $billing->fill($addressData);
            $billing->name        = $addressData['address_name'] ?? $fullname;
            $billing->customer_id = $customer->id;
            $billing->save();

            $customer->default_billing_address_id = $billing->id;
            $customer->default_shipping_address_id = $billing->id;
            $customer->save();

            Cart::transferSessionCartToCustomer($user->customer);
            Wishlist::transferToCustomer($user->customer);

            return $user;
        });

        // To prevent multiple guest accounts with the same email address we edit
        // the email of all existing guest accounts registered to the same email.
        $this->renameExistingGuestAccounts($data, $user);

        Event::fire('mall.customer.afterSignup', [$this, $user]);

        if ($requiresConfirmation === true) {
            return $user;
        }

        $credentials = [
            'login'    => array_get($data, 'email'),
            'password' => array_get($data, 'password'),
        ];

        return Auth::authenticate($credentials, true);
    }

    protected function createUser($data, $requiresConfirmation): User
    {
        $data = [
            'name'                  => $data['firstname'],
            'surname'               => $data['lastname'],
            'email'                 => $data['email'],
            'phone'                 => $data['phone'],
            'password'              => $data['password'],
            'password_confirmation' => $data['password_repeat'],
        ];

        $user = Auth::register($data, ! $requiresConfirmation);
        if ($this->asGuest && $user && $group = UserGroup::getGuestGroup()) {
            $user->groups()->sync($group);
        } else {
            $user->groups()->sync([]);
        }

        return $user;
    }

    /**
     * @throws ValidationException
     */
    protected function validate(array $data)
    {
        $rules = self::rules();

        if ($this->asGuest) {
            unset($rules['password'], $rules['password_repeat']);
        }

        $messages = self::messages();

        $validation = Validator::make($data, $rules, $messages);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
    }

    public static function rules($forSignup = true): array
    {
        $rules = [
            'firstname'           => 'required',
            'lastname'            => 'required',
            'email'               => ['required', 'email', ($forSignup ? 'non_existing_user' : null)],
            'phone'               => 'required|digits:10',
            'billing_lines'       => 'required',
            'billing_city'        => 'required',
            'billing_country_id'  => 'required|exists:rainlab_location_countries,id',
            'password'            => 'required|min:8|max:255',
            'password_repeat'     => 'required|same:password',
            'terms_accepted'      => 'required',
        ];

        return $rules;
    }

    public static function messages(): array
    {
        return array_merge(parent::messages(), [
            'phone.required'      => trans('offline.mall::lang.components.signup.errors.phone.required'),
            'phone.numeric'       => trans('offline.mall::lang.components.signup.errors.phone.numeric'),
            'phone.min'           => trans('offline.mall::lang.components.signup.errors.phone.min'),
            'phone.max'           => trans('offline.mall::lang.components.signup.errors.phone.max'),
        ]);
    }
}
