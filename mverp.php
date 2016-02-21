<?php
/**
 * Created by PhpStorm.
 * User: jasonehmke
 * Date: 2/20/15
 * Time: 4:49 PM
 */

include_once "app/Mage.php";  //Adjust for current path to Mage.php
Mage::app()->setCurrentStore(0);

$sku = isset($_GET['sku']) ? $_GET['sku'] : null;
$qty = isset($_GET['qty']) ? $_GET['qty'] : null;
$function = isset($_GET['function']) ? $_GET['function'] : null;
$orderNumber = isset($_GET['ordernumber']) ? $_GET['ordernumber'] : null;
$trackingNumber = isset($_GET['trackingnumber']) ? $_GET['trackingnumber'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;
$warehouse = isset($_GET['warehouse']) ? $_GET['warehouse'] : null;
$currentQty = isset($_GET['currentqty']) ? $_GET['currentqty'] : null;
$shipmentId = isset($_GET['shipmentId']) ? $_GET['shipmentId'] : null;

//$function = isset($_GET['status']) ? $_GET['status'] : null;

$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read = Mage::getSingleton("core/resource")->getConnection("core_read");


function setErply($qty){


}


function sendShippingEmail($shipmentId){
	echo "sending email...";
	$shipment = Mage::getModel('sales/order_shipment');

	$shipment->load($shipmentId);

	if (!$shipment->getId()) {
		echo "Shipment doesn't exist";
	}

	echo "<pre>";
	print_r($shipment->getData());
	echo "</pre>";
	//die();
	$shipment->sendEmail(true, '');
	$shipment->setEmailSent(true);

}

function getDescBySku($sku){
	$productName = '';

	$product_collection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('*')
			->addAttributeToFilter('sku', array('like' => ''.$sku.''))
			->load();

	foreach ($product_collection->getData() as $product){

		$my_product = Mage::getModel('catalog/product')->load($product['entity_id']);
		$Content = preg_replace("/&#?[a-z0-9]+;/i","",strip_tags($my_product->getDescription(), ''));
		$Content = preg_replace( "/\r|\n/", "", $Content );
		$Content = str_replace( ",", " ", $Content );
		echo($Content );

	}

	//return $productName;
}

function getId($sku){
	$id = Mage::getModel("catalog/product")->getIdBySku($sku);
	return $id;
}

function invoiceOrder($orderNumber){
	$order = Mage::getModel("sales/order")->loadByIncrementId($orderNumber);

	try {
		if(!$order->canInvoice())
		{
			Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
		}

		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

		if (!$invoice->getTotalQty()) {
			Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
		}

		$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
		$invoice->register();
		$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder());

		$transactionSave->save();

		$order->setData('state', "processing");
		$order->setStatus("processing");
		$history = $order->addStatusHistoryComment('Order was set to Processing automatically.', false);
		$history->setIsCustomerNotified(false);
		$order->save();

	}
	catch (Mage_Core_Exception $e) {

	}


}

function GetOrderItemId($order_items, $sku) {
	foreach ($order_items as $order_item) {
		if ($order_item->getSku() == $sku) {
			return $order_item->getItemId();
		}
	}
	return 0;
}

function updateSku($sku, $qty){
	$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct(getId($sku));
	if (!$stockItem->getId()) {
		return "e404";
		//$stockItem->setData('product_id', 23841);
		//$stockItem->setData('stock_id', 1);
	}
	elseif ($stockItem->getQty() != $qty) {
		$stockItem->setData('qty', $qty);

		$backorderable = $stockItem->getBackorders();

		if ($backorderable){
			$stockItem->setData('is_in_stock', $qty ? 1 : 1);
		}
		else{
			$stockItem->setData('is_in_stock', $qty > 0 ? 1 : 0);
		}

		$stockItem->save();
	}
}

function getStockQty($sku){
	$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct(getId($sku));
	if (!$stockItem->getId()) {
		return "e404";
	}
	else{
		$qtyInStock = $stockItem->getQty();
		return intval($qtyInStock);
	}
}

