<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Rule\LineItemRule;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

// to retrieve and calculate
class CustomCartProcessor implements CartProcessorInterface
{
    private PercentagePriceCalculator $calculator;
    private QuantityPriceCalculator $quantityPriceCalculator;

    public function __construct(
        PercentagePriceCalculator $calculator,
        QuantityPriceCalculator $quantityPriceCalculator
    ) {
        $this->calculator = $calculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
    }

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

        //////////////////////////////
        /// set discount
        /// ///////////////////////////
        $products = $toCalculate->getLineItems();
        $discountLineItem = $this->createDiscount('EXAMPLE_DISCOUNT');

        // declare price definition to define how this price is calculated
        $definition = new PercentagePriceDefinition(
            -10,
            new LineItemRule(Rule::OPERATOR_EQ, $products->getKeys())
        );

        $discountLineItem->setPriceDefinition($definition);

        // calculate price
        $discountLineItem->setPrice(
            $this->calculator->calculate($definition->getPercentage(), $products->getPrices(), $context)
        );

        // add discount to new cart
        $toCalculate->add($discountLineItem);

        ///////////////////////////////////////////////////////////


        ////////////////////////////////////////////////
        /// set new price
        /// //////////////////////////////////////
        // get all product line items
        $products = $toCalculate->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);

        foreach ($products as $product) {
            $key = $this->buildKey($product->getReferencedId());

            // no overwritten price? continue with next product
            if (!$data->has($key) || $data->get($key) === null) {
                continue;
            }

            $newPrice = $data->get($key);

            // build new price definition
            $definition = new QuantityPriceDefinition(
                $newPrice,
                $product->getPrice()->getTaxRules(),
                $product->getPrice()->getQuantity()
            );

            // build CalculatedPrice over calculator class for overwritten price
            $calculated = $this->quantityPriceCalculator->calculate($definition, $context);

            // set new price into line item
            $product->setPrice($calculated);
            $product->setPriceDefinition($definition);
        }
    }

    private function createDiscount(string $name): LineItem
    {
        $discountLineItem = new LineItem($name, 'example_discount', null, 1);

        $discountLineItem->setLabel('Our example discount!');
        $discountLineItem->setGood(false);
        $discountLineItem->setStackable(false);
        $discountLineItem->setRemovable(false);

        return $discountLineItem;
    }

    private function buildKey(string $id): string
    {
        return 'price-overwrite-'.$id;
    }
}