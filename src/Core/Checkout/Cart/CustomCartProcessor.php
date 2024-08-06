<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

// to retrieve and calculate
class CustomCartProcessor implements CartProcessorInterface
{
    /**
     * @param CartDataCollection $data
     * @param Cart $original
     * @param Cart $toCalculate
     * @param SalesChannelContext $context
     * @param CartBehavior $behavior
     * @return void
     */
    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $a = $data->get('test_key');
    }
}