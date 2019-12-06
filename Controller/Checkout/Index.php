<?php

namespace Billplz\BillplzPaymentGateway\Controller\Checkout;

use Magento\Sales\Model\Order;

/**
 * @package Billplz\BillplzPaymentGateway\Controller\Checkout
 */
class Index extends AbstractAction
{

    private function createBill($order)
    {
        if ($order == null) {
            $this->getLogger()->debug('Unable to get order from last lodged order id. Possibly related to a failed database call');
            $this->_redirect('checkout/onepage/error', array('_secure' => false));
        }

        $orderId = $order->getRealOrderId();
        $gatewayConf = $this->getGatewayConfig();

        return [200, array("url" => "https://www.billplz.com/" . $gatewayConf->getCollectionId())];

        // $order->getOrderCurrencyCode() // String: MYR ??

        $data = array(
            'x_currency' => '',
            'x_url_callback' => $this->getDataHelper()->getCompleteUrl(),
            'x_url_complete' => $this->getDataHelper()->getCompleteUrl(),
            'x_url_cancel' => $this->getDataHelper()->getCancelledUrl($orderId),
            'x_shop_name' => $this->getDataHelper()->getStoreCode(),
            'x_account_id' => $this->getGatewayConfig()->getMerchantNumber(),
            'x_reference' => $orderId,
            'x_invoice' => $orderId,
            'x_amount' => $order->getTotalDue(),
            'x_customer_first_name' => $order->getCustomerFirstname(),
            'x_customer_last_name' => $order->getCustomerLastname(),
            'x_customer_email' => $order->getData('customer_email'),
            'x_customer_phone' => $billingAddress->getData('telephone'),
            'x_customer_billing_address1' => $billingAddressParts[0],
            'x_customer_billing_address2' => count($billingAddressParts) > 1 ? $billingAddressParts[1] : '',
            'x_customer_billing_city' => $billingAddress->getData('city'),
            'x_customer_billing_state' => $billingAddress->getData('region'),
            'x_customer_billing_zip' => $billingAddress->getData('postcode'),
            'x_customer_shipping_address1' => $shippingAddressParts[0],
            'x_customer_shipping_address2' => count($shippingAddressParts) > 1 ? $shippingAddressParts[1] : '',
            'x_customer_shipping_city' => $shippingAddress->getData('city'),
            'x_customer_shipping_state' => $shippingAddress->getData('region'),
            'x_customer_shipping_zip' => $shippingAddress->getData('postcode'),
            'x_test' => 'false',
        );

        foreach ($data as $key => $value) {
            $data[$key] = preg_replace('/\r\n|\r|\n/', ' ', $value);
        }

        $apiKey = $this->getGatewayConfig()->getApiKey();
        $signature = $this->getCryptoHelper()->generateSignature($data, $apiKey);
        $data['x_signature'] = $signature;

        return $data;
    }

    private function redirectToBill($shouldRedirect, $bill)
    {
        if ($shouldRedirect) {
            $this->renderRedirect($bill['url']);
        } else {
            $this->getLogger()->debug('Bill creation failed: ' . print_r($payload, true));
            $this->_redirect('checkout/cart');
        }
    }

    private function renderRedirect($bill_url)
    {
        echo
            "<html>
            <body>
            <a href=\"$bill_url\">Click here to Pay</a>
            </body>
            <script>
                window.location.replace(\"$bill_url\");
            </script>
            </html>";
    }

    /**
     *
     *
     * @return void
     */
    public function execute()
    {
        try {
            $order = $this->getOrder();
            if ($order->getState() === Order::STATE_PENDING_PAYMENT) {
                list($rheader, $bill) = $this->createBill($order);
                $this->redirectToBill($rheader === 200, $bill);
            } else if ($order->getState() === Order::STATE_CANCELED) {
                // $errorMessage = $this->getCheckoutSession()->getOxipayErrorMessage(); //set in InitializationRequest
                $errorMessage = "To do";
                if ($errorMessage) {
                    $this->getMessageManager()->addWarningMessage($errorMessage);
                    $errorMessage = $this->getCheckoutSession()->unsOxipayErrorMessage();
                }
                $this->getCheckoutHelper()->restoreQuote(); //restore cart
                $this->_redirect('checkout/cart');
            } else {
                $this->getLogger()->debug('Order in unrecognized state: ' . $order->getState());
                $this->_redirect('checkout/cart');
            }
        } catch (Exception $ex) {
            $this->getLogger()->debug('An exception was encountered in billplz/checkout/index: ' . $ex->getMessage());
            $this->getLogger()->debug($ex->getTraceAsString());
            $this->getMessageManager()->addErrorMessage(__('Unable to start Billplz Checkout.'));
        }
    }

}
