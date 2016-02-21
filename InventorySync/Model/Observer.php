<?php
/**
 * Created by PhpStorm.
 * User: jasonehmke
 * Date: 2/23/15
 * Time: 9:11 AM
 */

class Mverp_InventorySync_Model_Observer
{
	/**
	 * Magento passes a Varien_Event_Observer object as
	 * the first parameter of dispatched events.
	 */

	public function __construct()
	{

	}
	public function logUpdate(Varien_Event_Observer $observer)
	{
		$write = Mage::getSingleton("core/resource")->getConnection("core_write");
		$event = $observer->getEvent();
		$_item = $observer->getEvent()->getProduct();
		if ($_item->getOrigData()){
			$origStockData = $_item->getOrigData('stock_item')->getOrigData();
		}
		$sku = $_item->getSku();
		$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
				->loadByProduct($_item)->getQty();
		if(Mage::getSingleton('admin/session')->isLoggedIn()) {
			$user = Mage::getSingleton('admin/session');
			$userId = $user->getUser()->getUserId();
		}
		else{
			$userId = 0;
		}
		$qtyChange = $stocklevel - intval($origStockData['qty']);
		$date = date("Y-m-d H:i:s");


		if (!function_exists('initializeControl')) {
			function initializeControl($url)
			{
				$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

				$options = array(

						CURLOPT_CUSTOMREQUEST => "GET",        //set request type post or get
						CURLOPT_POST => false,        //set to GET
						CURLOPT_USERAGENT => $user_agent, //set user agent
						CURLOPT_COOKIEFILE => "cookie.txt", //set cookie file
						CURLOPT_COOKIEJAR => "cookie.txt", //set cookie jar
						CURLOPT_RETURNTRANSFER => true,     // return web page
						CURLOPT_HEADER => false,    // don't return headers
						CURLOPT_FOLLOWLOCATION => true,     // follow redirects
						CURLOPT_ENCODING => "",       // handle all encodings
						CURLOPT_AUTOREFERER => true,     // set referer on redirect
						CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
						CURLOPT_TIMEOUT => 5,      // timeout on response
						CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
				);

				$ch = curl_init($url);
				curl_setopt_array($ch, $options);
				$content = curl_exec($ch);
				$err = curl_errno($ch);
				$errmsg = curl_error($ch);
				$header = curl_getinfo($ch);
				curl_close($ch);

			}
		}

		if (intval($origStockData <= 0)){
			$query = "INSERT INTO mverp_db_adjustments_control (sku,current_qty,qty,description,created_at,user_id) VALUES('" . $sku . "','" . $stocklevel . "','" . $stocklevel . "','manual','" . $date . "',$userId)";
			$write->query($query);
			//$data = initializeControl($url);
			//Mage::log( "Qty: ".$data, null, 'orderlog.log');
		}
		elseif ($qtyChange != 0) {

			$query = "INSERT INTO mverp_db_adjustments_control (sku,current_qty,qty,description,created_at,user_id) VALUES('" . $sku . "','" . $stocklevel . "','" . $qtyChange . "','manual','" . $date . "',$userId)";
			$write->query($query);
			//$data = initializeControl($url);
			//Mage::log( "Qty: ".$data, null, 'orderlog.log');
		}
		$url = "http://sync.wholesalevapingsupply.com/process.aspx?source=MV&ver=live&orderid=manual";
		initializeControl($url);

	}
	public function cancelOrderItem(Varien_Event_Observer $observer)
	{
		$write = Mage::getSingleton("core/resource")->getConnection("core_write");
		$orderId = $observer->getEvent()->getItem()->getOrder()->getId();
		$itemId = $observer->getEvent()->getItem();
		$skuCancelled = $itemId->getSku();

		$product_id = Mage::getModel("catalog/product")->getIdBySku( $skuCancelled );

		$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
		$stockLevel = (int)$stockItem->getQty();
		Mage::log( "Qty: ".$stockLevel, null, 'orderlog.log');
		$qtyCancelled = $itemId->getQtyOrdered(); //$item->getQtyToInvoice();
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO mverp_db_adjustments_control (sku,current_qty,qty,description,created_at,user_id) VALUES('".$skuCancelled."','".$stockLevel."','".$qtyCancelled	."','cancel','".$date."',$userId)";
		$write->query($query);

		$url = "http://sync.wholesalevapingsupply.com/process.aspx?source=MV&ver=live&orderid=cancel";

		$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

		$options = array(

				CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
				CURLOPT_POST           =>false,        //set to GET
				CURLOPT_USERAGENT      => $user_agent, //set user agent
				CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
				CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
				CURLOPT_RETURNTRANSFER => true,     // return web page
				CURLOPT_HEADER         => false,    // don't return headers
				CURLOPT_FOLLOWLOCATION => true,     // follow redirects
				CURLOPT_ENCODING       => "",       // handle all encodings
				CURLOPT_AUTOREFERER    => true,     // set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
				CURLOPT_TIMEOUT        => 5,      // timeout on response
				CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);

		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;




		//$data = initializeControl($url);
		//Mage::log( "Qty: ".$data, null, 'orderlog.log');

	}
	public function logOrder(Varien_Event_Observer $observer){
		//$order = $observer->getEvent()->getOrder()->getIncrementId()

		$order = $observer->getEvent()->getOrder();

		if (!$order) {
			return $this;
		}

		$orderId = $order->getData('entity_id');

		$loadedOrder = Mage::getModel('sales/order')->load($orderId);

		$orderItems = $loadedOrder->getItemsCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('product_type', array('eq'=>'simple'))
				->load();

		$url = "http://sync.wholesalevapingsupply.com/process.aspx?source=MV&ver=live&orderid=".$orderId;
		// initialize cURL


		foreach($orderItems as $sItem) {

			$itemId = Mage::getModel("catalog/product")->getIdBySku($sItem->getSku());
			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($itemId);
			$stockLevel = $stockItem->getQty();
			Mage::log( "Item ID: ".$itemId."\n", null, 'orderlog.log');
			$manageStock = $stockItem->getData('manage_stock');
			$configManageStock = $stockItem->getData('use_config_manage_stock');

			$pItemId = $sItem->getParentItemId();

			$item = Mage::getModel('sales/order_item')->load("$pItemId"); //use an item_id here
			if ($manageStock == 1 || $configManageStock == 1){
				$write = Mage::getSingleton("core/resource")->getConnection("core_write");
				$date = date("Y-m-d H:i:s");
				$qty = 0 - intval($sItem->getQtyOrdered());
				$query = "INSERT INTO mverp_db_adjustments_control (sku,qty,current_qty,order_number,description,created_at) VALUES('".$sItem->getSku()."','".$qty	."','".$stockLevel	."','".$sItem->getOrderId()	."','order','".$date."')";
				$write->query($query);
				//Mage::log( "Qty: ".$qty, null, 'orderlog.log');
			}

			//}
		}
		function initializeControl($url)
		{
			$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

			$options = array(

					CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
					CURLOPT_POST           =>false,        //set to GET
					CURLOPT_USERAGENT      => $user_agent, //set user agent
					CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
					CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
					CURLOPT_RETURNTRANSFER => true,     // return web page
					CURLOPT_HEADER         => false,    // don't return headers
					CURLOPT_FOLLOWLOCATION => true,     // follow redirects
					CURLOPT_ENCODING       => "",       // handle all encodings
					CURLOPT_AUTOREFERER    => true,     // set referer on redirect
					CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
					CURLOPT_TIMEOUT        => 5,      // timeout on response
					CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			);

			$ch      = curl_init( $url );
			curl_setopt_array( $ch, $options );
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			curl_close( $ch );

			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $content;
			if(strpos($header['content'],'False') !== false){
				return false;
			}
			else {
				return true;
			}


		}
		$data = initializeControl($url);
		Mage::log( "Qty: ".$data, null, 'orderlog.log');
		//die('sales_order_place_after');
		//$params = $observer->getEvent()->getOrder;
		//Mage::log(print_r($order->getData(),false, 'orderlog.log'));
		//echo "<pre>";print_r($params);die();
		//Mage::log('sales_order_place_after'. $order->getData('entity_id'), null, 'orderlog.log');
	}

//	public function refundOrderInventory(Varien_Event_Observer $observer)
//	{
//		$creditmemo = $observer->getEvent()->getCreditmemo();
//		foreach ($creditmemo->getAllItems() as $item) {
//			$params = array();
//			$params['product_id'] = $item->getProductId();
//			$params['sku'] = $item->getSku();
//			$params['qty'] = $item->getProduct()->getStockItem()->getQty();
//			$params['qty_change'] = ($item->getQty());
//		}
//	}
}

