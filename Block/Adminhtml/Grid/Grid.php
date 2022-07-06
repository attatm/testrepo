<?php


namespace TM\PriceTickets\Block\Adminhtml\Grid;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $productFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('hello_tab_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->productFactory->create()->getCollection()->addAttributeToSelect("*");
        $this->setCollection($collection);
//        echo "<pre>";
//        foreach ($collection as $product){
//            print_r($product->getData());
//die("here");
//        }
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product Id'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Product Name'),
                'index' => 'name'
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price'
            ]
        );

        $this->addColumn(
            'size',
            [
                'header' => __('Size'),
                'index' => 'size'
            ]
        );
        
        $this->addColumn(
            'display_locations',
            [
                'header' => __('Display Location'),
                'index' => 'display_locations'
            ]
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('tm_checkbox_list');  //html name of checkbox
        $this->getMassactionBlock()->addItem('addqueue', array(
            'label' => __('Add to Queue'),
            'url'  => $this->getUrl('tilemo/*/tmSyncMassactionAddtoQueue'),   //an action defined in the controller
            // 'selected' => 'selected',
        ));

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('pricetickets/grid/products', ['_current' => true]);
    }

}