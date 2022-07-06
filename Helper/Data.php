<?php


namespace TM\PriceTickets\Helper;

use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Sales\Model\OrderFactory;
use Magento\SecondModule\Model\Model;
use \TM\Base\Helper\Data as BaseData;
use \TM\Base\Helper\Local as LocalHelper;
use \Magento\Catalog\Model\ProductFactory;

require_once(BP . '/lib/internal/TCPDF/TCPDF.php');


error_reporting(E_ALL);
ini_set('display_errors', 1);

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        JsonFactory $jsonFactory,
        OrderFactory $orderFactory,
        BaseData $baseHelper,
        LocalHelper $localHelper,
        ProductFactory $productFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    )
    {
        $this->_resultJsonFactory = $jsonFactory;
        $this->_orderFactory = $orderFactory;
        $this->baseHelper = $baseHelper;
        $this->localHelper = $localHelper;
        $this->productFactory = $productFactory;
        $this->authSession = $authSession;
        $this->scopeConfig = $scopeConfig;
        $this->_dir = $dir;
        $this->moduleReader = $moduleReader;
        $this->collectionFactory = $collectionFactory;
    }

    public function generatePdfGM($product_locations)
    {
    }

    public function generatePdf($product_locations)
    {

        $skus = array();
        $location_s = array();
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(100, 100), true, 'UTF-8', false);
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'TM_PriceTickets'
        );
        $fontname = \TCPDF_FONTS::addTTFfont($viewDir . '/Oblik-Bold.ttf', 'TrueTypeUnicode', '', 20);
        $pdf->SetFont($fontname, '', 10, '', false);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setPageMark();
        $pdf->setPrintFooter(false);

        $topstart = 1;
        $xstart = 6;
        $xend = 90;
        
        $fontSizeFinalPrice = 18;
        $fontSizeSKU = 11;
        $fontSizePrice = 8;
        foreach ($product_locations['display_locations'] as $product_id => $display_locations) {
            $product_display_loc = explode(',', $display_locations);
            if (!empty($product_display_loc)) {
                $product = $this->productFactory->create()->loadByAttribute('entity_id', $product_id);

                for ($i = 0; $i < count($product_display_loc); $i++) {
                    if ($product_display_loc[$i] != "") {
                        $location = $product_display_loc[$i];
                        $location_s[] = $location;
                        if (is_object($product)) {
                            $name = $product->getName();
                            $Barcode = $product->getSku(); //sku;
                            $price = $product->getPrice();
                            $final_price = $product->getFinalPrice();
                            $skus[] = $Barcode;

                        }

                        $location = ($location != '') ? $location : '';

                        $chartname = strlen($name) - 41;
                        $namefontsize = 15;
                        if ($chartname <= 0) {
                            $namefontsize = 15;
                        } else {
                            $namefontsize = 15 / ((strlen($name) / 36));
                        }
                        
                        
                        if(strlen($name) >= 50){
                            $namefontsize = 11;
                        }else{
                            $namefontsize = 14;
                        }

                        $params = $pdf->serializeTCPDFtagParameters(array($Barcode, 'C39', 55, 65, 30, 5, 0.4, array('position' => 'S', 'align' => 'C', 'border' => false, 'padding' => 0, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4), 'N'));

                        $nameHtml = '<span style="font-weight: 300;">' . $name . '</span>';
                        $priceHtml = '<span style="font-weight: 400; flo">&pound;' . number_format($final_price, 2) . '</span><span style="color: #000085;font-size: 55px;">&nbsp;&nbsp;</span>';
                        $skuHtml = '<span style="font-weight: 200;">SKU: ' . $Barcode . '</span><span style="color: #000085;font-size: 55px;">&nbsp;&nbsp;</span>';
                        $priceHtml2 = '<span style="font-weight: 50;"><strike>RRP: ' . number_format($price, 2). '</strike></span><span style="color: #000085;font-size: 55px;">&nbsp;&nbsp;</span>';
                        $HTML_Merged1 = $nameHtml  ;
                        $HTML_Merged2 = $priceHtml.$skuHtml  ;
                        $html = '<tcpdf method="write1DBarcode" params="' . $params . '" />';;
                        $priceStart = $topstart + 4;

                        $pdf->SetFont($fontname, '', $namefontsize, '', false);
                        $pdf->writeHTMLCell($xend, 0, $xstart, $topstart, $nameHtml, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);
                        
                        
                        
                        $pdf->SetFont($fontname, '', $fontSizeFinalPrice, '', false);
                        $pdf->writeHTMLCell($xend, 0, $xstart, $priceStart, $priceHtml, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);
                        
                        
                        
                        $pdf->SetFont($fontname, '', $fontSizeSKU, '', false);
                        $pdf->writeHTMLCell($xend, 0, $xstart + 30, $priceStart, $skuHtml, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);

                        $pdf->SetFont($fontname, '', $fontSizePrice, '', false);
                        $pdf->writeHTMLCell($xend, 0, $xstart , $priceStart+4, $priceHtml2, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);
                        
                        $topstart = $topstart + 25;
                        //$pdf->writeHTML($html, true, false, true, false, '');
                        $product->setDisplayLocations($location);
                        $product->getResource()->saveAttribute($product, 'display_locations');

                    }
                }
            }
        }
        $localFolder = "m2prictickets";
        $directory = $this->localHelper->getLocalDirectory("m2prictickets");
        $_file = 'pdf-' . implode('-', $skus) . '-' . implode('-', $location_s) . '.pdf';
        $file = $directory . '/' . $_file;
        $pdf->Output($file, 'F');
        $this->localHelper->moveFileToLocal($_file,'',$localFolder,false,1,1);
    }

    public function getLocalDirectory($type)

    {

        $directory = $this->_dir->getRoot() . '/' . $type;

        if (!file_exists($directory)) {

            $dirMode = 0777;

            mkdir($directory, $dirMode, true);

        }

        return $directory;

    }


    public function getMsrp($product)
    {
        $msrp = trim($product->getMsrp());
        $width = trim($product->getWidth());
        $height = trim($product->getHeight());
        $type = trim($product->getData('product_type'));
        $boards = $product->getBoardPerSqm();
        $msrpsqm = 0.00;
        if (($type == "Tile" || $type == "Pack" || $type == "Modular" || $type == "Mosaic" || $boards) && $width != "" && $height != "") {
            $is_hexagon = trim($product->getAttributeText('is_hexagon'));
            if ($is_hexagon == 'Yes') {
                $hexagon_length = (3 * sqrt(3) * (pow(trim($product->getHexagonLength()), 2))) / 2;
            } else {
                $hexagon_length = '';
            }

            $width = (int)$width;

            $height = (int)$height;

            if ($width != 0 && $height != 0) {


                if ($hexagon_length != '' && $hexagon_length > 0) {
                    $multiplier = 1000000 / $hexagon_length;
                } else {
                    $multiplier = 1000000 / ($width * $height);
                }

                /* Get the price with discount applied for quantity to make up a metre. */

                $finalPrice = $product->getTierPrice($multiplier, $product) * $multiplier;
                $fp = $product->getFinalPrice();
                $p = $product->getPrice();
                /* Check if a special price is active. */
                if ($fp < $p) {

                    $finalPrice = $fp * $multiplier;

                }
                if ($boards) {
                    //$SqM = 1/($width * $height)*100;
                    $finalPrice = $product->getFinalPrice() * $boards;
                }

                if ($msrp != "" && $finalPrice != 0) {
                    $msrpsqm = $msrp * $multiplier;

                }

            }


        }
        return $msrpsqm;
    }


    public function getPersqm($product)
    {
        $width = trim($product->getWidth());
        $height = trim($product->getHeight());
        $type = trim($product->getData('product_type'));
        $boards = $product->getBoardPerSqm();
        if (($type == "Tile" || $type == "Pack" || $type == "Modular" || $type == "Mosaic" || $boards) && $width != "" && $height != "") {
            $is_hexagon = trim($product->getAttributeText('is_hexagon'));
            if ($is_hexagon == 'Yes') {
                $hexagon_length = (3 * sqrt(3) * (pow(trim($product->getHexagonLength()), 2))) / 2;
            } else {
                $hexagon_length = '';
            }

            $width = (int)$width;

            $height = (int)$height;

            if ($width != 0 && $height != 0) {


                if ($hexagon_length != '' && $hexagon_length > 0) {
                    $multiplier = 1000000 / $hexagon_length;
                } else {
                    $multiplier = 1000000 / ($width * $height);
                }

                /* Get the price with discount applied for quantity to make up a metre. */

                $finalPrice = $product->getTierPrice($multiplier, $product) * $multiplier;
                $fp = $product->getFinalPrice();
                $p = $product->getPrice();
                /* Check if a special price is active. */
                if ($fp < $p) {

                    $finalPrice = $fp * $multiplier;

                }
                if ($boards) {
                    //$SqM = 1/($width * $height)*100;
                    $finalPrice = $product->getFinalPrice() * $boards;
                }


                return $finalPrice;
            }
        }
    }

    /**
     * getluxBg function to print luxes display location label
     * @param obj product
     * @return string location
     */
    public function getluxBg($product, $location, $storeName)
    {

        $html1 = "";
        $html2 = "";
        $html3 = "";

        if (is_object($product)) {
            $name = $product->getName();
            $Barcode = $product->getSku(); //sku;
            $size = $product->getSize();
            $productId = $product->getId();
            $productFullDetail = $this->productFactory->create()->load($productId);//Mage::getModel('catalog/product')->load($productId);
            $typeforBox = trim($productFullDetail->getData('product_type'));
            $area = round($product->getQtyPerSqm(), 2);
            $msrp = $this->getMsrp($product);
            $price = $this->getPersqm($product);
            if ($typeforBox == "Pack") {
                $price_per_pack = ($productFullDetail['width'] * $productFullDetail['height']) / 1000000;
                $pricePerPackm2 = $price_per_pack * $price;
                $permname = round($price_per_pack, 0);
            }

        } else {

            $name = $product['nameone'] . '<br/>' . $product['nametwo'];
            $Barcode = ($product['sku'] == '') ? implode('-', explode(' ', $product['name'])) : $product['sku']; //sku;
            $size = $product['size'];
            $area = round($product['area'], 2);
            $msrp = $product['oldprice'];
            $price = $product['newprice'];

        }
        $location = ($location != '') ? $location : '';

        $chartname = strlen($name) - 41;
        $namefontsize = 17;
        if ($chartname <= 0) {
            $namefontsize = 17;
        } else {
            $namefontsize = 17 / ((strlen($name) / 38));
        }

        $pdf = new \TCPDF_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(100, 100), true, 'UTF-8', false);
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'TM_PriceTickets'
        );
        $fontname = \TCPDF_FONTS::addTTFfont($viewDir . '/Oblik-Bold.ttf', 'TrueTypeUnicode', '', 32);
        $fontPath = $viewDir . '/CODE39.ttf';
        $pdf->SetFont($fontname, '', 12, '', false);


        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setPrintHeader(false);
        $pdf->SetMargins(15, 20, 10, true);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false, 0);
        //$pdf->Image(Mage::getBaseDir('media')."/luxe-bg.jpg", 0, 0, 100, 100, '', '', '', false, 100, '', false, false, 0);
        $pdf->setPageMark();
        $pdf->setPrintFooter(false);

        $params = $pdf->serializeTCPDFtagParameters(array($Barcode, 'C39', 14, 85, 30, 8, 0.8, array('position' => 'S', 'align' => 'C', 'border' => false, 'padding' => 0, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255), 'text' => false, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4), 'N'));


        $nameHtml = '<span style="font-weight: 600;">' . $name . '</span>';

        $html = '<tcpdf method="write1DBarcode" params="' . $params . '" />';;

        if ($price != "") {
            $html1 .= '
                <span style="font-size: 30px;">&pound;' . number_format($price, 2) . '</span><span style="color: #000085;font-size: 12px;">&nbsp;&nbsp;</span>
                <br/>';
        } else {
            $html1 .= '
                <span style="font-size: 30px;">&nbsp;&nbsp;</span><span style="color: #000085;font-size: 12px;">&nbsp;&nbsp;</span>
                <br/>';
        }
        if ($msrp && $msrp > 0) {
            $html2 .= '<span style="color:#000;font-size: 12px;"><del>RRP &pound;' . number_format($msrp, 2) . '</del></span>';
        } else {
            $html2 .= '<span style="color:#000;font-size: 12px;">&nbsp;&nbsp;</span>';
        }
        if ($typeforBox == "Pack") {
            $html3 .= '
                <span style="font-size: 30px;">&pound;' . number_format($pricePerPackm2, 2) . '</span><span style="color: #000085;font-size: 12px;">&nbsp;&nbsp;</span>
                <span style="font-size: 12px;">&nbsp;&nbsp;</span>
                <br/>';
        }
        //$pdf->AddPage();
        $pdf->SetFont($fontname, '', $namefontsize, '', false);
        $pdf->writeHTMLCell(0, 0, 14, 19, $nameHtml, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->writeHTMLCell(0, 0, 14, 35, $html1, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell(0, 0, 14, 44, $html2, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);
        $pdf->writeHTMLCell(0, 0, 14, 47, $html3, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true);

        $pdf->SetXY(22, 59); // 20 = margin left

        $pdf->SetFont($fontname, '', 12, '', false);
        $pdf->Cell(0, 0, $size . ' mm', 0, false, 'L');

        $pdf->SetXY(22, 65); // 20 = margin left

        $pdf->SetFont($fontname, '', 12, '', false);
        $pdf->Cell(0, 0, $area . ' tiles/m2', 0, false, 'L');

        $pdf->SetXY(22, 70); // 20 = margin left

        $pdf->SetFont($fontname, '', 12, '', false);
        $pdf->Cell(0, 0, $Barcode, 0, false, 'L');


        $pdf->SetXY(15, 93.5); // 20 = margin left
        //$pdf->SetFont(PDF_FONT_NAME_MAIN, $fontname, '9');
        $pdf->SetFont($fontname, '', 10, '', false);
        $pdf->Cell(0, 0, $location, 0, false, 'L');

        $directory = $this->getLocalDirectory("priceticket_luxe");

        if (!file_exists($directory)) {
            mkdir('priceticket_luxe', 0777, true);
        }


        $FileNAme = 'pdf-' . $Barcode . '-' . $location . '.pdf';

        $file = $directory . '/' . $FileNAme;

        $pdf->Output($file, 'F');

//        Mage::helper('ipconfigurations')->movepdftolocal($FileNAme, "", "priceticket_luxe",false,false); commneted

    }
}