<?php

namespace TM\PriceTickets\Block\Adminhtml\Lists;

use Magento\Framework\View\Element\Template;

class Products extends Template
{
    public function __construct(
        Template\Context $context,
        array $data = [],
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getProducts($productIds)
    {

        $collection = $this->productFactory->create()
            ->getCollection()
            ->addAttributeToSelect("*")
            ->addFieldToFilter('entity_id', array('in' => $productIds));
        return $collection;
    }
}