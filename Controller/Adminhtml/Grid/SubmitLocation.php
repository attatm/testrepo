<?php


namespace TM\PriceTickets\Controller\Adminhtml\Grid;

//use Ebizmarts\SagePaySuite\Model\Logger\Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\User\Model\UserFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ProductFactory as ProductFactory;
use TM\PriceTickets\Helper\Data;
class SubmitLocation extends Action
{
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeinterface,
        JsonFactory $resultJsonFactory,
        UserFactory $userFactory,
        ProductRepository $productRepository,
        ProductFactory $productFactory,
        Data $helper
    )
    {
        $this->_scopeinterface = $scopeinterface;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->userFactory = $userFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {

        $return_data = array();

        $product_locations = $this->getRequest()->getParams();
        $product_list = array();
        $product_display_location = array();

        try{
            $return_data['success'] = true;
            $return_data['message'] = "Labels Printed Successfully..";
            $this->helper->generatePdf($product_locations);
        }catch (\Exception $exception){
            $return_data['success'] = false;
            $return_data['message'] = $exception->getMessage();
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($return_data);
        return $resultJson;
//        die("gmends");
//        if (isset($product_locations['display_locations'])){
//            foreach ($product_locations['display_locations'] as $product_id => $display_locations) {
//                $product_display_loc = explode(',', $display_locations);
//                if(!empty($product_display_loc)){
//                    $product = $this->productFactory->create()->loadByAttribute('entity_id',$product_id);
//
//                    for ($i=0; $i <count($product_display_loc); $i++) {
//                        if($product_display_loc[$i]!=""){
//                            $this->helper->generatePdf($product,$product_display_loc[$i],"");
//                        }
//                    }
//
////                    $product->setDisplayLocations($product_display_loc);
////                    $product->getResource()->saveAttribute($product, 'display_locations');
//
//                }
//
//            }
//        }
        die("heres");



//        if(isset($product_locations['luxe_locations'])){
//
//            foreach ($product_locations['luxe_locations'] as $product_id => $luxe_locations) {
//
//                $product_luxe_loc = explode(',', $luxe_locations);
//                if(!empty($product_luxe_loc)){
//                    $_product = $this->productFactory->create()->loadByAttribute('id',$product_id);
//
//                    for ($i=0; $i <count($product_luxe_loc); $i++) {
//                        if($product_luxe_loc[$i]!=""){
//                            $this->helper->getluxBg($_product,$product_luxe_loc[$i],$storeName);
//                        }
//
//                    }
//                    //$new = $_product->getData();
//                    $_product->setLuxeLocations($luxe_locations);
//                    $_product->getResource()->saveAttribute($_product, 'luxe_locations');
//                    if(!array_key_exists($product_id,$productArray)){
//                        $productArray[$product_id]['name'] = $_product->getName();//['name'];
//                        $productArray[$product_id]['sku'] = $_product->getSku();
//                        $productArray[$product_id]['size'] = $_product->getSize();
//                        $productArray[$product_id]['price'] = $_product->getPrice();
//                        $productArray[$product_id]['msrp'] = $_product->getMsrp();
//                        $productArray[$product_id]['luxe_locations'] = $luxe_locations;
//
//
//                    }else{
//                        $productArray[$product_id]['luxe_locations'] = $luxe_locations;
//
//                    }
//                    $_product->unset();
//
//                }
//
//
//
//
//
//            }
//
//            if(is_array($productArray) && isset($productArray))
//            {
//
//                foreach($productArray as $key=>$p){
//                    $priceTicketModel = Mage::getModel('pdflabel/pdflabel');
//                    $priceTicketModel->setData('name',$productArray[$key]['name']);
//                    $priceTicketModel->setData('product_id',$key);
//                    $priceTicketModel->setData('sku',$productArray[$key]['sku']);
//                    $priceTicketModel->setData('size',$productArray[$key]['size']);
//                    $priceTicketModel->setData('price',$productArray[$key]['price']);
//                    $priceTicketModel->setData('msrp',$productArray[$key]['msrp']);
//
//                    $priceTicketModel->setData('display_locations',$productArray[$key]['display_locations']);
//                    $priceTicketModel->setData('luxe_locations',$productArray[$key]['luxe_locations']);
//                    $priceTicketModel->setData('user_id',$userId);
//                    $priceTicketModel->setData('created_at',Mage::getModel('core/date')->date());
//
//
//                    $priceTicketModel->save();
//                    $priceTicketModel->unset();
//                }
//            }
//
//        }
    }
}