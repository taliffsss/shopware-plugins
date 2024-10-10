<?php

declare(strict_types=1);

namespace MinimalOffcanvasPlugin\Core\Content\Product\SalesChannel\Decorator;

use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\AbstractProductCrossSellingRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPage;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoader;
use Symfony\Component\HttpFoundation\Request;

class OffCanvasCartPageLoaderDecorator extends OffcanvasCartPageLoader
{
    private OffcanvasCartPageLoader $original;

    private AbstractProductCrossSellingRoute $crossSellingRoute;

    private SystemConfigService $systemConfigService;

    private CartService $cartService;

    public function __construct(
        OffcanvasCartPageLoader $original,
        AbstractProductCrossSellingRoute $crossSellingRoute,
        SystemConfigService $systemConfigService,
        CartService $cartService
    ) {
        $this->original = $original;
        $this->crossSellingRoute = $crossSellingRoute;
        $this->systemConfigService = $systemConfigService;
        $this->cartService = $cartService;
    }

    public function load(Request $request, SalesChannelContext $context): OffcanvasCartPage
    {
        $page = $this->original->load($request, $context);

        $lastLineItem = $this->cartService->getCart($context->getToken(), $context)->getLineItems()->last();
        if ($lastLineItem && $lastLineItem->getType() === 'product') {
            $productId = $lastLineItem->getReferencedId();
            $crossSellingProducts = $this->getCrossSellingProducts($productId, $context);
            $page->assign(['crossSellingProducts' => $crossSellingProducts]);
        }

        return $page;
    }

    private function getCrossSellingProducts(string $productId, SalesChannelContext $context): array
    {
        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('crossSellings');

        $product = $this->crossSellingRoute->load($productId, new Request(), $context, $criteria)->getProduct();

        if (! $product instanceof ProductEntity) {
            return [];
        }

        $crossSellingGroup = $this->determineCrossSellingGroup($product);
        if (! $crossSellingGroup) {
            return [];
        }

        $crossSellingResult = $this->crossSellingRoute->load($productId, new Request(), $context, $criteria);

        return $crossSellingResult->getResult()->get($crossSellingGroup->getId())?->getProducts() ?? [];
    }

    private function determineCrossSellingGroup(ProductEntity $product): ?ProductCrossSellingEntity
    {
        $customFieldIndex = $product->getCustomFields()['cross_selling_index'] ?? null;
        $configIndex = $this->systemConfigService->get('MinimalOffcanvasPlugin.config.crossSellingIndex');

        $crossSellings = $product->getCrossSellings();
        if (! $crossSellings) {
            return null;
        }

        if ($customFieldIndex !== null) {
            return $crossSellings->filter(fn ($cs) => $cs->getPosition() === (int) $customFieldIndex)->first() ?? null;
        }

        if ($configIndex !== null) {
            return $crossSellings->filter(fn ($cs) => $cs->getPosition() === (int) $configIndex)->first() ?? null;
        }

        return $crossSellings->first();
    }
}
