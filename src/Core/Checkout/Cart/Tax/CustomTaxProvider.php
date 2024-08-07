<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart\Tax;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTax;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\TaxProvider\AbstractTaxProvider;
use Shopware\Core\Checkout\Cart\TaxProvider\Struct\TaxProviderResult;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CustomTaxProvider extends AbstractTaxProvider
{
    public function provide(Cart $cart, SalesChannelContext $context): TaxProviderResult
    {
        $lineItemTaxes = [];

        foreach ($cart->getLineItems() as $lineItem) {
            $taxRate = 50;
            $price = $lineItem->getPrice()->getTotalPrice();
            $tax = $price * $taxRate / 100;

            // shopware will look for the `uniqueIdentifier` property of the lineItem to identify this lineItem even in nested-line-item structures
            $lineItemTaxes[$lineItem->getUniqueIdentifier()] = new CalculatedTaxCollection(
                [
                    new CalculatedTax($tax, $taxRate, $price),
                ]
            );
        }

        // you could do the same for deliveries
        // $deliveryTaxes = []; // use the id of the delivery position as keys, if you want to transmit delivery taxes

        // foreach ($cart->getDeliveries() as $delivery) {
        //     foreach ($delivery->getPositions() as $position) {
        //         $deliveryTaxes[$delivery->getId()] = new CalculatedTaxCollection(...);
        //         ...
        //     }
        // }

        // If you call a tax provider, you will probably get calculated tax sums for the whole cart
        // Use the cartPriceTaxes to let Shopware show the correct sums in the checkout
        // If omitted, Shopware will try to calculate the tax sums itself
        // $cartPriceTaxes = [];

        return new TaxProviderResult(
            $lineItemTaxes,
        // $deliveryTaxes,
        // $cartPriceTaxes
        );
    }
}