function holdPickup($orderNumber){
	$order = Mage::getModel('sales/order')
		->getCollection()
		//->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
		->addAttributeToFilter('increment_id', $orderNumber)
		->getFirstItem();
	$orderId = $order->getId();
	$loadedOrder = Mage::getModel('sales/order')->load($orderId);
	$state = 'pickup';
	$status = 'pickup';
	$comment = 'Changing state to pickup and status to pickup Status';
	$isCustomerNotified = false;
	$loadedOrder->setState($state, $status, $comment, $isCustomerNotified);
	$loadedOrder->save();
}

function completeOrder($orderNumber){

	$order = Mage::getModel('sales/order')
		->getCollection()
		//->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
		->addAttributeToFilter('increment_id', $orderNumber)
		->getFirstItem();

	$orderId = $order->getId();

	$loadedOrder = Mage::getModel('sales/order')->load($orderId);

	$loadedOrder->setData('state', "complete");
	$loadedOrder->setStatus("complete");
	$history = $loadedOrder->addStatusHistoryComment('Order marked as complete automatically.', false);
	$history->setIsCustomerNotified(false);
	$loadedOrder->save();
}

function shipItem($orderNumber, $trackingNumber, $products){

	$shippedItems = json_decode($products, true);

	$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);

	$email=true;
	$includeComment=true;
	$comment="test Shipment";

	if ($order->canShip()) {
		/* @var $shipment Mage_Sales_Model_Order_Shipment */
		/* prepare to create shipment */
		$shipment = $order->prepareShipment($shippedItems);
		if ($shipment) {

			$shipmentCarrierCode = 'fedex';
			$shipmentCarrierTitle = 'FedEx';

			$arrTracking = array(
				'carrier_code' => isset($shipmentCarrierCode) ? $shipmentCarrierCode : $order->getShippingCarrier()->getCarrierCode(),
				'title' => isset($shipmentCarrierTitle) ? $shipmentCarrierTitle : $order->getShippingCarrier()->getConfigData('title'),
				'number' => $trackingNumber,
			);

			$track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
			$shipment->addTrack($track);

			$shipment->register();
			$shipment->addComment($comment, $email && $includeComment);
			$shipment->getOrder()->setIsInProcess(true);
			try {
				$transactionSave = Mage::getModel('core/resource_transaction')
					->addObject($shipment)
					->addObject($shipment->getOrder())
					->save();
				$shipment->sendEmail($email, ($includeComment ? $comment : ''));

				$qtyOrdered = array();
				$qtyShipped = array();

				foreach ($order->getAllItems() as $_eachItem) {
					$qtyOrdered[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
					$qtyShipped[$_eachItem->getId()] = $_eachItem->getQtyShipped();
					//echo $_eachItem->getId() . ' : ' . $_eachItem->getQtyOrdered() . ' : ' . $_eachItem->getQtyShipped();
				}

				if ($qtyOrdered == $qtyShipped){
					completeOrder($orderNumber);
				}

				return true;
			} catch (Mage_Core_Exception $e) {
				return "Can ship, but had errors";
			}

		}

	}
	return "Can't ship for some stupid reason.";
}

