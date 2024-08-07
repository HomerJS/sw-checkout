<?php declare(strict_types=1);

namespace Ihor\CheckOut;

use Ihor\CheckOut\Core\Checkout\Cart\Tax\CustomTaxProvider;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Uuid\Uuid;

class IhorCheckOut extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        //add tax provider
        $ruleRepo = $this->container->get('rule.repository');

        // create a rule, which will be used to determine the availability of your tax provider
        // do not rely on specific rules to be always present
        $rule = $ruleRepo->create([
            [
                'name' => 'Cart > 0',
                'priority' => 0,
                'conditions' => [
                    [
                        'type' => 'cartCartAmount',
                        'value' => [
                            'amount' => 0,
                            'operator' => '>=',
                        ],
                    ],
                ],
            ],
        ], $installContext->getContext());

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('name', 'Cart > 0')
        );

        $ruleId = $ruleRepo->searchIds($criteria, $installContext->getContext())->firstId();

        $taxRepo = $this->container->get('tax_provider.repository');
        $taxRepo->create([
            [
                'id' => Uuid::randomHex(),
                'identifier' => CustomTaxProvider::class,
                'priority' => 1,
                'active' => false, // activate this via the `activate` lifecycle method
                'availabilityRuleId' => $ruleId,
            ],
        ], $installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        // Remove or deactivate the data created by the plugin
    }

    public function activate(ActivateContext $activateContext): void
    {
        // Activate entities, such as a new payment method
        // Or create new entities here, because now your plugin is installed and active for sure
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        // Deactivate entities, such as a new payment method
        // Or remove previously created entities
    }

    public function update(UpdateContext $updateContext): void
    {
        // Update necessary stuff, mostly non-database related
    }

    public function postInstall(InstallContext $installContext): void
    {
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
    }
}
