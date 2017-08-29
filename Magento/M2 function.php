<!-- Get store emails -->
<?php
$this->scopeConfig->getValue(
    'trans_email/ident_general/email',
    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
);
// trans_email/ident_general/email
// trans_email/ident_sales/email
// trans_email/ident_support/email 
// trans_email/ident_custom1/email 
// trans_email/ident_custom2/email 
?>

<!-- add folder dir custom DirectoryList -->
<?php // edit file /vendor/magento/framework/App/Filesystem/DirectoryList.php
const MEDIA_ORDER_PDF = 'order_pdf'; // Samuel Kong
public static function getDefaultConfig()
{
    $result = [
        // ....
        self::MEDIA_ORDER_PDF => [parent::PATH => 'pub/media/order_pdf'] // Samuel Kong
    ];
    return parent::getDefaultConfig() + $result;
}
// use in custom module
use Magento\Framework\App\Filesystem\DirectoryList;
DirectoryList::MEDIA_ORDER_PDF;
?>



<!-- ======= CATALOG - PRODUCT DETAIL PAGE - CATEGORIES PAGE ======= -->

Magento 2 có 3 loại price:
Group price: cho phép bạn đặt giá khuyến mãi cho 1 vài nhóm user.
Special price: đặt giá khuyến mãi trong một khoảng thời gian nào đó.
Tier price: đặt một khuyến mãi về giá theo qty của sản phẩm cho một nhóm user.

<!-- config product attributes -->
List product attribute -> in tables: eav_attribute + catalog_eav_attribute

<!-- Remove Please select in option select -->
/Magento/Catalog/Block/Product/View/Options/Type/Select.php
<?php
if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN) {
    $select->setName('options[' . $_option->getid() . ']');
    //->addOption('', __('-- Please Select --'));
} 
?>

<!-- Add image url for category object -->

<?php 
<preference for="Magento\Catalog\Model\ResourceModel\Category" type="Lexim\CategoryImage\Model\Category" />
public function getChildrenCategories($category)
{
    $collection = $category->getCollection();
    $collection->addAttributeToSelect(
    ...
    )->addAttributeToSelect(
        'image'
    )
    ...
    return $collection;
}    
?>

<!-- Enable Review -->
\vendor\magento\module-review\Block\Product\ReviewRenderer.php 
\vendor\magento\module-review\Block\View.php
View file: Magento_Catalog/../detail.phtml
<referenceBlock name="product.info.details">
    <block class="Magento\Review\Block\Product\Review" name="reviews.tab" as="reviews" template="Magento_Review::review.phtml" group="detailed_info">
        <block class="Magento\Review\Block\Form" name="product.review.form" as="review_form">
            <container name="product.review.form.fields.before" as="form_fields_before" label="Review Form Fields Before"/>
        </block>
    </block>
</referenceBlock>
ADMIN PAGE:
Go to Catalog Product -> reviews and ratings -> Customer Reviews: Write review for product
Stores -> Rating : Add new rating
Marketing -> Reviews: Manage all review

<!-- Get price custom option of product -->
<?php
$selectId = 100; $optionId = 132;
$customOptions = $_product->getOptions();
$opsObj = $customOptions[$selectId]->getValues();
$price = $opsObj[$optionId]->getPrice();
?>

<!-- Config custom option of product -->
catalog_product_option table

<!-- Get current category -->
<?php
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
echo $category->getName();
?>

<!-- Get Category ID by urk_key -->
<?php 
$categoryFactory = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
$category = $categoryFactory->create()
    ->addAttributeToFilter('url_key','promotional')
    ->addAttributeToSelect('*')
    ->getFirstItem();
$promotionId = $category->getId() ? $category->getId() : 0;
echo $promotionId;
?>

<!-- Get Parent Product from child product -->
<?php 
$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$parentProduct = $_objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable');

$parentId = $parentProduct->getParentIdsByChild($_product->getId());
$parentId = (isset($parentId[0])) ? 0:$parentId[0];

?>

<!-- Filter category by id  -->
<?php
$catId = 1;
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$categoryFac =  $objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
$categories = $categoryFac->create()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('entity_id', $catId)
    ->addAttributeToFilter('is_active', '0')
    ->getFirstItem();
?>


<?php // Get all category (not root category) 
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$categoryFac =  $objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
$categories = $categoryFac->create()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter('parent_id', array('gt' => '1'))
    ->addAttributeToFilter('is_active', '1');
?>

<?php // Get Latest Products
$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$productCollection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
$collection = $productCollection->create()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter("entity_id", ["eq" => "20"])
    ->addFieldToFilter("status", "1")
    ->addFieldToFilter("type_id", array('neq' => 'configurable'))
    ->addAttributeToFilter('visibility', array("neq" => "1"))
    ->setOrder('created_at', 'DESC')
    ->setPageSize(12) // Limit product
    ->load();

foreach ($collection as $product){
    $item = array(
        'getId' => $product->getId(),
        'getName' => $product->getName(),
        'getPrice' => floatval($product->getPrice()),
        'priceString' => "$".number_format($product->getPrice(), 2),
        'getStatus' => $product->getStatus(),
        'thumbnail' => $mediaUrl .'catalog/product'. $product->getThumbnail(),
        'getProductUrl' => $product->getProductUrl(),
    );
}
?>

<?php // Get All Products by Category
$categoryFactory = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
$promotionCategory = $categoryFactory->create()
    ->addAttributeToFilter('url_key','promotional')
    ->addAttributeToSelect('*')
    ->getFirstItem();

$promotionCollection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
$promo = $promotionCollection->create()
    ->addAttributeToSelect('*')
    ->addCategoryFilter($promotionCategory)
    ->addAttributeToFilter('status', '1')
    ->setOrder('created_at', 'DESC')->load();
?>

<!-- Get Bundled Items By Bundle Product -->
<?php 
    $bundled_product = new Mage_Catalog_Model_Product();        
    $bundled_product->load(YOUR_BUNDLED_PRODUCT_ID); 
 
    $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
        $bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
    );
 
    $bundled_items = array();
    foreach($selectionCollection as $option) {
        $bundled_items[] = $option->product_id;
    }
 
    print_r($bundled_items); 
?>

<!-- Edit EAV Product Attributes -->
1. Find Id of Attribute: eav_attribute 19
2. Check id in: catalog_eav_attribute


<!-- ========== CONFIG ADMIN ========== -->
<!-- render view cell column in grid  -->
/vendor/magento/module-ui/view/base/web/js/grid/columns/actions.js
/vendor/magento/module-ui/view/base/web/templates/grid/cells/actions.html


<!-- Config Email -->
+ List email: General -> Store Email Address
+ Customer -> Customer config -> Create new account options:
Default Email: smtp.gmail.com

<!-- Config contact email -->
Store / Config / General / Contact

<!-- ======= ORDER - CART - CHECKOUT ======= -->

<!-- Get Order items by order id -->
<?php 
$order = $_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
$orders = $order->getAllItems();
?>

<!-- Get custom options of item in order cart -->
<?php
$items = $_order->getItems();
$firstItem = array_values($items)[0];  
$options = $firstItem->getProductOptions();                  
foreach ($options['options'] as $itemOption) {
    echo $itemOption['label'] . $itemOption['value'];  
}    

?>

<!-- Show/hide Free delivery by customer field -->
magento/module-quote/Model/Address.php
<?php
public function getGroupedAllShippingRates()
{      
    $rates = [];
    $customer = $this->getQuote()->getCustomer();
    $freeStatus = $customer->getCustomAttribute('free_delivery_kong')->getValue();

    foreach ($this->getShippingRatesCollection() as $rate) {
        if (!$rate->isDeleted() && $this->_carrierFactory->get($rate->getCarrier())) {

            if ($rate->getCarrier() != 'freeshipping' || ($rate->getCarrier() == 'freeshipping' && $freeStatus == '1') ) {
                if (!isset($rates[$rate->getCarrier()])) {
                    $rates[$rate->getCarrier()] = [];
                }

                $rates[$rate->getCarrier()][] = $rate;
                $rates[$rate->getCarrier()][0]->carrier_sort_order = $this->_carrierFactory->get(
                    $rate->getCarrier()
                )->getSortOrder();
            }
        }
    }
    uasort($rates, [$this, '_sortRates']);
    return $rates;
}
?>