function setStockBySkuAndPlace($sku, $place_id, $setQty) {

	global $write;

	$product_id = getId($sku);

//	$query = "update advancedinventory_stock set `quantity_in_stock` = $setQty where product_id = $product_id and place_id = $place_id";
//	$write->query($query);
//	$advancedinventory_item = Mage::getSingleton('core/resource')->getTableName('advancedinventory_item');
//
//	$collection = Mage::getModel('advancedinventory/stock')->getCollection()
//		->addFieldToFilter('main_table.product_id', Array('eq' => $product_id))
//		->addFieldToFilter('place_id', Array('eq' => $place_id));
//	$collection->getSelect()->joinLeft(
//		array("lsp" => $advancedinventory_item), "lsp.product_id = $product_id", array(
//			"manage_local_stock" => "lsp.manage_local_stock",
//		)
//	);
//	echo "<pre>";
//	//print_r($collection->getFirstItem());
//	echo "</pre>";
//	echo "Quantity in Stock for Place ID $place_id: " . $collection->getFirstItem()->getQuantityInStock();
//	echo "<br />";
//	//print_r($collection->getFirstItem());


	$stockModel = Mage::getModel("advancedinventory/item")->loadByProductId($product_id);

	$data = array(
		"id" => $stockModel->getId(),
		"product_id" => $product_id,
		"manage_local_stock" => true,
	);

	$stock_id = $stockModel->setData($data)->save()->getId();

	$product = Mage::getModel("catalog/product")->load(getId($sku));


	$stores = Mage::getModel('pointofsale/pointofsale')->getPlacesByStoreId(1);

	$model = Mage::getModel("advancedinventory/stock");

	$stock = $model->getStockByProductIdAndPlaceId($product_id, $place_id);

	$data = array(
		"id" => $stock->getId(),
		"localstock_id" => $stock_id,
		"product_id" => $product_id,
		"place_id" => $place_id,
		//"manage_stock" => 1,
		"quantity_in_stock" => $setQty,
		"backorder_allowed" => 0,
		//"use_config_setting_for_backorders" => 1,
	);

	$model->setData($data)->save();

	$totalQty = 0;

	foreach ($stores as $s) {

		$stock = Mage::getModel('advancedinventory/stock')->getStockByProductIdAndPlaceId($product_id, $s['place_id']);

		//print_r($stock->getData('quantity_in_stock'));

		$totalQty = $totalQty + $stock->getData('quantity_in_stock');
	}

	updateSku($sku, $totalQty);


}

function getStockBySkuAndPlace($sku, $place_id){
	$collection = Mage::getModel('advancedinventory/stock')->getCollection()
		->addFieldToFilter('product_id', Array('eq' => getId($sku)));

	$warehouses = null;
	foreach ($collection->getData() as $productData){

		$place_id = $productData['place_id'];
		$qty = $productData['quantity_in_stock'];
		$reserve = $productData['reserve_qty'];

		$warehouses = $warehouses . "<warehouses><id>" . $place_id . "</id><reserve>" . $reserve . "</reserve><qty>" . $qty . "</qty></warehouses>";

		//echo "<pre>" . htmlentities("<warehouses><id>$place_id</id><reserve>$reserve</reserve><qty>$qty</qty></warehouses>") . "</pre>";
	}

	if (!$warehouses){

		$qty = getStockQty($sku);

		return "<?xml version='1.0' encoding='UTF-8'?><magento><qty>$qty</qty></magento>";

	}
	else{
		return "<?xml version='1.0' encoding='UTF-8'?><product><sku>$sku</sku>$warehouses</product>";
	}
}

function getJson($sku){
	$productId = getId($sku);
	//echo $productId;

	$product = Mage::getModel('catalog/product')->load($productId);
	$attributeNames = array_keys($product->getData());

//	echo "Just the attribute names";
//	echo "<pre>";
//
//	print_r($attributeNames);
//	echo "</pre>";

//	echo "Attribute names and values";
//
//	echo "<pre>";
//	print_r($product->getData());
//	echo "</pre>";

	$myArray = [];
	$i = 0;
	foreach ($product->getData() as $key=>$value){
		$myArray[$i]['name'] = $key;
		$myArray[$i]['caption'] = $key;
		$myArray[$i]['id'] = $key;
		$myArray[$i]['type'] = "text";
		$myArray[$i]['value'] = $value;


		$i++;
	}

//	echo "<pre>";
//	print_r($myArray);
//	echo "</pre>";

	$json = json_encode($myArray);

	return $json;

}

if ($function == "set" && $type != "receiving"){
	echo updateSku($sku, $qty);
	echo getStockQty($sku);
}

