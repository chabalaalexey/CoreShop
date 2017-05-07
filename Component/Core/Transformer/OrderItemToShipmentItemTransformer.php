<?php

namespace CoreShop\Component\Core\Transformer;

use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use Pimcore\Model\Object\Fieldcollection;
use Webmozart\Assert\Assert;

class OrderItemToShipmentItemTransformer implements OrderDocumentItemTransformerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var string
     */
    private $pathForItems;

    /**
     * @param ObjectServiceInterface $objectService
     * @param string $pathForItems
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems
    )
    {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderDocumentInterface $shipment, OrderItemInterface $orderItem, OrderDocumentItemInterface $shipmentItem, $quantity)
    {
        /**
         * @var $shipment OrderInvoiceInterface
         * @var $orderItem OrderItemInterface
         * @var $shipmentItem OrderShipmentItemInterface
         */
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($shipment, OrderDocumentInterface::class);
        Assert::isInstanceOf($shipmentItem, OrderDocumentItemInterface::class);

        $itemFolder = $this->objectService->createFolderByPath($shipment->getFullPath() . '/' . $this->pathForItems);

        $shipmentItem->setKey($orderItem->getKey());
        $shipmentItem->setParent($itemFolder);
        $shipmentItem->setPublished(true);

        $shipmentItem->setOrderItem($orderItem);
        $shipmentItem->setQuantity($quantity);
        $shipmentItem->setTotal($orderItem->getItemPrice(true) * $quantity, true);
        $shipmentItem->setTotal($orderItem->getItemPrice(false) * $quantity, false);
        $shipmentItem->setWeight($orderItem->getTotalWeight());

        $shipmentItem->save();

        return $shipmentItem;
    }
}