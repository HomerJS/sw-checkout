<?php declare(strict_types=1);

namespace Ihor\CheckOut\Core\Checkout\Cart\Validator\Error;

use Shopware\Core\Checkout\Cart\Error\Error;

class PriceValidationError extends Error
{
    private const KEY = 'custom-price-validator-error';

    private string $lineItemId;

    public function __construct(string $lineItemId)
    {
        $this->lineItemId = $lineItemId;
        parent::__construct();
    }

    public function getId(): string
    {
        return $this->lineItemId;
    }

    public function getMessageKey(): string
    {
        return self::KEY;
    }

    public function getLevel(): int
    {
//        return self::LEVEL_NOTICE;
//        return self::LEVEL_WARNING;
        return self::LEVEL_ERROR;
    }

    //should cart be blocked
    public function blockOrder(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        //extra field to payload info
        return [ 'lineItemId' => $this->lineItemId ];
    }
}