if ($function == "get"){
	echo getStockQty($sku);
}

if ($function == "set" && $type == "receiving"){

	$multistock_enabled = Mage::getModel("advancedinventory/stock")->getMultiStockEnabledByProductId(getId($sku));

	//$stock_id = $stock->setData($data)->save()->getId();

	//print_r($placeCollection->getAllIds());
	if ($multistock_enabled) {

		echo "Quantity to set: $qty";
		echo "<br />";
		echo "Warehouse: $warehouse";
		echo "<br />";
		$product = Mage::getModel('catalog/product')->load(getId($sku));
		$reservedQty = $product->getReservedQty();

		$stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct(getId($sku));

		if ($stockItem){
			$stockItem->setQty($qty);
			$stockItem->save();
		}

/*
		echo "<br />";
		print_r($reservedQty);
		echo "<br />";
*/

		$place_qty = 135;

		if ($reservedQty > $qty){
			echo "hello";
		}

		$query = "Select store_id, store_code from pointofsale";
		$placeCollection = $read->fetchAll($query);

		foreach ($placeCollection as $place) {

/*
			echo "<pre>";
			print_r($place);
			echo "</pre>";
*/

			if ($place['store_code'] == "reserved"){

			}
			$placeId = 1;
			setStockByProductIdAndPlaceId($sku, $placeId, $place_qty);

		}
	}

	else{
		echo "Multi stock not enabled. Set item inventory manually";
	}


	//$stocks = Mage::getModel('advancedinventory/stock')->

	//$collection = Mage::getModel('pointofsale/pointofsale')->getCountries(Mage::app()->getStore()->getStoreId())->addFieldToFilter('status', Array('status' => 1));


	$places = Mage::getModel('advancedinventory/stock')->getStocksByProductIdAndStoreId(getId($sku), Mage::app()->getStore()->getStoreId());
/*
	echo "<pre>";
	print_r($places->getAllIds());
	echo "</pre>";
*/

	foreach ($places as $p) {

		//$data = Mage::getModel('advancedinventory/stock')->getStockByProductIdAndPlaceId($child->getId(), $p->getPlaceId());
		//$children[$i]['stock'][] = array("store" => $p->getPlaceId(), "qty" => $data->getQuantityInStock(), "status" => Mage::helper('advancedinventory')->getStockStatus($data));
	}
//
//	echo "<pre>";
//	print_r($children);
//	echo "</pre>";
}

if ($function == "holdforpickup"){
	holdPickup($orderNumber);
}

if ($function == "completeorder"){
	completeOrder($orderNumber);
}

if ($function == "shiporder"){

	$json = file_get_contents('php://input');

	print_r(shipItem($orderNumber, $trackingNumber, $json));

//	$orderIncrementId = '1400047153';
//	$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
//	$items = $order->getAllVisibleItems();
//	foreach($items as $i):
//		echo $i->getProductId();
//		echo "<br />";
//		echo $i->getSku();
//	endforeach;

}

if ($function == "logcontrol"){

	global $write;
	$date = date("Y-m-d H:i:s");
	

	$query = "INSERT INTO mverp_db_adjustments_control (sku,current_qty,qty,description,created_at) VALUES('" . $sku . "','" . $currentQty . "','" . $qty . "','" . $type . "','" . $date . "')";
	$write->query($query);


}

if ($function == "getjson"){
	echo getJson($sku);
}

if ($function == "getmulti"){
	echo getStockBySkuAndPlace($sku, $warehouse);
}

if ($function == "setmulti"){
	setStockBySkuAndPlace($sku, $warehouse, $qty);
	echo getStockBySkuAndPlace($sku, $warehouse);
}

if ($function == "invoiceorder"){
	invoiceOrder($orderNumber);
}

if ($function == "getDescBySku"){
	getDescBySku($sku);
}

if ($function == "sendShippingEmail"){
	sendShippingEmail($shipmentId);
}

?>