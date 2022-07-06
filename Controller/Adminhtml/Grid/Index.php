<?php


namespace TM\PriceTickets\Controller\Adminhtml\Grid;


use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }


    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('TM_PriceTickets::grid');
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('TM PriceTickets'));
        $this->_addContent($this->_view->getLayout()->createBlock('TM\PriceTickets\Block\Adminhtml\PriceTickets\Dashboard'));
        $this->_view->renderLayout();
    }
}