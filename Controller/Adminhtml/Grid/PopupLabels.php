<?php


namespace TM\PriceTickets\Controller\Adminhtml\Grid;


class PopupLabels extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        try {
            $productids = $this->_request->getParam('productlist');
            $this->_view->loadLayout();
            $this->getResponse()->setBody(
                $this->_view->getLayout()
                    ->createBlock('TM\PriceTickets\Block\Adminhtml\Lists\Products')
                    ->setTemplate('TM_PriceTickets::productslist.phtml')
                    ->setData("productlist", $productids)->toHtml());
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die("here");
        }
    }


}