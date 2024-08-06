<?php declare(strict_types=1);

namespace Ihor\CheckOut\Storefront\Controller;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class TestController extends StorefrontController
{
    private LineItemFactoryRegistry $factory;

    private CartService $cartService;

    public function __construct(LineItemFactoryRegistry $factory, CartService $cartService)
    {
        $this->factory = $factory;
        $this->cartService = $cartService;
    }

    #[Route(path: '/cartAdd', name: 'frontend.example', methods: ['GET'])]
    public function add(Cart $cart, SalesChannelContext $context): Response
    {
        // Create product line item
        $lineItem = $this->factory->create([
            'type' => LineItem::PRODUCT_LINE_ITEM_TYPE, // Results in 'product'
            'referencedId' => '018fec75209572ec9a053d8d7614c56a', // this is not a valid UUID, change this to your actual ID!
            'quantity' => 5,
            'payload' => ['key' => 'value']
        ], $context);

        $this->cartService->add($cart, $lineItem, $context);

        return $this->renderStorefront('@Storefront/storefront/base.html.twig');
    }
}