<!-- Change order status -->
Thêm vài dòng code trong module-sales/view/adminhtml/ui_component/sales_order_grid.xml
<listingToolbar name="listing_top">
    <massaction name="listing_massaction">
        <action name="cs_pending">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="type" xsi:type="string">cs_pending</item>
                    <item name="label" xsi:type="string" translate="true">Order Receive</item>
                    <item name="url" xsi:type="url" path="sales/order/massOrderStatus">
                        <param name="status">pending</param>
                    </item>
                    <item name="confirm" xsi:type="array">
                        <item name="title" xsi:type="string" translate="true">Order Receive</item>
                        <item name="message" xsi:type="string" translate="true">Are you sure you want to change status of selected items?</item>
                    </item>
                </item>
            </argument>
        </action>
    </massaction>
</listingToolbar>

Tạo file MassOrderStatus.php trong module-sales/Controller/Adminhtml/Order (bắt chước MassCancel.php)
<?php
protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');

        $countExecution = 0;
        foreach ($collection->getItems() as $order) {
            $order->setStatus($status);
            $order->save();
            $countExecution++;
        }

        $countDefeat = $collection->count() - $countExecution;
        if ($countDefeat && $countExecution) {
            $this->messageManager->addError(__('%1 order(s) cannot be changed status.', $countDefeat));
        } elseif ($countDefeat) {
            $this->messageManager->addError(__('No order statuses have been changed.'));
        }
        if ($countExecution) {
            $this->messageManager->addSuccess(__('%1 order(s) have been updated status.', $countExecution));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
?>

<!-- Create Custom Order Status -->
On the Admin Panel, Stores > Settings > Order Status.
You cannot remove the order status that is being used.
Find the order status that you want to unassign in the status list.
Under Action column, on the corresponding row, click on the Unassign link.
Right after that, a notification of the assignment will appear at the top of the workplace. Although the order status is unassigned, it is still included in the gird and never deleted.

<!-- Override Minicart M2 -->
Edit Your_theme/Magento_Checkout/web/template/minicart/content.html
sudo php bin/magento setup:static-content:deploy
Clear Cache.
Change another theme and clear cache.
Re-choose current theme and clear cache.

<!-- Remove Wishlist Function -->
Go to admin -> Catalog -> Wishlist -> Enable to No

<!-- Set Tax Shipping price -->
Flat Rate: Set Highest shippping price for international.
Marketing -> Promotions -> Cart Price Rules: Set discount by countries 

<!-- FeDex - Sorry, no quotes are available for this order at this time -->
+ Please ensure that you have selected the API mode as Test.
Stores->Configuration->Sales->Shipping Methods->set the sandbox mode to yes
+ Ensure that your maximum package weight is greater than the product weight.

http://docs.magento.com/m2/ce/user_guide/shipping/fedex.html
http://magento.stackexchange.com/questions/123211/magento2-fedex-shipping-is-not-working
https://www.xadapter.com/2016/06/29/trouble-shooting-magento-2-shipping-extensions/?preview=true

==== Fedex Samuel Kong =====
Developer Test Key:  gmt2uIhRySwp9Qgu
Test Password: sEpMPQrsFYVpwLQSKn9HirDe5
Test Account Number:    510087607 
Test Meter Number:      118766562 
Test    FedEx Office Integrator ID:  123
Test    Client Product ID:   TEST
Test    Client Product Version:  9999

FedEx Freight LTL Testing Information (used in the Freight Shipment Detail):

FedEx Freight LTL Shipper
Account Number: 510087020
Address Line 1: 1202 Chalet Ln
Address Line 2: Do Not Delete - Test Account
City: Harrison
State: AR
Zip: 72601

FedEx Freight LTL Bill To/Third Party
Account Number: 510051408
Address Line 1: 2000 Freight LTL Testing
Address Line 2: Do Not Delete - Test Account
City: Harrison
State: AR
Zip: 72601

Test URL: https://wsbeta.fedex.com:443/web-services

<!-- ======= CUSTOMER - ACCOUNT PAGE ======= -->

<!-- Add custom field for customer -->
File Setup/InstallData.php in Extension
<?php
namespace Ibnab\CustomerPut\Setup;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $used_in_forms[] = "adminhtml_customer";
        $used_in_forms[] = "checkout_register";
        $used_in_forms[] = "customer_account_create";
        $used_in_forms[] = "customer_account_edit";
        $used_in_forms[] = "adminhtml_checkout";

        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "free_delivery_kong");
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "free_delivery_kong", array(
            "type" => "varchar",
            "backend" => "",
            "label" => "Free Delivery Status: Yes = 1, No = 0",
            "input" => "text",
            "source" => "",
            "visible" => true,
            "required" => false,
            "default" => "0",
            "frontend" => "",
            "unique" => false,
            "note" => ""

        ));
        $installer->endSetup();
    }
}
?>

<!-- Override Controller -->
In di.xml of extension
<preference for="Magento\Customer\Controller\Account\CreatePost" type="Lexim\Override\Controller\Account\CreatePost" />
Call public function __construct() in CreatePost extensuon extend from CreatePost core

<!-- Controller -->
In Magento 2 URL’s are constructed this way:
<frontName>/<controler_folder_name>/<controller_class_name>

<!-- Upload file in post controller -->
<?php
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;

$uploader = $this->fileUploaderFactory->create(['fileId' => 'seller_file']); // seller_file is name of input file
$uploader->setAllowedExtensions(['jpg', 'jpeg', 'pdf', 'png']);
$uploader->setAllowRenameFiles(true);
$uploader->setFilesDispersion(false);
$uploader->setAllowCreateFolders(true);

$path = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)
->getAbsolutePath('images/certificate/');

$result = $uploader->save($path);
if ($result) $customer->setCustomAttribute('seller_permit', $this->_url->getBaseUrl() . 'pub/media/images/certificate/' .  $result['file']);
?>


<!-- Add custom Attribute Customer -->
etc/module.xml
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Ibnab_CustomerPut" setup_version="1.0.2">
        <sequence>
            <module name="Magento_Customer"/>
        </sequence>
    </module>
</config>
file Setup/InstallData.php. Xem thêm extension Ibnab_CustomerPut

<!-- Redirect current page after login -->
Admin -> config -> Customer -> Customer Configuration > Login Options > 
Redirect Customer to Account Dashboard after Logging in > No.
** Clear all admin cache, local cache on M2, browser cache.

Controller file
<?php 
public function __construct(
    \Magento\Framework\Url\EncoderInterface $urlEncoder
) {
    $this->urlEncoder = $urlEncoder;
}
public function execute(){
    $referrer = $this->urlEncoder->encode($curUrl);
    $this->_redirect($this->targetUrl, ['referer' => $referrer])->sendResponse();
}
?>

<!-- ======= OTHER ======= -->

<!-- GET BASE URL -->
<?php 
	$_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of \Magento\Framework\App\ObjectManager
    $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
	$currentStore = $storeManager->getStore();
	$baseUrl = $currentStore->getBaseUrl();
	$mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	$linkUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    // Try {{secure_base_url}} in CMS Pages/Static blocks.
?>

<!-- Get logger  -->
<?php
protected $logger;
public function __construct(\Psr\Log\LoggerInterface $logger)
{
    $this->logger = $logger;
    $this->logger->debug("================== Start debug kong ====================");
}
?>


<!-- GET URL IN CMS PAGE -->
{{media url=test/logo.svg}}

<!-- GET CURRENT URL -->
<?php
$urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
$curUrl = explode("?", $urlInterface->getCurrentUrl())[0];
?>

<!-- Call .phtml file  -->
<block class="Magento\Framework\View\Element\Template" name="test_file" template="Magento_Theme::html/test.phtml"/>
{{block class="Magento\Framework\View\Element\Template" name="test_file" template="Magento_Theme::html/latestItems.phtml"}}
<?php include ($block->getTemplateFile('Magento_Theme::html/test.phtml')) ?>
<?php echo $this->getLayout()->createBlock("Magento\Framework\View\Element\Template")->setTemplate("Magento_Theme::html/test.phtml")->toHtml(); ?>
<?php echo $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId('block_identifier')->toHtml();?>
<referenceContainer name="content">
    <block class="Magento\Cms\Block\Block" name="block_identifier">
        <arguments>
            <argument name="block_id" xsi:type="string">block_identifier</argument>
        </arguments>
    </block>
</referenceContainer>



<!-- Get Menu -->
 <?php
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
// Get Menu
$menu = $objectManager->get('\Magento\Theme\Block\Html\Topmenu');
$_menu = $menu->getHtml('level-top', 'submenu');
?>

