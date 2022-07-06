<?php


namespace TM\PriceTickets\Block\Adminhtml\PriceTickets;


class Dashboard extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('hel_tab');
        $this->setDestElementId('edit_form');
//        $this->setTitle(__('Your Title'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'abcd',
            [
                'label' => __('Products'),
                'url' => $this->getUrl('pricetickets/grid/products', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        return parent::_prepareLayout();
    }

}