<!-- Re-INDEX -->
sudo bin/magento indexer:reindex

<!-- Re-DEPLOY -->
sudo bin/magento setup:static-content:deploy

<!-- CLEAR CACHE -->
Go to magento folder and type: php bin/magento cache:clean

<!-- Upgrade -->
sudo bin/magento setup:upgrade
sudo rm -rf var/di/
sudo bin/magento setup:di:compile 

<!-- List Module Status -->
sudo bin/magento module:status

<!-- Remove Module -->
Remove all related tabel, and remove module in setup_module table
sudo bin/magento module:uninstall Magentostudy_News --backup-code --backup-media --backup-db --clear-static-content
<!-- Disable Module -->
sudo bin/magento module:disable Magentostudy_News

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


<?php // Get Latest Products
$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$productCollection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
$collection = $productCollection->create()
    ->addAttributeToSelect('*')
    ->addFieldToFilter("status", "1")
    ->setOrder('created_at', 'DESC')
    ->setPageSize(12) // Limit product
    ->load();

$latestAr = [];
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
    array_push($latestAr, $item);
}
$newProSize = count($latestAr);
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

<!-- JS -->
<block class="Magento\Framework\View\Element\Text" name="carousel">
    <arguments>
        <argument name="text" xsi:type="string"><![CDATA[<script type="text/javascript" src="/pub/js/carousel.js"></script>]]></argument>
    </arguments>
</block>
<move element="carousel" destination="before.body.end"/>

<!-- # Layout -->


Install MAGENTO 2
Link step by step: http://devdocs.magento.com/guides/v2.0/install-gde/bk-install-guide.html

1/ Install apache2, mySQL, PHP
apache -v (2.2 or 2.4)
sudo apt-get install apache2
php -v (>= 5.5.22 or 5.6.x)


2/ PHP extension
bc-math, curl, gd, ImageMagick 6.3.7 (or later) or both
intl, mbstring, mcrypt, mhash, openssl, PDO/MySQL
SimpleXML, soap, xml, xsl, zip

INSTALL PHP 5.6.x
sudo apt-get -y update
sudo add-apt-repository ppa:ondrej/php5-5.6
sudo apt-get -y update
sudo apt-get -y install php5 php5-common php5-mcrypt php5-curl php5-cli php5-mysql php5-gd php5-intl php5-xsl libapache2-mod-php5 libcurl3 
sudo apt-get install mysql-server-5.6 php5-mysql

INSTALL imagemagick
sudo apt-get install php5-imagick

curl --version (>= 7.34)

3/ Config memory_limit of PHP
Ubuntu: /etc/php5/cli/php.ini and /etc/php5/apache2/php.ini
Change memory_limit to:
memory_limit = 768M or more for normal operation
memory_limit = 2G or more for testing
upload_max_filesize
post_max_size
mod_rewrite module must be enabled: a2enmod rewrite
always_populate_raw_post_data = -1
Save your changes and Restart Apache: sudo service apache2 restart

4/ Create vitrual machine
sudo nano /etc/apache2/sites-available/magento.conf                    

< Directory /var/www/html/magento >
        Allow from all
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
< /Directory >

sudo a2ensite magento.conf 
sudo service apache2 reload
sudo service apache2 restart

5/ Create DATABASE
mysql -u root -p
CREATE DATABASE dowell;CREATE USER dwd@localhost IDENTIFIED BY 'Password$1';GRANT ALL PRIVILEGES ON dowell.* TO dwd@localhost IDENTIFIED BY 'Password$1';FLUSH PRIVILEGES;
exit

6/ Autoload error - Vendor autoload is not found. Please run 'composer install' under application root directory.
sudo apt-get update
sudo apt-get install curl php5-cli git

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

sudo find . -type d -exec chmod 775 {} \; && sudo find . -type f -exec chmod 664 {} \; && sudo chmod u+x bin/magento; 
sudo chmod -R 777 var/ pub/ app/etc/

sudo chmod -R 777 var/ pub/ app/etc/ app/design/frontend/ .idea/ .git/ .gitignore 


8/ Go to http://localhost/project-name/setup/



UPDATE mytable    SET column1 = value1, column2 = value2 WHERE key_value = some_value;