<ul id="leftHomeMenu"><?= $_menu ?></ul>



<!-- Init Javascript -->
<script type="text/javascript">
    require(['jquery', 'jquery/ui'], function($){
        $(document).ready(function($) {
            $(window).load(function() {
                ....
            });
        });
    });
</script>



<!-- Newsletter -->
https://www.fastcomet.com/tutorials/magento2/newsletters
http://blog.belvg.com/newsletters-in-magento-2-0.html


<!-- Magento: Join, filter, select and sort attributes, fields and tables  -->
http://blog.chapagain.com.np/magento-join-filter-select-and-sort-attributes-fields-and-tables/


<?php
/**
* Add attribute filter to collection
*
* If $attribute is an array will add OR condition with following format:
* array(
    array('attribute'=>'firstname', 'like'=>'test%'),
     array('attribute'=>'lastname', 'like'=>'test%'),
* )
*
*/
addAttributeToFilter($attribute, $condition=null, $joinType='inner');

// addAttributeToSelect: gets the value for $attribute in the SELECT clause; specify * to get all attributes (i.e. to execute SELECT *)
addAttributeToSelect($attribute, $joinType=false);

/* If an array is passed but no attribute code specified, it will be interpreted as a group of OR conditions that will be processed in the same way.
If no attribute code is specified, it defaults to eq.*/

$collection = Mage::getModel('catalog/product')->getCollection();
$collection->addAttributeToSelect('*');
$collection->addAttributeToSelect(array('name', 'url_key', 'type_id'));
$collection->addAttributeToFilter('status', 1);
// alternative to select only those items whose status = 1
$collection->addAttributeToFilter('status', array('eq' => 1));
// using LIKE statement
$collection->addAttributeToFilter('sku', array('like' => '%CH%'));
// using IN statement
$collection->addAttributeToFilter('id', array('in' => array(1, 14, 51, 52)));
// selecting only those items whose ID is greater than the given value
$collection->addAttributeToFilter('id', array('gt' => 5));
// select by date range
$collection->addAttributeToFilter('date_field', array(
    'from' => '10 September 2010',
    'to' => '21 September 2010',
    'date' => true, // specifies conversion of comparison values
));
// Add OR condition:
$collection->addAttributeToFilter(array(
    array(
        'attribute' => 'field_name',
        'in'        => array(1, 2, 3),
    ),
    array(
        'attribute' => 'date_field',
        'from'      => '2010-09-10',
    )
));

/**
Below is the full filter condition codes with attribute code and its sql equivalent
eq  :   =
neq :   !=
like :  LIKE
nlike : NOT LIKE
in  :   IN ()
nin :   NOT IN ()
is  :   IS
notnull :   IS NOT NULL
null :  IS NULL
moreq : >=
gt  :   >
lt  :   <   gteq :  >=
lteq :  <=  finset :    FIND_IN_SET()   from :  >=  (for use with dates)
to  :   <=  (for use with dates) date : optional flag for use with from/to to specify that comparison value should first be converted to a date datetime :  optional flag for use with from/to to specify that comparison value should first be converted to a datetime
*/

/**
* addFieldToFilter: alias for addAttributeToFilter(). This filters the database table fields.
* Wrapper for compatibility with Varien_Data_Collection_Db
*/
addFieldToFilter($attribute, $condition=null)


// addAttributeToSort: adds ORDER BY clause on $attribute
addAttributeToSort($attribute, $dir='asc')

/**
* groupByAttribute: adds $attribute to GROUP BY clause
* Groups results by specified attribute
*
* @param string|array $attribute
*/
groupByAttribute($attribute)

/**
setPage: sets LIMIT clause by specifying page number (one-indexed) and number of records per page; equivalent to calling setCurPage($pageNum) and setPageSize($pageSize)
* Set collection page start and records to show
*
* @param integer $pageNum
* @param integer $pageSize
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
setPage($pageNum, $pageSize)

/**
setOrder: alias for addAttributeToSort() q.v., identical except that it can accept array of attributes, and default $dir is desc
* Set sorting order
*
* $attribute can also be an array of attributes
*
* @param string|array $attribute
* @param string $dir
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
setOrder($attribute, $dir='desc')


/**
* Add attribute expression (SUM, COUNT, etc)
*
* addExpressionAttributeToSelect: adds SQL expression $expression, using $alias, to SELECT clause (typically containing aggregate functions such as SUM(), COUNT()); when $attribute specifies a single attribute as a string, $expression can reference the attribute as simply {{attribute}}, but when passing an array of attributes, each attribute must be referenced in $expression by the name of the specific attribute;
*
* Example: (‘sub_total’, ‘SUM({{attribute}})’, ‘revenue’)
* Example: (‘sub_total’, ‘SUM({{revenue}})’, ‘revenue’)
*
* For some functions like SUM use groupByAttribute.
*
* @param string $alias
* @param string $expression
* @param string $attribute
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
addExpressionAttributeToSelect($alias, $expression, $attribute)

/**
* 
joinAttribute: joins another entity and adds attribute from joined entity, using $alias, to SELECT clause.
Here are the parameters for joinAttribute function:-
$alias = selected field name. You can keep it’s name whatever you want.
$attribute = joined entity type code and attribute code = entity_type_code/attribute_code
entity_type_code is present in eav_entity_type table
attribute_code is present in eav_attribute table
attribute_code is attribute of the corresponding entity you want to select out.
$bind = attribute code of the main entity to link to the joined entity.
$filter = primary key for the joined entity (entity_id default)
*
* Add attribute from joined entity to select
*
* Examples:
* (‘billing_firstname’, ‘customer_address/firstname’, ‘default_billing’)
* (‘billing_lastname’, ‘customer_address/lastname’, ‘default_billing’)
* (‘shipping_lastname’, ‘customer_address/lastname’, ‘default_billing’)
* (‘shipping_postalcode’, ‘customer_address/postalcode’, ‘default_shipping’)
* (‘shipping_city’, $cityAttribute, ‘default_shipping’)
*
* Developer is encouraged to use existing instances of attributes and entities
* After first use of string entity name it will be cached in the collection
*
* @todo connect between joined attributes of same entity
* @param string $alias alias for the joined attribute
* @param string|Mage_Eav_Model_Entity_Attribute_Abstract $attribute
* @param string $bind attribute of the main entity to link with joined $filter
* @param string $filter primary key for the joined entity (entity_id default)
* @param string $joinType inner|left
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
joinAttribute($alias, $attribute, $bind, $filter=null, $joinType=’inner’, $storeId=null)


/**
* joinTable: joins table $table
Here are the parameters of the function joinTable:-
$table = table name to join
$bind = ( parent_key = foreign_key )
$fields = array of fields to select
$cond = where condition
$joinType = join type
*
* @param string|array $table
* @param string $bind
* @param string|array $fields
* @param null|array $cond
* @param string $joinType
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
joinTable($table, $bind, $fields=null, $cond=null, $joinType=’inner’)

/**
Using joinAttribute and joinTable
In the code below, all order invoice items are selected, i.e. all products that have been invoiced.
joinTable is used to join sales_order_entity table to fetch increment_id and store_id of the invoice for each product.
joinAttribute is used to fetch order_id, product_name, and store_id.
joinTable is used again to fetch the order status of each invoice item.
*/
$collection = Mage::getModel('sales/order_invoice_item')
->getCollection()
->joinTable('sales_order_entity', 'entity_id=parent_id', array('invoice_id'=>'increment_id', 'store_id' => 'store_id'), null , 'left')
->joinAttribute('order_id', 'invoice/order_id', 'parent_id', null, 'left')
->joinAttribute('product_name', 'invoice_item/name', 'entity_id', null, 'left')
->joinAttribute('store_id', 'invoice/store_id', 'parent_id', null, 'left')
->joinTable('sales_order', 'entity_id=order_id', array('order_status'=>'status'), null , 'left')
;

$collection = Mage::getModel('sales/order_invoice_item')
->getCollection()
->joinTable('sales_order_entity', 'entity_id=parent_id', array('invoice_id'=>'increment_id', 'store_id' => 'store_id'), null , 'left')
->joinAttribute('order_id', 'invoice/order_id', 'parent_id', null, 'left')
->joinAttribute('product_name', 'invoice_item/name', 'entity_id', null, 'left')
->joinAttribute('store_id', 'invoice/store_id', 'parent_id', null, 'left')
->joinTable('sales_order', 'entity_id=order_id', array('order_status'=>'status'), null , 'left')
;


