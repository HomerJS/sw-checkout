<?php declare(strict_types=1);

namespace Ihor\CheckOut\Decorator\Core\Content\Product\SalesChannel\Price;

use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ProductPriceCalculatorDecorator extends AbstractProductPriceCalculator
{
    /**
     * @var AbstractProductPriceCalculator
     */
    private AbstractProductPriceCalculator $productPriceCalculator;

    public function __construct(AbstractProductPriceCalculator $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    public function getDecorated(): AbstractProductPriceCalculator
    {
        return $this->productPriceCalculator;
    }

    /**
     * @inheritDoc
     */
    public function calculate(iterable $products, SalesChannelContext $context): void
    {
        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            $price = $product->getPrice();
            // Just an example!
            // A product can have more than one price, which you also have to consider.
            // Also you might have to change the value of "getCheapestPrice"!
            $price->first()->setGross(100);
            $price->first()->setNet(50);
        }

        $this->getDecorated()->calculate($products, $context);
    }
}