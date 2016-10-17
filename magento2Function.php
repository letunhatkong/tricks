<!-- Re-INDEX -->
sudo php bin/magento indexer:reindex

<!-- Re-DEPLOY -->
sudo php bin/magento setup:static-content:deploy
sudo chmod -R 777 var/ pub/
M2 có 2 chế độ symlink and copy. deploy content => copy.
Nếu M2 ko tự động tạo symlink khi F5 page thì có nghĩa là cần xem lại file .htaccess của thư mục pub/static
Del pub/static/* && var/view_preprocessed
https://github.com/magento/magento2/blob/develop/pub/static/.htaccess

<!-- CLEAR CACHE -->
Go to magento folder and type: php bin/magento cache:clean
sudo php bin/magento cache:clean
sudo php bin/magento cache:fluid
Edit code trong html 
sudo rm -rf var/cache/ var/page_cache/ var/view_preprocessed/ var/composer_home/  pub/static/* var/generation/ var/di

<!-- Show mode -->
sudo php bin/magento deploy:mode:show
sudo php bin/magento deploy:mode:set developer
sudo php bin/magento deploy:mode:set production

<!-- Upgrade -->
sudo php bin/magento setup:upgrade
sudo rm -rf var/di/
sudo php bin/magento setup:di:compile 

<!-- List Module Status -->
sudo php bin/magento module:status

<!-- Remove Module -->
Remove all related tabel, and remove module in setup_module table
DROP TABLE IF EXISTS magefan_blog_category, magefan_blog_category_store;
DELETE FROM setup_module WHERE module = "Magefan_Blog";
sudo php bin/magento module:uninstall Magentostudy_News --backup-code --backup-media --backup-db --clear-static-content
<!-- Disable Module -->
sudo php bin/magento module:disable Lexim_Catalog
<!-- Enable Module -->
php bin/magento module:enable Sahy_Banner --clear-static-content

<!-- Update Magento 2 -->
The second way is with composer:
In composer.json change this line
"magento/product-community-edition": "2.0.0",
Also you should change the line 5 as well "version": "2.0.0", to keep it in sync.
to whatever version you want, and then run:
composer update
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy

To upgrade to 2.0.7 using the command line: cd /var/www/html/magento2
php bin/magento cache:disable
composer require magento/product-community-edition 2.0.7 --no-update
composer update
php bin/magento setup:upgrade
php bin/magento cache:enable


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

<!-- GET URL IN CMS PAGE -->
{{media url=test/logo.svg}}

GET CURRENT URL
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

<!-- GET CURRENT CATEGORY -->
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

<?php // Get Latest Products
$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$productCollection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
$collection = $productCollection->create()
    ->addAttributeToSelect('*')
    ->addFieldToFilter("status", "1")
    ->addFieldToFilter("type_id", array('neq' => 'configurable'))
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

<!-- Layout -->
<!-- Banner home -->
<block class="Magento\Framework\View\Element\Template" name="top.slider.home" template="Magento_Theme::html/top-slider-home.phtml"/>
<!--   <move element="top.slider.home" before="main.content" destination="page.wrapper"/> -->
<!-- Call Home Block: {{block class="Magento\Framework\View\Element\Template" name="home_static" template="Magento_Theme::html/home.phtml"}} -->
<!-- # Banner home -->
<!-- Remove footer -->
<referenceContainer name="footer" remove="true" />
<!-- Move Copyright -->
<move element="copyright" destination="before.body.end" />
<!-- Remove WishList, Register in top link -->
<referenceBlock name="wish-list-link" remove="true" />
<referenceBlock name="register-link" remove="true" />
<!-- Move minicart -->
<move element="minicart" destination="top.links" after="-"/>
<!-- Add custom link to top.link -->
<referenceBlock name="top.links">
    <block class="Magento\Framework\View\Element\Html\Link\Current" name="about-us">
        <arguments>
            <argument name="label" xsi:type="string">About Us</argument>
            <argument name="path" xsi:type="string">about-us</argument>
        </arguments>
    </block>
</referenceBlock>
<!-- Move Breadcrumb -->
    <move element="breadcrumbs" destination="content" before="-"/>
<!-- JS -->
<block class="Magento\Framework\View\Element\Text" name="carousel">
    <arguments>
        <argument name="text" xsi:type="string"><![CDATA[<script type="text/javascript" src="/pub/js/carousel.js"></script>]]></argument>
    </arguments>
</block>
<move element="carousel" destination="before.body.end"/>

<!-- # Layout -->

<!-- Remove unwanted account navigation links -->
<referenceBlock name="customer-account-navigation-downloadable-products-link" remove="true"/>
<!-- subscription link -->
<referenceBlock name="customer-account-navigation-newsletter-subscriptions-link" remove="true"/>
<!-- billing agreement link -->
<referenceBlock name="customer-account-navigation-billing-agreements-link" remove="true"/>
<!-- product review link -->
<referenceBlock name="customer-account-navigation-product-reviews-link" remove="true"/>
<!-- my credit card link -->
<referenceBlock name="customer-account-navigation-my-credit-cards" remove="true"/>
<!-- account link -->
<referenceBlock name="customer-account-navigation-account-link" remove="true"/>
<!-- account edit link -->
<referenceBlock name="customer-account-navigation-account-edit-link" remove="true"/>
<!-- address link -->
<referenceBlock name="customer-account-navigation-address-link" remove="true"/>
<!-- orders link -->
<referenceBlock name="customer-account-navigation-orders-link" remove="true"/>
<!-- wish list link -->
<referenceBlock name="customer-account-navigation-wish-list-link" remove="true"/>

<!-- Remove links on Top Links - following code -->
<referenceBlock name="register-link" remove="true" />           <!--for Create Account Link-->
<referenceBlock name="authorization-link" remove="true" />      <!--for Sign In Link  -->
<referenceBlock name="wish-list-link" remove="true" />          <!--for WishList Link-->
<referenceBlock name="my-account-link" remove="true" />         <!--for My Account Link-->

<!-- Set Tax Shipping price -->
Flat Rate: Set Highest shippping price for international.
Marketing -> Promotions -> Cart Price Rules: Set discount by countries 

<!-- Install MAGENTO 2 -->
Link step by step: http://devdocs.magento.com/guides/v2.0/install-gde/bk-install-guide.html

1/ Install apache2, mySQL, PHP
apache -v (2.2 or 2.4)
sudo apt-get install apache2 zip
php -v (>= 5.5.22 or 5.6.x)


2/ PHP extension
bc-math, curl, gd, ImageMagick 6.3.7 (or later) or both
intl, mbstring, mcrypt, mhash, openssl, PDO/MySQL
SimpleXML, soap, xml, xsl, zip

INSTALL PHP 5.6.x
sudo apt-get -y update
sudo add-apt-repository ppa:ondrej/php5-5.6
sudo apt-get -y update
sudo apt-get -y install php5 php5-common php5-mcrypt php5-curl php5-cli php5-mysql php5-gd php5-intl php5-xsl libapache2-mod-php5 libcurl3 php5-imagick zip
sudo apt-get install mysql-server-5.6 php5-mysql

curl --version (>= 7.34)

3/ Config memory_limit of PHP
Ubuntu: /etc/php5/cli/php.ini and /etc/php5/apache2/php.ini
/etc/php/7.0/cli/php.ini and /etc/php/7.0/apache2/php.ini

memory_limit = 768M or 1G
post_max_size 100M
always_populate_raw_post_data = -1 (removed on php7)
upload_max_filesize 100M

mod_rewrite module must be enabled: sudo a2enmod rewrite
Save your changes and Restart Apache: sudo service apache2 restart

4/ Create vitrual machine
sudo nano /etc/apache2/sites-available/magento.conf                    

<Directory /var/www/html>
    Allow from all
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
</Directory>

<VirtualHost siva.vn:80>
    ServerAdmin siva@localhost
    DocumentRoot /var/www/html/siva
    ServerName siva.vn

    <Directory /var/www/html/siva>
        Allow from all
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/siva-log.log
    CustomLog ${APACHE_LOG_DIR}/siva-access.log combined

</VirtualHost>
# sudo nano /etc/hosts
127.0.1.1 siva.vn


sudo a2ensite magento.conf 
sudo service apache2 reload
sudo service apache2 restart

5/ Create DATABASE
mysql -u root -p
CREATE DATABASE hlm1 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci; CREATE USER hlm1@localhost IDENTIFIED BY 'Password$1'; GRANT ALL PRIVILEGES ON hlm1.* TO hlm1@localhost IDENTIFIED BY 'Password$1'; FLUSH PRIVILEGES; 


6/ Autoload error - Vendor autoload is not found. Please run 'composer install' under application root directory.
sudo apt-get update
sudo apt-get install git

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
cd to magento folder
sudo composer install

Invalid credentials for 'https://repo.magento.com/archives/magento/composer/magento-composer-1.0.2.0.zip', aborting.
Create file ~/.composer/auth.json (Example)

{
    "http-basic": {
        "repo.magento.com": {
            "username": "a3615e9926c4efa2db13cb1182c5ca33",
            "password": "a3d1fd61a5e2346438b8c054e372c322"
        }
    },
    "github-oauth": {
        "github.com": "84926ebcfe71c881633e5b9de172877802s6aaf4"
    }
}

cd to magento folder
sudo composer install

7/ Permission folders and files
cd to /var/www/html and type: sudo chown -R www-data:www-data magento-folder
cd to magento folder and type:

sudo find . -type d -exec chmod 775 {} \; && sudo find . -type f -exec chmod 664 {} \; && sudo chmod -R 777 bin/magento var/ pub/ app/etc/ app/design/frontend/; 

sudo find . -type d -exec chmod 777 {} \; && sudo find . -type f -exec chmod 664 {} \; && sudo chmod -R 777 bin/magento var/ pub/ app/etc/ app/design/frontend/; 

sudo find . -type d -exec chmod 777 {} \; && sudo find . -type f -exec chmod 664 {} \; && sudo chmod -R 777 bin/magento var/ pub/ app/etc/ app/design/frontend/ .idea/ .git/ .gitignore;


8/ Go to http://localhost/project-name/setup/

<!-- ===== UPDATE URL BASE ==== -->
UPDATE core_config_data SET value = 'http://54.213.49.253/' WHERE config_id = 2; 

<!-- ===== BUG MAGENTO ====== -->
Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock'

Solution:
sudo service mysql stop
sudo /etc/init.d/apparmor reload
sudo service mysql start 

sudo /etc/init.d/apache2 restart
sudo /etd/init.d/apparmor.d reload
sudo service mysql start 

<!-- ===== BUG MAGENTO ====== -->
[Exception] Missing write permissions to the following directories: pub/static folder - MAGENTO 2

How to debug and fix:
1) setup/src/Magento/Setup/Model/FilePermissions.php
2) Goto line 143 in method checkRecursiveDirectories
3) Add the lines var_dump($subDirectory);var_dump($subDirectory->isWritable()); return false;
4) Re run bin/magento setup:upgrade

Now you'll see what is really wrong, and you can fix it. Personally i remove everything in pub/static, this will be auto generated content so you should not be worried about that.

<!-- BUG:  Use of undefined constant MCRYPT_BLOWFISH - assumed 'MCRYPT_BLOWFISH' -->
Bug này có thể là do mcrypt chưa được cài đặt trên php5 hoặc php7
Check if mcrypt module is there: php -m | grep mcrypt 
If not then install mcrypt: sudo apt-get install php7.0-mcrypt
Then enable the module: phpenmod mcrypt
Restart apache2

<!-- Override Minicart M2 -->
Edit Your_theme/Magento_Checkout/web/template/minicart/content.html
sudo php bin/magento setup:static-content:deploy
Clear Cache.
Change another theme and clear cache.
Re-choose current theme and clear cache.


<!-- Edit EAV Product Attributes -->
1. Find Id of Attribute: eav_attribute 19
2. Check id in: catalog_eav_attribute

<!-- Magento: Join, filter, select and sort attributes, fields and tables  -->
<!-- http://blog.chapagain.com.np/magento-join-filter-select-and-sort-attributes-fields-and-tables/ -->
addAttributeToFilter: adds WHERE clause on $attribute specified by $condition

<?php
/**
* Add attribute filter to collection
*
* If $attribute is an array will add OR condition with following format:
* array(
* array(‘attribute’=>’firstname’, ‘like’=>’test%’),
* array(‘attribute’=>’lastname’, ‘like’=>’test%’),
* )
*
* @see self::_getConditionSql for $condition
* @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
* @param null|string|array $condition
* @param string $operator
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
addAttributeToFilter($attribute, $condition=null, $joinType=’inner’)
addAttributeToSelect: gets the value for $attribute in the SELECT clause; specify * to get all attributes (i.e. to execute SELECT *)

/**
* Add attribute to entities in collection
*
* If $attribute==’*’ select all attributes
*
* @param array|string|integer|Mage_Core_Model_Config_Element $attribute
* @param false|string $joinType flag for joining attribute
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
addAttributeToSelect($attribute, $joinType=false)

/* If an array is passed but no attribute code specified, it will be interpreted as a group of OR conditions that will be processed in the same way.
If no attribute code is specified, it defaults to eq.*/