/**
joinField: joins regular table field using an attribute as foreign key
* Join regular table field and use an attribute as fk
*
* Examples:
* (‘country_name’, ‘directory/country_name’, ‘name’, ‘country_id=shipping_country’, “{{table}}.language_code=’en'”, ‘left’)
*
* @param string $alias ‘country_name’
* @param string $table ‘directory/country_name’
* @param string $field ‘name’
* @param string $bind ‘PK(country_id)=FK(shipping_country_id)’
* @param string|array $cond “{{table}}.language_code=’en'” OR array(‘language_code’=>’en’)
* @param string $joinType ‘left’
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
joinField($alias, $table, $field, $bind, $cond=null, $joinType=’inner’)

/**
removeAttributeToSelect: removes $attribute from SELECT clause; specify null to remove all attributes
* Remove an attribute from selection list
*
* @param string $attribute
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
removeAttributeToSelect($attribute=null)

/**
importFromArray: imports 2D array into collection as objects
* Import 2D array into collection as objects
*
* If the imported items already exist, update the data for existing objects
*
* @param array $arr
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
importFromArray($arr)


/**
exportToArray: returns collection data as a 2D array
* Get collection data as a 2D array
* @return array
*/
exportToArray()
?>


<!-- API Instagram API -->
0. Create a new app
https://www.instagram.com/developer/clients/manage/
CLIENT INFO SIVA
CLIENT ID   98164b1728f148c0ae005e3493cd2a2b
CLIENT SECRET   793acb2a607b4ff19788a55dfa218406
WEBSITE URL http://siva.vn
REDIRECT URI    http://siva.vn
SUPPORT EMAIL   letunhatkong@gmail.com
letunhatkong: 3309870573
access_token siva.vn: 3309870573.98164b1.d872979b5c61476ea4994fcd5d316ea5

1. Find User id
http://www.otzberg.net/iguserid/index.php

2. Get code 
https://api.instagram.com/oauth/authorize/?client_id=CLIENT-ID&redirect_uri=REDIRECT-URI&response_type=code

3. Get access_token
type in linux cmd:
curl -F 'client_id=98164b1728f148c0ae005e3493cd2a2b' \
-F 'client_secret=793acb2a607b4ff19788a55dfa218406' \
-F 'grant_type=authorization_code' \
-F 'redirect_uri=http://52.39.199.186/' \
-F 'code=5c2f8f00733648a084382579902f5b20' \
https://api.instagram.com/oauth/access_token


http://52.39.199.186/?code=5c2f8f00733648a084382579902f5b20
http://siva.vn/?code=9dcf2108c3ac43129f4029bcc9696d39

object(stdClass)#1122 (15) { 
    ["attribution"]=> NULL 
    ["tags"]=> array(0) { } 
    ["type"]=> string(5) "image" 
    ["location"]=> NULL 
    ["comments"]=> object(stdClass)#1247 (1) { ["count"]=> int(0) } 
    ["filter"]=> string(6) "Normal" 
    ["created_time"]=> string(10) "1465980278" 
    ["link"]=> string(40) "https://www.instagram.com/p/BGqzuBLCPta/" 
    ["likes"]=> object(stdClass)#1166 (1) { ["count"]=> int(0) } 
    ["images"]=> object(stdClass)#1108 (3) { 
        ["low_resolution"]=> object(stdClass)#1147 (3) { 
            ["url"]=> string(151) "https://scontent.cdninstagram.com/t51.2885-15/s320x320/e35/13392647_1579044049062434_1611277269_n.jpg?ig_cache_key=MTI3MzA1NzMwNzQxMjQ2MjQyNg%3D%3D.2.l" 
            ["width"]=> int(320) 
            ["height"]=> int(320) 
        } 
        ["thumbnail"]=> object(stdClass)#1219 (3) { 
            ["url"]=> string(163) "https://scontent.cdninstagram.com/t51.2885-15/s150x150/e35/c0.95.768.768/13385621_148838945521951_483117555_n.jpg?ig_cache_key=MTI3MzA1NzMwNzQxMjQ2MjQyNg%3D%3D.2.c" 
            ["width"]=> int(150) 
            ["height"]=> int(150) 
        } 
        ["standard_resolution"]=> object(stdClass)#1126 (3) { 
            ["url"]=> string(158) "https://scontent.cdninstagram.com/t51.2885-15/s640x640/sh0.08/e35/13392647_1579044049062434_1611277269_n.jpg?ig_cache_key=MTI3MzA1NzMwNzQxMjQ2MjQyNg%3D%3D.2.l" 
            ["width"]=> int(640) 
            ["height"]=> int(640) 
        } 
    } 
    ["users_in_photo"]=> array(0) { } 
    ["caption"]=> object(stdClass)#1114 (4) { 
        ["created_time"]=> string(10) "1465980278" 
        ["text"]=> string(9) "White LOL" 
        ["from"]=> object(stdClass)#1135 (4) { 
            ["username"]=> string(12) "letunhatkong" 
            ["profile_picture"]=> string(116) "https://igcdn-photos-f-a.akamaihd.net/hphotos-ak-xat1/t51.2885-19/s150x150/13408825_850610411711365_1887981232_a.jpg" 
            ["id"]=> string(10) "3309870573" 
            ["full_name"]=> string(11) "Samuel Kong" 
        } 
        ["id"]=> string(17) "17857743217007909" 
    } 
    ["user_has_liked"]=> bool(false) 
    ["id"]=> string(30) "1273057307412462426_3309870573" 
    ["user"]=> object(stdClass)#1192 (4) { 
        ["username"]=> string(12) "letunhatkong" 
        ["profile_picture"]=> string(116) "https://igcdn-photos-f-a.akamaihd.net/hphotos-ak-xat1/t51.2885-19/s150x150/13408825_850610411711365_1887981232_a.jpg" ["id"]=> string(10) "3309870573" 
        ["full_name"]=> string(11) "Samuel Kong" 
    } 
}


