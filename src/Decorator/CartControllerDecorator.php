<?php

declare(strict_types=1);

namespace MinimalOffcanvasPlugin\Decorator;

use Shopware\Storefront\Controller\CartLineItemController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartControllerDecorator extends CartLineItemController
{
    private CartLineItemController $decorated;

    public function __construct(CartLineItemController $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @Route("/checkout/line-item/add", name="frontend.checkout.line-item.add", methods={"POST"}, defaults={"XmlHttpRequest"=true})
     */
    public function addLineItem(Request $request): Response
    {
        $response = $this->decorated->addLineItem($request);

        // Use the minimal template for AJAX requests
        if ($request->isXmlHttpRequest()) {
            $response->setData([
                'cart' => $this->cartService->getCart($request->getSession()->getId(), $this->salesChannelContext),
                'offcanvasTemplate' => '@MinimalOffcanvasPlugin/storefront/component/checkout/offcanvas-cart-minimal.html.twig',
            ]);
        }

        return $response;
    }
}
