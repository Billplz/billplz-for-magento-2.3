<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Billplz\BillplzPaymentGateway\Model\Ui;

// use Billplz\BillplzPaymentGateway\Gateway\Http\Client\ClientMock;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'billplz_gateway';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [];
        // return [
        //     'payment' => [
        //         self::CODE => [
        //             'transactionResults' => [
        //                 ClientMock::SUCCESS => __('Success'),
        //                 ClientMock::FAILURE => __('Fraud'),
        //             ],
        //         ],
        //     ],
        // ];
    }
}