<!-- EVENT OBSERVER -->
search eventManager->dispatch( in module of vendor
<p
File    Event name
app/code/Magento/Authorizenet/Controller/Directpost/Payment/Place.php   checkout_directpost_placeOrder
app/code/Magento/Backend/Block/System/Store/Edit/AbstractForm.php   adminhtml_store_edit_form_prepare_form
app/code/Magento/Backend/Block/Template.php adminhtml_block_html_before
app/code/Magento/Backend/Block/Widget/Grid.php  backend_block_widget_grid_prepare_grid_before
app/code/Magento/Backend/Console/Command/CacheCleanCommand.php  adminhtml_cache_flush_system
app/code/Magento/Backend/Console/Command/CacheFlushCommand.php  adminhtml_cache_flush_all
app/code/Magento/Backend/Controller/Adminhtml/Cache/CleanImages.php clean_catalog_images_cache_after
app/code/Magento/Backend/Controller/Adminhtml/Cache/CleanMedia.php  clean_media_cache_after
app/code/Magento/Backend/Controller/Adminhtml/Cache/CleanStaticFiles.php    clean_static_files_cache_after
app/code/Magento/Backend/Controller/Adminhtml/Cache/FlushAll.php    adminhtml_cache_flush_all
app/code/Magento/Backend/Controller/Adminhtml/Cache/FlushSystem.php adminhtml_cache_flush_system
app/code/Magento/Backend/Controller/Adminhtml/System/Design/Save.php    theme_save_after
app/code/Magento/Backend/Controller/Adminhtml/System/Store/DeleteStorePost.php  store_delete
app/code/Magento/Backend/Controller/Adminhtml/System/Store/Save.php store_group_save
app/code/Magento/Backend/Controller/Adminhtml/System/Store/Save.php NO_MATCH
app/code/Magento/Backend/Model/Auth.php backend_auth_user_login_success
app/code/Magento/Backend/Model/Auth.php backend_auth_user_login_failed
app/code/Magento/Backend/Model/Auth.php backend_auth_user_login_failed
app/code/Magento/Bundle/Block/Catalog/Product/View/Type/Bundle.php  catalog_product_option_price_configuration_after
app/code/Magento/Bundle/Model/Product/Price.php prepare_catalog_product_collection_prices
app/code/Magento/Bundle/Model/Product/Price.php catalog_product_get_final_price
app/code/Magento/Bundle/Model/Product/Price.php catalog_product_get_final_price
app/code/Magento/Bundle/Model/ResourceModel/Indexer/Price.php   catalog_product_prepare_index_select
app/code/Magento/Bundle/Pricing/Price/BundleSelectionPrice.php  catalog_product_get_final_price
app/code/Magento/Catalog/Block/Adminhtml/Category/Tab/Attributes.php    adminhtml_catalog_category_edit_prepare_form
app/code/Magento/Catalog/Block/Adminhtml/Category/Tabs.php  adminhtml_catalog_category_tabs
app/code/Magento/Catalog/Block/Adminhtml/Category/Tree.php  adminhtml_catalog_category_tree_is_moveable
app/code/Magento/Catalog/Block/Adminhtml/Category/Tree.php  adminhtml_catalog_category_tree_can_add_root_category
app/code/Magento/Catalog/Block/Adminhtml/Category/Tree.php  adminhtml_catalog_category_tree_can_add_sub_category
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Edit/Tab/Advanced.php    product_attribute_form_build
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Edit/Tab/Front.php   product_attribute_form_build_front_tab
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Edit/Tab/Front.php   adminhtml_catalog_product_attribute_edit_frontend_prepare_form
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Edit/Tab/Main.php    adminhtml_product_attribute_types
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Edit/Tab/Main.php    product_attribute_form_build_main_tab
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Grid.php product_attribute_grid_build
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/NewAttribute/Product/Attributes.php  adminhtml_catalog_product_edit_prepare_form
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/NewAttribute/Product/Attributes.php  adminhtml_catalog_product_edit_element_types
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Set/Main.php adminhtml_catalog_product_attribute_set_main_html_before
app/code/Magento/Catalog/Block/Adminhtml/Product/Attribute/Set/Toolbar/Main.php adminhtml_catalog_product_attribute_set_toolbar_main_html_before
app/code/Magento/Catalog/Block/Adminhtml/Product/Edit/Action/Attribute/Tab/Attributes.php   adminhtml_catalog_product_form_prepare_excluded_field_list
app/code/Magento/Catalog/Block/Adminhtml/Product/Edit/Tab/Attributes/Create.php adminhtml_catalog_product_edit_tab_attributes_create_html_before
app/code/Magento/Catalog/Block/Adminhtml/Product/Edit/Tab/Attributes.php    adminhtml_catalog_product_edit_prepare_form
app/code/Magento/Catalog/Block/Adminhtml/Product/Edit/Tab/Attributes.php    adminhtml_catalog_product_edit_element_types
app/code/Magento/Catalog/Block/Adminhtml/Product/Grid.php   adminhtml_catalog_product_grid_prepare_massaction
app/code/Magento/Catalog/Block/Adminhtml/Product/Helper/Form/Gallery/Content.php    catalog_product_gallery_prepare_layout
app/code/Magento/Catalog/Block/Product/AbstractProduct.php  catalog_block_product_status_display
app/code/Magento/Catalog/Block/Product/ListProduct.php  catalog_block_product_list_collection
app/code/Magento/Catalog/Block/Product/ProductList/Upsell.php   catalog_product_upsell
app/code/Magento/Catalog/Block/Product/View/Options.php catalog_product_option_price_configuration_after
app/code/Magento/Catalog/Block/Product/View.php catalog_product_view_config
app/code/Magento/Catalog/Block/Rss/Category.php rss_catalog_category_xml_callback
app/code/Magento/Catalog/Block/Rss/Product/NewProducts.php  rss_catalog_new_xml_callback
app/code/Magento/Catalog/Block/Rss/Product/Special.php  rss_catalog_special_xml_callback
app/code/Magento/Catalog/Block/ShortcutButtons.php  shortcut_buttons_container
app/code/Magento/Catalog/Controller/Adminhtml/Category/Delete.php   catalog_controller_category_delete
app/code/Magento/Catalog/Controller/Adminhtml/Category/Edit.php category_prepare_ajax_response
app/code/Magento/Catalog/Controller/Adminhtml/Category/Save.php catalog_category_prepare_save
app/code/Magento/Catalog/Controller/Adminhtml/Product/Action/Attribute/Save.php catalog_product_to_website_change
app/code/Magento/Catalog/Controller/Adminhtml/Product/Edit.php  catalog_product_edit_action
app/code/Magento/Catalog/Controller/Adminhtml/Product/Gallery/Upload.php    catalog_product_gallery_upload_image_after
app/code/Magento/Catalog/Controller/Adminhtml/Product/NewAction.php catalog_product_new_action
app/code/Magento/Catalog/Controller/Adminhtml/Product/Save.php  controller_action_catalog_product_save_entity_after
app/code/Magento/Catalog/Controller/Category/View.php   catalog_controller_category_init_after
app/code/Magento/Catalog/Controller/Product/Compare/Add.php catalog_product_compare_add_product
app/code/Magento/Catalog/Controller/Product/Compare/Remove.php  catalog_product_compare_remove_product
app/code/Magento/Catalog/Helper/Product/View.php    catalog_controller_product_view
app/code/Magento/Catalog/Helper/Product.php catalog_controller_product_init_before
app/code/Magento/Catalog/Helper/Product.php catalog_controller_product_init_after
app/code/Magento/Catalog/Model/Category.php _move_before
app/code/Magento/Catalog/Model/Category.php _move_after
app/code/Magento/Catalog/Model/Category.php category_move
app/code/Magento/Catalog/Model/Product/Action.php   catalog_product_attribute_update_before
app/code/Magento/Catalog/Model/Product/Attribute/Source/Inputtype.php   adminhtml_product_attribute_types
app/code/Magento/Catalog/Model/Product/Type/AbstractType.php    NO_MATCH
app/code/Magento/Catalog/Model/Product/Type/Price.php   catalog_product_get_final_price
app/code/Magento/Catalog/Model/Product.php  _validate_before
app/code/Magento/Catalog/Model/Product.php  _validate_after
app/code/Magento/Catalog/Model/Product.php  catalog_product_is_salable_before
app/code/Magento/Catalog/Model/Product.php  catalog_product_is_salable_after
app/code/Magento/Catalog/Model/ResourceModel/Category/Collection.php    _load_before
app/code/Magento/Catalog/Model/ResourceModel/Category/Collection.php    _load_after
app/code/Magento/Catalog/Model/ResourceModel/Category/Collection.php    _add_is_active_filter
app/code/Magento/Catalog/Model/ResourceModel/Category/Flat/Collection.php   _load_before
app/code/Magento/Catalog/Model/ResourceModel/Category/Flat/Collection.php   _load_after
app/code/Magento/Catalog/Model/ResourceModel/Category/Flat/Collection.php   _add_is_active_filter
app/code/Magento/Catalog/Model/ResourceModel/Category/Flat.php  catalog_category_tree_init_inactive_category_ids
app/code/Magento/Catalog/Model/ResourceModel/Category/Flat.php  catalog_category_flat_loadnodes_before
app/code/Magento/Catalog/Model/ResourceModel/Category/Tree.php  catalog_category_tree_init_inactive_category_ids
app/code/Magento/Catalog/Model/ResourceModel/Category.php   catalog_category_change_products
app/code/Magento/Catalog/Model/ResourceModel/Product/Collection.php catalog_prepare_price_select
app/code/Magento/Catalog/Model/ResourceModel/Product/Collection.php catalog_product_collection_load_after
app/code/Magento/Catalog/Model/ResourceModel/Product/Collection.php catalog_product_collection_before_add_count_to_categories
app/code/Magento/Catalog/Model/ResourceModel/Product/Collection.php catalog_product_collection_apply_limitations_after
app/code/Magento/Catalog/Model/ResourceModel/Product/Compare/Item/Collection.php    catalog_product_compare_item_collection_clear
app/code/Magento/Catalog/Model/ResourceModel/Product/Indexer/Eav/AbstractEav.php    prepare_catalog_product_index_select
app/code/Magento/Catalog/Model/ResourceModel/Product/Indexer/Eav/Decimal.php    prepare_catalog_product_index_select
app/code/Magento/Catalog/Model/ResourceModel/Product/Indexer/Eav/Source.php prepare_catalog_product_index_select
app/code/Magento/Catalog/Model/ResourceModel/Product/Indexer/Eav/Source.php prepare_catalog_product_index_select
app/code/Magento/Catalog/Model/ResourceModel/Product/Indexer/Price/DefaultPrice.php prepare_catalog_product_index_select
app/code/Magento/Catalog/Model/ResourceModel/Product.php    catalog_product_delete_after_done
app/code/Magento/Catalog/Model/Rss/Product/NotifyStock.php  rss_catalog_notify_stock_collection_select
app/code/Magento/Catalog/Plugin/Model/Product/Action/UpdateAttributesFlushCache.php clean_cache_by_tags
app/code/Magento/CatalogImportExport/Model/Import/Product.php   catalog_product_import_bunch_delete_after
app/code/Magento/CatalogImportExport/Model/Import/Product.php   catalog_product_import_finish_before
app/code/Magento/CatalogImportExport/Model/Import/Product.php   catalog_product_import_bunch_save_after
app/code/Magento/CatalogInventory/Model/Indexer/Stock/AbstractAction.php    clean_cache_by_tags
app/code/Magento/CatalogRule/Block/Adminhtml/Promo/Catalog/Edit/Tab/Main.php    adminhtml_promo_catalog_edit_tab_main_prepare_form
app/code/Magento/CatalogRule/Controller/Adminhtml/Promo/Catalog/Save.php    adminhtml_controller_catalogrule_prepare_save
app/code/Magento/CatalogRule/Model/Indexer/AbstractIndexer.php  clean_cache_by_tags
app/code/Magento/CatalogSearch/Model/Indexer/Fulltext/Action/DataProvider.php   catelogsearch_searchable_attributes_load_after
app/code/Magento/CatalogSearch/Model/Indexer/Fulltext/Action/Full.php   catelogsearch_searchable_attributes_load_after
app/code/Magento/CatalogSearch/Model/ResourceModel/Fulltext.php catalogsearch_reset_search_result
app/code/Magento/Checkout/Block/QuoteShortcutButtons.php    shortcut_buttons_container
app/code/Magento/Checkout/Controller/Cart/Add.php   checkout_cart_add_product_complete
app/code/Magento/Checkout/Controller/Cart/UpdateItemOptions.php checkout_cart_update_item_complete
app/code/Magento/Checkout/Controller/Onepage/SaveOrder.php  checkout_controller_onepage_saveOrder
app/code/Magento/Checkout/Controller/Onepage/Success.php    checkout_onepage_controller_success_action
app/code/Magento/Checkout/Helper/Data.php   checkout_allow_guest
app/code/Magento/Checkout/Model/Cart.php    checkout_cart_product_add_after
app/code/Magento/Checkout/Model/Cart.php    checkout_cart_update_items_before
app/code/Magento/Checkout/Model/Cart.php    checkout_cart_update_items_after
app/code/Magento/Checkout/Model/Cart.php    checkout_cart_save_before
app/code/Magento/Checkout/Model/Cart.php    checkout_cart_save_after
app/code/Magento/Checkout/Model/Cart.php    checkout_cart_product_update_after
app/code/Magento/Checkout/Model/Session.php custom_quote_process
app/code/Magento/Checkout/Model/Session.php checkout_quote_init
app/code/Magento/Checkout/Model/Session.php load_customer_quote_before
app/code/Magento/Checkout/Model/Session.php checkout_quote_destroy
app/code/Magento/Checkout/Model/Session.php restore_quote
app/code/Magento/Checkout/Model/Type/Onepage.php    checkout_type_onepage_save_order_after
app/code/Magento/Checkout/Model/Type/Onepage.php    checkout_submit_all_after
app/code/Magento/Cms/Block/Adminhtml/Page/Edit/Tab/Content.php  adminhtml_cms_page_edit_tab_content_prepare_form
app/code/Magento/Cms/Block/Adminhtml/Page/Edit/Tab/Design.php   adminhtml_cms_page_edit_tab_design_prepare_form
app/code/Magento/Cms/Block/Adminhtml/Page/Edit/Tab/Main.php adminhtml_cms_page_edit_tab_main_prepare_form
app/code/Magento/Cms/Block/Adminhtml/Page/Edit/Tab/Meta.php adminhtml_cms_page_edit_tab_meta_prepare_form
app/code/Magento/Cms/Controller/Adminhtml/Page/Delete.php   adminhtml_cmspage_on_delete
app/code/Magento/Cms/Controller/Adminhtml/Page/Delete.php   adminhtml_cmspage_on_delete
app/code/Magento/Cms/Controller/Adminhtml/Page/Save.php cms_page_prepare_save
app/code/Magento/Cms/Controller/Router.php  cms_controller_router_match_before
app/code/Magento/Cms/Helper/Page.php    cms_page_render
app/code/Magento/Cms/Helper/Wysiwyg/Images.php  cms_wysiwyg_images_static_urls_allowed
app/code/Magento/Config/Block/System/Config/Form/Fieldset/Modules/DisableOutput.php adminhtml_system_config_advanced_disableoutput_render_before
app/code/Magento/Config/Model/Config.php    NO_MATCH
app/code/Magento/ConfigurableProduct/Model/Product/Validator/Plugin.php catalog_product_validate_variations_before
app/code/Magento/Cookie/Controller/Index/NoCookies.php  controller_action_nocookies
app/code/Magento/CurrencySymbol/Model/System/Currencysymbol.php admin_system_config_changed_section_currency_before_reinit
app/code/Magento/CurrencySymbol/Model/System/Currencysymbol.php admin_system_config_changed_section_currency
app/code/Magento/Customer/Block/Adminhtml/Edit/Tab/Carts.php    adminhtml_block_html_before
app/code/Magento/Customer/Controller/Account/CreatePost.php customer_register_success
app/code/Magento/Customer/Controller/Adminhtml/Index/Save.php   adminhtml_customer_prepare_save
app/code/Magento/Customer/Controller/Adminhtml/Index/Save.php   adminhtml_customer_save_after
app/code/Magento/Customer/Model/AccountManagement.php   customer_customer_authenticated
app/code/Magento/Customer/Model/AccountManagement.php   customer_data_object_login
app/code/Magento/Customer/Model/Address/AbstractAddress.php customer_address_format
app/code/Magento/Customer/Model/Customer.php    customer_customer_authenticated
app/code/Magento/Customer/Model/Customer.php    customer_validate
app/code/Magento/Customer/Model/ResourceModel/CustomerRepository.php    customer_save_after_data_object
app/code/Magento/Customer/Model/Session.php customer_session_init
app/code/Magento/Customer/Model/Session.php customer_login
app/code/Magento/Customer/Model/Session.php customer_data_object_login
app/code/Magento/Customer/Model/Session.php customer_login
app/code/Magento/Customer/Model/Session.php customer_data_object_login
app/code/Magento/Customer/Model/Session.php customer_logout
app/code/Magento/Customer/Model/Visitor.php visitor_init
app/code/Magento/Customer/Model/Visitor.php visitor_activity_save
app/code/Magento/Eav/Block/Adminhtml/Attribute/Edit/Main/AbstractMain.php   adminhtml_block_eav_attribute_edit_form_init
app/code/Magento/Eav/Model/Entity/Collection/AbstractCollection.php eav_collection_abstract_load_before
app/code/Magento/GiftMessage/Block/Message/Inline.php   gift_options_prepare_items
app/code/Magento/GroupedProduct/Model/ResourceModel/Product/Indexer/Price/Grouped.php   catalog_product_prepare_index_select
app/code/Magento/Indexer/Model/Processor/InvalidateCache.php    clean_cache_after_reindex
app/code/Magento/Multishipping/Controller/Checkout/ShippingPost.php checkout_controller_multishipping_shipping_post
app/code/Magento/Multishipping/Controller/Checkout/Success.php  multishipping_checkout_controller_success_action
app/code/Magento/Multishipping/Model/Checkout/Type/Multishipping.php    checkout_type_multishipping_set_shipping_items
app/code/Magento/Multishipping/Model/Checkout/Type/Multishipping.php    checkout_type_multishipping_create_orders_single
app/code/Magento/Multishipping/Model/Checkout/Type/Multishipping.php    checkout_submit_all_after
app/code/Magento/Multishipping/Model/Checkout/Type/Multishipping.php    checkout_multishipping_refund_all
app/code/Magento/PageCache/Model/Cache/Type.php adminhtml_cache_refresh_type
app/code/Magento/PageCache/Model/Layout/DepersonalizePlugin.php depersonalize_clear_session
app/code/Magento/Payment/Block/Form/Cc.php  payment_form_block_to_html_before
app/code/Magento/Payment/Model/Cart.php payment_cart_collect_items_and_amounts
app/code/Magento/Payment/Model/Method/AbstractMethod.php    payment_method_is_active
app/code/Magento/Payment/Model/Method/Adapter.php   payment_method_is_active
app/code/Magento/Payment/Model/Method/Adapter.php   payment_method_assign_data_
app/code/Magento/Paypal/Controller/Express/AbstractExpress/PlaceOrder.php   paypal_express_place_order_success
app/code/Magento/Persistent/Controller/Index/UnsetCookie.php    persistent_session_expired
app/code/Magento/Persistent/Observer/CheckExpirePersistentQuoteObserver.php persistent_session_expired
app/code/Magento/Quote/Model/Cart/Totals/ItemConverter.php  items_additional_data
app/code/Magento/Quote/Model/Quote/Address/ToOrder.php  sales_convert_quote_to_order
app/code/Magento/Quote/Model/Quote/Item.php sales_quote_item_qty_set_after
app/code/Magento/Quote/Model/Quote/Item.php sales_quote_item_set_product
app/code/Magento/Quote/Model/Quote/Payment.php  _import_data_before
app/code/Magento/Quote/Model/Quote/TotalsCollector.php  sales_quote_collect_totals_before
app/code/Magento/Quote/Model/Quote/TotalsCollector.php  sales_quote_collect_totals_after
app/code/Magento/Quote/Model/Quote/TotalsCollector.php  sales_quote_address_collect_totals_before
app/code/Magento/Quote/Model/Quote/TotalsCollector.php  sales_quote_address_collect_totals_after
app/code/Magento/Quote/Model/Quote.php  sales_quote_remove_item
app/code/Magento/Quote/Model/Quote.php  sales_quote_add_item
app/code/Magento/Quote/Model/Quote.php  sales_quote_product_add_after
app/code/Magento/Quote/Model/Quote.php  _merge_before
app/code/Magento/Quote/Model/Quote.php  _merge_after
app/code/Magento/Quote/Model/QuoteManagement.php    checkout_submit_before
app/code/Magento/Quote/Model/QuoteManagement.php    checkout_submit_all_after
app/code/Magento/Quote/Model/QuoteManagement.php    sales_model_service_quote_submit_before
app/code/Magento/Quote/Model/QuoteManagement.php    sales_model_service_quote_submit_success
app/code/Magento/Quote/Model/QuoteManagement.php    sales_model_service_quote_submit_failure
app/code/Magento/Quote/Model/ResourceModel/Quote/Address/Collection.php _load_after
app/code/Magento/Quote/Model/ResourceModel/Quote/Item/Collection.php    prepare_catalog_product_collection_prices
app/code/Magento/Quote/Model/ResourceModel/Quote/Item/Collection.php    sales_quote_item_collection_products_after_load
app/code/Magento/Reports/Block/Adminhtml/Grid.php   adminhtml_widget_grid_filter_collection
app/code/Magento/Reports/Model/ResourceModel/Order/Collection.php   sales_prepare_amount_expression
app/code/Magento/Review/Controller/Product.php  review_controller_product_init_before
app/code/Magento/Review/Controller/Product.php  review_controller_product_init
app/code/Magento/Review/Controller/Product.php  review_controller_product_init_after
app/code/Magento/Review/Model/ResourceModel/Rating/Collection.php   rating_rating_collection_load_before
app/code/Magento/Review/Model/ResourceModel/Review/Collection.php   review_review_collection_load_before
app/code/Magento/Review/Model/Rss.php   rss_catalog_review_collection_select
app/code/Magento/Sales/Block/Adminhtml/Reorder/Renderer/Action.php  adminhtml_customer_orders_add_action_renderer
app/code/Magento/Sales/Controller/Adminhtml/Order/AddressSave.php   admin_sales_order_address_update
app/code/Magento/Sales/Controller/Adminhtml/Order/Create.php    adminhtml_sales_order_create_process_data_before
app/code/Magento/Sales/Controller/Adminhtml/Order/Create.php    adminhtml_sales_order_create_process_data
app/code/Magento/Sales/Controller/Adminhtml/Order/CreditmemoLoader.php  adminhtml_sales_order_creditmemo_register_before
app/code/Magento/Sales/Model/AdminOrder/Create.php  sales_convert_order_to_quote
app/code/Magento/Sales/Model/AdminOrder/Create.php  sales_convert_order_item_to_quote_item
app/code/Magento/Sales/Model/AdminOrder/Create.php  checkout_submit_all_after
app/code/Magento/Sales/Model/Config/Backend/Email/AsyncSending.php  sales_email_general_async_sending
app/code/Magento/Sales/Model/Config/Backend/Grid/AsyncIndexing.php  dev_grid_async_indexing
app/code/Magento/Sales/Model/Order/Address/Renderer.php customer_address_format
app/code/Magento/Sales/Model/Order/Email/Sender/CreditmemoCommentSender.php email_creditmemo_comment_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/CreditmemoSender.php    email_creditmemo_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/InvoiceCommentSender.php    email_invoice_comment_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/InvoiceSender.php   email_invoice_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/OrderCommentSender.php  email_order_comment_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/OrderSender.php email_order_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/ShipmentCommentSender.php   email_shipment_comment_set_template_vars_before
app/code/Magento/Sales/Model/Order/Email/Sender/ShipmentSender.php  email_shipment_set_template_vars_before
app/code/Magento/Sales/Model/Order/Invoice.php  sales_order_invoice_pay
app/code/Magento/Sales/Model/Order/Invoice.php  sales_order_invoice_cancel
app/code/Magento/Sales/Model/Order/Invoice.php  sales_order_invoice_register
app/code/Magento/Sales/Model/Order/Item.php sales_order_item_cancel
app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php  sales_order_payment_capture
app/code/Magento/Sales/Model/Order/Payment/Transaction.php  _html_txn_id
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_place_start
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_place_end
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_pay
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_cancel_invoice
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_void
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_refund
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_cancel_creditmemo
app/code/Magento/Sales/Model/Order/Payment.php  sales_order_payment_cancel
app/code/Magento/Sales/Model/Order/Status.php   sales_order_status_unassign
app/code/Magento/Sales/Model/Order.php  sales_order_place_before
app/code/Magento/Sales/Model/Order.php  sales_order_place_after
app/code/Magento/Sales/Model/Order.php  order_cancel_after
app/code/Magento/Sales/Model/ResourceModel/Attribute.php    _save_attribute_before
app/code/Magento/Sales/Model/ResourceModel/Attribute.php    _save_attribute_after
app/code/Magento/Sales/Model/ResourceModel/Order/Address/Collection.php _load_after
app/code/Magento/Sales/Model/ResourceModel/Order/Collection/AbstractCollection.php  _set_sales_order
app/code/Magento/Sales/Model/ResourceModel/Sale/Collection.php  sales_sale_collection_query_before
app/code/Magento/Sales/Model/Rss/NewOrder.php   rss_order_new_collection_select
app/code/Magento/Sales/Model/Service/CreditmemoService.php  sales_order_creditmemo_cancel
app/code/Magento/Sales/Model/Service/CreditmemoService.php  sales_order_creditmemo_refund
app/code/Magento/Sales/Model/Service/OrderService.php   sales_order_state_change_before
app/code/Magento/SalesRule/Block/Adminhtml/Promo/Quote/Edit/Tab/Actions.php adminhtml_block_salesrule_actions_prepareform
app/code/Magento/SalesRule/Block/Adminhtml/Promo/Quote/Edit/Tab/Coupons/Form.php    adminhtml_promo_quote_edit_tab_coupons_form_prepare_form
app/code/Magento/SalesRule/Block/Adminhtml/Promo/Quote/Edit/Tab/Main.php    adminhtml_promo_quote_edit_tab_main_prepare_form
app/code/Magento/SalesRule/Block/Adminhtml/Promo/Widget/Chooser.php adminhtml_block_promo_widget_chooser_prepare_collection
app/code/Magento/SalesRule/Controller/Adminhtml/Promo/Quote/Save.php    adminhtml_controller_salesrule_prepare_save
app/code/Magento/SalesRule/Model/Quote/Discount.php sales_quote_address_discount_item
app/code/Magento/SalesRule/Model/Quote/Discount.php sales_quote_address_discount_item
app/code/Magento/SalesRule/Model/Rule/Condition/Combine.php salesrule_rule_condition_combine
app/code/Magento/SalesRule/Model/Rule.php   salesrule_rule_get_coupon_types
app/code/Magento/SalesRule/Model/RulesApplier.php   salesrule_validator_process
app/code/Magento/Search/Controller/Adminhtml/Term/Report.php    on_view_report
app/code/Magento/SendFriend/Controller/Product/Send.php sendfriend_product
app/code/Magento/Store/Model/Address/Renderer.php   store_address_format
app/code/Magento/Swatches/Controller/Adminhtml/Iframe/Show.php  swatch_gallery_upload_image_after
app/code/Magento/Tax/Controller/Adminhtml/Tax/IgnoreTaxNotification.php adminhtml_cache_refresh_type
app/code/Magento/Tax/Model/Calculation/Rate.php tax_settings_change_after
app/code/Magento/Tax/Model/Calculation/Rate.php tax_settings_change_after
app/code/Magento/Tax/Model/Calculation/Rate.php tax_settings_change_after
app/code/Magento/Tax/Model/Calculation/Rule.php tax_settings_change_after
app/code/Magento/Tax/Model/Calculation/Rule.php tax_settings_change_after
app/code/Magento/Tax/Model/Calculation.php  tax_rate_data_fetch
app/code/Magento/Theme/Block/Html/Topmenu.php   page_block_html_topmenu_gethtml_before
app/code/Magento/Theme/Block/Html/Topmenu.php   page_block_html_topmenu_gethtml_after
app/code/Magento/Theme/Model/Config.php assign_theme_to_stores_after
app/code/Magento/Theme/Observer/CheckThemeIsAssignedObserver.php    assigned_theme_changed
app/code/Magento/Theme/Setup/InstallData.php    theme_registration_from_filesystem
app/code/Magento/User/Block/Role.php    permissions_role_html_before
app/code/Magento/User/Controller/Adminhtml/User/Role/SaveRole.php   admin_permissions_role_prepare_save
app/code/Magento/User/Model/User.php    admin_user_authenticate_before
app/code/Magento/User/Model/User.php    admin_user_authenticate_after
app/code/Magento/Wishlist/Block/Customer/Wishlist/Item/Options.php  product_option_renderer_init
app/code/Magento/Wishlist/Controller/Index/Add.php  wishlist_add_product
app/code/Magento/Wishlist/Controller/Index/Send.php wishlist_share
app/code/Magento/Wishlist/Controller/Index/UpdateItemOptions.php    wishlist_update_item
app/code/Magento/Wishlist/Helper/Data.php   wishlist_items_renewed
app/code/Magento/Wishlist/Model/ResourceModel/Item/Collection.php   wishlist_item_collection_products_after_load
app/code/Magento/Wishlist/Model/Rss/Wishlist.php    rss_wishlist_xml_callback
app/code/Magento/Wishlist/Model/Wishlist.php    wishlist_add_item
app/code/Magento/Wishlist/Model/Wishlist.php    wishlist_product_add_after
lib/internal/Magento/Framework/App/Action/Action.php    controller_action_predispatch
lib/internal/Magento/Framework/App/Action/Action.php    controller_action_predispatch_
lib/internal/Magento/Framework/App/Action/Action.php    controller_action_predispatch_
lib/internal/Magento/Framework/App/Action/Action.php    controller_action_postdispatch_
lib/internal/Magento/Framework/App/Action/Action.php    controller_action_postdispatch_
lib/internal/Magento/Framework/App/Action/Action.php    controller_action_postdispatch
lib/internal/Magento/Framework/App/Cron.php default
lib/internal/Magento/Framework/App/FrontController.php  NO_MATCH
lib/internal/Magento/Framework/App/Http.php NO_MATCH
lib/internal/Magento/Framework/App/Http.php controller_front_send_response_before
lib/internal/Magento/Framework/App/View.php controller_action_layout_render_before
lib/internal/Magento/Framework/App/View.php controller_action_layout_render_before_
lib/internal/Magento/Framework/Controller/Noroute/Index.php controller_action_noroute
lib/internal/Magento/Framework/Data/AbstractSearchResult.php    abstract_search_result_load_before
lib/internal/Magento/Framework/Data/AbstractSearchResult.php    _load_before
lib/internal/Magento/Framework/Data/AbstractSearchResult.php    abstract_search_result_load_after
lib/internal/Magento/Framework/Data/AbstractSearchResult.php    _load_after
lib/internal/Magento/Framework/DataObject/Copy.php  NO_MATCH
lib/internal/Magento/Framework/Event/Collection.php NO_MATCH
lib/internal/Magento/Framework/Event/Manager.php    NO_MATCH
lib/internal/Magento/Framework/Event/Observer/Collection.php    NO_MATCH
lib/internal/Magento/Framework/Event.php    NO_MATCH
lib/internal/Magento/Framework/Locale/Currency.php  currency_display_options_forming
lib/internal/Magento/Framework/Message/Manager.php  session_abstract_clear_messages
lib/internal/Magento/Framework/Message/Manager.php  session_abstract_add_message
lib/internal/Magento/Framework/Model/AbstractModel.php  model_load_before
lib/internal/Magento/Framework/Model/AbstractModel.php  _load_before
lib/internal/Magento/Framework/Model/AbstractModel.php  model_load_after
lib/internal/Magento/Framework/Model/AbstractModel.php  _load_after
lib/internal/Magento/Framework/Model/AbstractModel.php  model_save_commit_after
lib/internal/Magento/Framework/Model/AbstractModel.php  _save_commit_after
lib/internal/Magento/Framework/Model/AbstractModel.php  model_save_before
lib/internal/Magento/Framework/Model/AbstractModel.php  _save_before
lib/internal/Magento/Framework/Model/AbstractModel.php  model_save_after
lib/internal/Magento/Framework/Model/AbstractModel.php  clean_cache_by_tags
lib/internal/Magento/Framework/Model/AbstractModel.php  _save_after
lib/internal/Magento/Framework/Model/AbstractModel.php  model_delete_before
lib/internal/Magento/Framework/Model/AbstractModel.php  _delete_before
lib/internal/Magento/Framework/Model/AbstractModel.php  model_delete_after
lib/internal/Magento/Framework/Model/AbstractModel.php  clean_cache_by_tags
lib/internal/Magento/Framework/Model/AbstractModel.php  _delete_after
lib/internal/Magento/Framework/Model/AbstractModel.php  model_delete_commit_after
lib/internal/Magento/Framework/Model/AbstractModel.php  _delete_commit_after
lib/internal/Magento/Framework/Model/AbstractModel.php  _clear
lib/internal/Magento/Framework/Model/ResourceModel/Db/Collection/AbstractCollection.php core_collection_abstract_load_before
lib/internal/Magento/Framework/Model/ResourceModel/Db/Collection/AbstractCollection.php _load_before
lib/internal/Magento/Framework/Model/ResourceModel/Db/Collection/AbstractCollection.php core_collection_abstract_load_after
lib/internal/Magento/Framework/Model/ResourceModel/Db/Collection/AbstractCollection.php _load_after
lib/internal/Magento/Framework/Model/ResourceModel/Db/VersionControl/RelationComposite.php  _process_relation
lib/internal/Magento/Framework/View/Element/AbstractBlock.php   view_block_abstract_to_html_before
lib/internal/Magento/Framework/View/Element/Messages.php    view_message_block_render_grouped_html_after
lib/internal/Magento/Framework/View/Layout/Builder.php  layout_load_before
lib/internal/Magento/Framework/View/Layout/Builder.php  layout_generate_blocks_before
lib/internal/Magento/Framework/View/Layout/Builder.php  layout_generate_blocks_after
lib/internal/Magento/Framework/View/Layout/Generator/Block.php  core_layout_block_create_after
lib/internal/Magento/Framework/View/Layout.php  core_layout_render_element
lib/internal/Magento/Framework/View/Result/Layout.php   layout_render_before
lib/internal/Magento/Framework/View/Result/Layout.php   layout_render_before_
JavaScript Varien Events

File    Event name
lib/web/mage/adminhtml/form.js  formSubmit
lib/web/mage/adminhtml/form.js  address_country_changed
lib/web/mage/adminhtml/grid.js  gridRowClick
lib/web/mage/adminhtml/grid.js  gridRowDblClick
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymceSubmit
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymcePaste
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymceBeforeSetContent
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymceSetContent
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymceSaveContent
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymceChange
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    tinymceExecCommand
lib/web/mage/adminhtml/wysiwyg/tiny_mce/setup.js    open_browser_callback
lib/web/mage/adminhtml/wysiwyg/widget.js    tinymceChange

>


