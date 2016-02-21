<?php
/**
 * Created by PhpStorm.
 * User: jasonehmke
 * Date: 11/3/15
 * Time: 9:38 AM
 */

$juiceId = isset($_GET['juice']) ? $_GET['juice'] : null;
$optionId = isset($_GET['optionId']) ? $_GET['optionId'] : null;
$function = isset($_GET['function']) ? $_GET['function'] : null;
$customerGroup = isset($_GET['custgroup']) ? $_GET['custgroup'] : null;

include_once "../app/Mage.php";  //Adjust for current path to Mage.php
Mage::app()->setCurrentStore(0);

//$productSku = "109";
$product = Mage::getModel('catalog/product');
//$productId = $product->getIdBySku( $productSku );
$product->load($juiceId);

/**
 * In Magento Models or database schema level, the product's Custom Options are
 * executed & maintained as only "options". So, when checking whether any product has
 * Custom Options or not, we should check by using this method "hasOptions()" only.
 */

$jsonOptions = [];

//$productOptions = $product->getProductOptions();
//echo $product->getName();
//
//var_dump($product->getOptions());

//echo Mage::helper('checkout/cart')->getAddUrl($product);


if ($function == "getTypes"){

	$jsonOptionTypes = [];

	$resource = Mage::getSingleton('core/resource');

	/**
	 * Retrieve the read connection
	 */
	$readConnection = $resource->getConnection('core_read');

	$query = "SELECT *
FROM
    catalog_product_option_title
    INNER JOIN catalog_product_option
        ON (catalog_product_option_title.option_id = catalog_product_option.option_id)
        WHERE product_id=$juiceId AND FIND_IN_SET ($customerGroup, catalog_product_option.customer_groups);";

	/**
	 * Execute the query and store the results in $results
	 */
	$results = $readConnection->fetchAll($query);

	/**
	 * Print out the results
	 */


//	echo '<pre>';

	$i=0;

	foreach ($results as $option) {

		$optionType = $option['type'];

		if ($optionType == 'drop_down') {


			$jsonOptionTypes['option'][$i]['id'] = $option['option_id'];
			$jsonOptionTypes['option'][$i]['title'] = $option['title'];

			$i++;
			$jsonOptionTypes['count'] = $i;
		}
		else {
			print_r($option);
		}
	}

//	echo '</pre>';

//	echo "<pre>";
	print_r(json_encode($jsonOptionTypes));
//	echo "</pre>";
}
else if ($function = "getOptions"){

	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
	$query = "SELECT `main_table`.*
, `default_value_title`.`title` AS `default_title`
, IF(store_value_title.title IS NULL, default_value_title.title, store_value_title.title) AS `title`
FROM `catalog_product_option_type_value` AS `main_table`
 INNER JOIN `catalog_product_option_type_title` AS `default_value_title` ON default_value_title.option_type_id = main_table.option_type_id
 LEFT JOIN `catalog_product_option_type_title` AS `store_value_title` ON store_value_title.option_type_id = main_table.option_type_id AND store_value_title.store_id = '1'
 LEFT JOIN `catalog_product_option_type_price` AS `default_value_price` ON `default_value_price`.option_type_id=`main_table`.option_type_id AND `default_value_price`.store_id=0
 LEFT JOIN `catalog_product_option_type_price` AS `store_value_price` ON `store_value_price`.option_type_id=`main_table`.option_type_id AND `store_value_price`.store_id='1'
 WHERE (default_value_title.store_id = 0) AND (option_id IN($optionId))
 ORDER BY sort_order ASC, title ASC";

	$results = $readConnection->fetchAll($query);

//	echo "<pre>";
//	print_r($results);
//	echo "</pre>";

//	echo '<pre>';

	$i = 0;
	foreach ($results as $result) {
		//$optionType = $o->getType();
		//echo 'Type = '.$optionType;

		//echo $result['option_type_id'];

		if ($result['option_id'] ==  $optionId) {
			//$values = $o->getValues();
			//print_r($o->getData());

			$jsonOptions[$i]['id'] = $result['option_id'];
			$jsonOptions[$i]['optionTypeId'] = $result['option_type_id'];
			$jsonOptions[$i]['name'] = $result['title'];
//			foreach ($values as $k => $v) {
//				//print_r($v->getData());
//
//				$option = $v->getData();
//				//print_r($option);
//				$hiddenOptions = [
////				"No Label",
////				"Ultimate Vapor",
////				"Private Label - Standard Paper",
////				"Private Label - Glossy Paper",
////				"Private Label - Preprinted Dymo"
//				];
//
//				if (!in_array($option['title'], $hiddenOptions)){
//
//					//if ($option['option_id'] == $optionId){
//						$jsonOptions[$i]['id'] = $option['option_id'];
//						$jsonOptions[$i]['optionTypeId'] = $option['option_type_id'];
//						$jsonOptions[$i]['name'] = $option['title'];
//					//}
//					//echo 'Option id ' . $option['option_id'] . '<br /> option token ' . $optionId . '<br />';
//
//				}
				$i++;
//			}
		}
		else {
			//print_r($o);
		}
	}

	print_r(json_encode($jsonOptions));
//	echo '</pre>';
}