$collection = Mage::getModel('catalog/product')->getCollection();
// select all attributes
$collection->addAttributeToSelect('*');
// select specific attributes
$collection->addAttributeToSelect(array('name', 'url_key', 'type_id'));
// select only those items whose status = 1
$collection->addAttributeToFilter('status', 1);
// alternative to select only those items whose status = 1
$collection->addAttributeToFilter('status', array('eq' => 1));
// using LIKE statement
$collection->addAttributeToFilter('sku', array('like' => '%CH%'));
// using IN statement,
// i.e. selecting only those items whose ID fall in the given array
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
        ),
    ));
$collection = Mage::getModel('catalog/product')->getCollection(); 
// select all attributes
$collection->addAttributeToSelect('*'); 
// select specific attributes
$collection->addAttributeToSelect(array('name', 'url_key', 'type_id')); 
// select only those items whose status = 1
$collection->addAttributeToFilter('status', 1); 
// alternative to select only those items whose status = 1
$collection->addAttributeToFilter('status', array('eq' => 1)); 
// using LIKE statement
$collection->addAttributeToFilter('sku', array('like' => '%CH%')); 
// using IN statement,
// i.e. selecting only those items whose ID fall in the given array
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
        ),
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
*
* @param mixed $attribute
* @param mixed $condition
*/
addFieldToFilter($attribute, $condition=null)

addAttributeToSort: adds ORDER BY clause on $attribute

/**
* Add attribute to sort order
*
* @param string $attribute
* @param string $dir
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
addAttributeToSort($attribute, $dir=’asc’)


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
* groupByAttribute: adds $attribute to GROUP BY clause
* Groups results by specified attribute
*
* @param string|array $attribute
*/
groupByAttribute($attribute)

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

Using joinAttribute and joinTable
In the code below, all order invoice items are selected, i.e. all products that have been invoiced.
joinTable is used to join sales_order_entity table to fetch increment_id and store_id of the invoice for each product.
joinAttribute is used to fetch order_id, product_name, and store_id.
joinTable is used again to fetch the order status of each invoice item.

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
setPage: sets LIMIT clause by specifying page number (one-indexed) and number of records per page; equivalent to calling setCurPage($pageNum) and setPageSize($pageSize)
* Set collection page start and records to show
*
* @param integer $pageNum
* @param integer $pageSize
* @return Mage_Eav_Model_Entity_Collection_Abstract
*/
setPage($pageNum, $pageSize)

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
*
* @return array
*/
exportToArray()


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
setOrder($attribute, $dir=’desc’)



