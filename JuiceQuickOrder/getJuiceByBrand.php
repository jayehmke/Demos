<?php

$brand = isset($_GET['brand']) ? $_GET['brand'] : null;
/**
 * Created by PhpStorm.
 * User: jasonehmke
 * Date: 10/30/15
 * Time: 3:34 PM
 */

include_once "../app/Mage.php";  //Adjust for current path to Mage.php
Mage::app()->setCurrentStore(0);

$brandId = Mage::getResourceModel('catalog/category_collection')
	->addFieldToFilter('name', $brand)
	->getFirstItem()->getId(); // The parent category


$category_id = $brandId; // if you know static category then enter number

$category_model = Mage::getModel('catalog/category')->load($category_id); //where $category_id is the id of the category

$collection = Mage::getResourceModel('catalog/product_collection');

$collection->addCategoryFilter($category_model); //category filter

$collection->addAttributeToFilter('status',1); //only enabled product

$collection->addAttributeToFilter('visibility', 4);

$collection->addAttributeToSelect(array('name','url','small_image')); //add product attribute to be fetched

$collection->addStoreFilter();

$jsonProducts = [];

if(!empty($collection))

{
	$i = 0;
	foreach ($collection as $_product):

		$jsonProducts[$i]['id'] = $_product->getId();
		$jsonProducts[$i]['name'] = $_product->getName();

		//echo $_product->getName() . ' ' . $_product->getId() . "<br />";   //get product name
	$i++;
	endforeach;

}else

{

	echo 'No products exists';

}

//filter for products who name is equal (eq) to Widget A, or equal (eq) to Widget B
//$collection->addFieldToFilter(array(
//	array('attribute'=>'name','eq'=>'TFV4 Kit')
//));

print_r(json_encode($jsonProducts));