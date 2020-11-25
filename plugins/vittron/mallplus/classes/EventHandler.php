<?php

namespace Vittron\Mallplus\Classes;

use Backend\Facades\Backend;
use Cms\Classes\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use October\Rain\Support\Facades\Config;
use OFFLINE\Mall\Classes\Jobs\SendOrderConfirmationToCustomer;
use OFFLINE\Mall\Models\GeneralSettings;

class EventHandler
{
    public function subscribe($events)
    {
        $events->listen('mall.order.filled', self::class . '@orderFilled');
    }

    /**
     * Order was finally filled.
     *
     * @param $result
     *
     * @throws \Cms\Classes\CmsException
     */
    public function orderFilled($result)
    {
        // Notify the customer
        $input = [
            'id'          => $result->order->id,
            'template'    => 'vittron.mallplus::checkout.succeeded',
            'account_url' => $this->getAccountUrl(),
            'order_url'   => $this->getBackendOrderUrl($result->order),
        ];
        // Push the PDF generation and mail send call to the queue.
        Queue::push(SendOrderConfirmationToCustomer::class, $input);

        // Notify the admin
        if ($adminMail = GeneralSettings::get('admin_email')) {
            $data = [
                'order'       => $result->order->fresh(['products', 'customer']),
                'account_url' => $this->getAccountUrl(),
                'order_url'   => $this->getBackendOrderUrl($result->order),
            ];
            Mail::queue('vittron.mallplus::admin.checkout_succeeded', $data,
                function ($message) use ($adminMail) {
                    $message->to($adminMail);
                });
        }
    }

     /**
     * Return the direct URL to a customer's account page.
     *
     * @param string $page
     *
     * @return string
     * @throws \Cms\Classes\CmsException
     */
    protected function getAccountUrl($page = 'orders'): string
    {
        return (new Controller())->pageUrl(
            GeneralSettings::get('account_page'), ['page' => $page]
        );
    }

    /**
     * Returns the direct URL to the order details.
     *
     * @param $order
     *
     * @return string
     */
    protected function getBackendOrderUrl($order): string
    {
        return Backend::url('offline/mall/orders/show/' . $order->id);
    }
}