<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
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

        /////////////////////////////////////
        /// set new price
        /// ////////////////////////////////
        // get all product ids of current cart
        $productIds = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE)->getReferenceIds();

        // remove all product ids which are already fetched from the database
        $filtered = $this->filterAlreadyFetchedPrices($productIds, $data);

        // Skip execution if there are no prices to be requested & saved
        if (empty($filtered)) {
            return;
        }

        foreach ($filtered as $id) {
            $key = $this->buildKey($id);

            // Needs implementation, just an example
            $newPrice = 49;

            // we have to set a value for each product id to prevent duplicate queries in next calculation
            $data->set($key, $newPrice);
        }
    }

    private function filterAlreadyFetchedPrices(array $productIds, CartDataCollection $data): array
    {
        $filtered = [];

        foreach ($productIds as $id) {
            $key = $this->buildKey($id);

            // already fetched from database?
            if ($data->has($key)) {
                continue;
            }

            $filtered[] = $id;
        }

        return $filtered;
    }

    private function buildKey(string $id): string
    {
        return 'price-overwrite-'.$id;
    }
}