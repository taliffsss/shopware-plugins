<?php

declare(strict_types=1);

namespace MinimalOffcanvasPlugin;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MinimalOffcanvasPlugin extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->autowire(OffCanvasCartPageLoaderDecorator::class)
            ->setDecoratedService('Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoader');

        $container->autowire(CartControllerDecorator::class)
            ->setDecoratedService('Shopware\Storefront\Controller\CartLineItemController');
    }

    public function install(InstallContext $installContext): void
    {
        $this->createCustomFields($installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }
        $this->removeCustomFields($uninstallContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        // Activation logic
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        // Deactivation logic
    }

    public function update(UpdateContext $updateContext): void
    {
        // Update logic
    }

    private function createCustomFields(\Shopware\Core\Framework\Context $context): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $customFieldSet = [
            'name' => 'minimal_offcanvas_cross_selling',
            'config' => [
                'label' => [
                    'en-GB' => 'Minimal Offcanvas Cross-Selling',
                    'de-DE' => 'Minimaler Offcanvas Cross-Selling',
                ],
            ],
            'customFields' => [
                [
                    'name' => 'cross_selling_index',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Cross-Selling Group Index',
                            'de-DE' => 'Cross-Selling-Gruppenindex',
                        ],
                        'customFieldPosition' => 1,
                    ],
                ],
            ],
            'relations' => [
                [
                    'entityName' => 'product',
                ],
            ],
        ];

        $customFieldSetRepository->create([$customFieldSet], $context);
    }

    private function removeCustomFields(\Shopware\Core\Framework\Context $context): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addFilter(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('name', 'minimal_offcanvas_cross_selling'));

        $customFieldSetIds = $customFieldSetRepository->searchIds($criteria, $context)->getIds();

        if (! empty($customFieldSetIds)) {
            $customFieldSetRepository->delete(array_map(static function ($id) {
                return ['id' => $id];
            }, $customFieldSetIds), $context);
        }
    }
}
