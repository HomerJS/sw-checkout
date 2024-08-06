<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

//set data
class CustomCartCollector implements CartDataCollectorInterface
{
    /**
     * Add to cart collector
     *
     * @param CartDataCollection $data - new cart
     * @param Cart $original - current cart
     * @param SalesChannelContext $context - channel info
     * @param CartBehavior $behavior -
     * @return void
     */
    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
          $data->set('test_key', 'test');
//        var_dump('asdasd');die;
    }
}