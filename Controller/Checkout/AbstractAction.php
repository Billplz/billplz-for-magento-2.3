<?php

namespace Billplz\BillplzPaymentGateway\Controller\Checkout;

use Billplz\BillplzPaymentGateway\Gateway\Config\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

/**
 * @package Billplz\BillplzPaymentGateway\Controller\Checkout
 */
abstract class AbstractAction extends Action
{

    const LOG_FILE = 'billplz.log';

    private $_context;

    private $_checkoutSession;

    private $_orderFactory;

    private $_gatewayConfig;

    private $_messageManager;

    private $_logger;

    public function __construct(
        Config $gatewayConfig,
        Session $checkoutSession,
        Context $context,
        OrderFactory $orderFactory,
        LoggerInterface $logger) {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_gatewayConfig = $gatewayConfig;
        $this->_messageManager = $context->getMessageManager();
        $this->_logger = $logger;
    }

    protected function getContext()
    {
        return $this->_context;
    }

    protected function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    protected function getOrderFactory()
    {
        return $this->_orderFactory;
    }

    protected function getGatewayConfig()
    {
        return $this->_gatewayConfig;
    }

    protected function getMessageManager()
    {
        return $this->_messageManager;
    }

    protected function getLogger()
    {
        return $this->_logger;
    }

    protected function getOrder()
    {
        $orderId = $this->_checkoutSession->getLastRealOrderId();

        if (!isset($orderId)) {
            return null;
        }

        return $this->getOrderById($orderId);
    }

    protected function getOrderById($orderId)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);

        if (!$order->getId()) {
            return null;
        }

        return $order;
    }

    protected function getObjectManager()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

}
