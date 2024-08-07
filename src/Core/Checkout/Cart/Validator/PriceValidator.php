<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart\Validator;

use Ihor\CheckOut\Core\Checkout\Cart\Validator\Error\PriceValidationError;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PriceValidator implements CartValidatorInterface
{
    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        foreach ($cart->getLineItems()->getFlat() as $lineItem) {
            if ($lineItem->getPrice()->getTotalPrice() > 1150) {
                $errors->add(new PriceValidationError($lineItem->getId()));

                return;
            }
        }
    }
}