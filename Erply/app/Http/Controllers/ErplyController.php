<?php

namespace App\Http\Controllers;

use Mail;
use App\Http\Controllers\Controller;
use App\Library\EAPI;
use App\TransferOrder;
use App\TransferOrderItem;
use App\ErplyProduct;
use App\WebOrder;
use App\WebOrderItem;
use Illuminate\Http\Request;


class ErplyController extends Controller
{

	public function api(){
		// Initialise class
		$api = new EAPI();

		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		// Configuration settings
		$api->clientCode = "428377";
		$api->username = "jason.ehmke@madvapes.com";
		$api->password = "Vaping123";
		$api->url = "https://".$api->clientCode.".erply.com/api/";

		return $api;
	}


	public function main(){

		//$this->getTransferOrders();
		//$this->getProducts();
		//echo $this->getErplyID('4272');
		//$this->saveRegistration('4272', 50, 'RMA');
		//$this->getInventoryRegistrations();
		//$this->saveWriteOff();
		//$this->getReasonCodes();
		//$this->sendOrderToErply();
		//$this->getWebOrders();
		//$this->sendNoProductEmail('4272');
	}


	public function getTransferOrders()
	{

		// @TODO Get last 24 hours of transfer orders & check if exists, only add new
		// Get all inventory transfers
		$result = $this->api()->sendRequest("getInventoryTransfers", array(
			'type' => 'TRANSFER_ORDER',
			'warehouseFromID' => 1,
			'confirmed' => 1,
			'getCost' => 1
		));

		// Default output format is JSON, so we'll decode it into a PHP array
		$output = json_decode($result, true);

		// Pull the transfer records out of the data
		$transfers = $output['records'];

		foreach($transfers as $transfer){

			// Check to see if the transfer exists already
			$count = TransferOrder::where('inventoryTransferNo', $transfer['inventoryTransferNo'])->count();

			if ($count == 0){ // if 0, we're good to insert
				// Create a new transfer in the DB
				$transferOrder = new TransferOrder;
				$transferOrder->inventoryTransferID = $transfer['inventoryTransferID'];
				$transferOrder->inventoryTransferNo = $transfer['inventoryTransferNo'];
				$transferOrder->creatorID = $transfer['creatorID'];
				$transferOrder->warehouseFromID = $transfer['warehouseFromID'];
				$transferOrder->warehouseToID = $transfer['warehouseToID'];
				$transferOrder->followupInventoryTransferID = $transfer['followupInventoryTransferID'];
				$transferOrder->date = $transfer['date'];
				$transferOrder->notes = $transfer['notes'];
				$transferOrder->confirmed = $transfer['confirmed'];
				$transferOrder->added = $transfer['added'];
				$transferOrder->lastModified = $transfer['lastModified'];
				$transferOrder->save();

				// Loop through the order items and add them to the DB
				foreach($transfer['rows'] as $item){

					$transferItem = new TransferOrderItem;
					$transferItem->productID = $item['productID'];
					$transferItem->price = $item['price'];
					$transferItem->amount = $item['amount'];
					$transferItem->cost = $item['cost'];
					$transferItem->transfer_order_id = $transfer['inventoryTransferNo'];
					$transferItem->save();

				}
			}

		}

	}

	public function getProducts(){

		$x = 1; // Set page 1 to start

		do {
			// Get the products, starting with page 1
			$result = $this->api()->sendRequest("getProducts", array(
				'recordsOnPage' => 1000,
				'pageNo' => $x // Set the page number to x so I can increment it.
			));

			$output = json_decode($result, true);

			// If there are 1000 records in the array, there might be more we need to process
			if (count($output['records']) == 1000){
				$x = $x +1;
			}
			else ($x = 500); // setting to a high number to exit the loop

			foreach($output['records'] as $product){
				// Find the product and update it. If no product, create a new one.
				$erplyProduct = ErplyProduct::firstOrNew(['productID' => $product['productID']]);
				$erplyProduct->productID = $product['productID'];
				$erplyProduct->name = $product['name'];
				$erplyProduct->code =$product['code'];
				$erplyProduct->code2 = $product['code2'];
				$erplyProduct->manufacturerName = $product['manufacturerName'];
				$erplyProduct->cost = $product['cost'];
				$erplyProduct->groupName = $product['groupName'];
				$erplyProduct->categoryName = $product['categoryName'];
				$erplyProduct->save();
			}

		} while ($x < 500);


	}

	public function getSingleErplyProduct($sku){

		// Not used right now, possible to retrieve missing products

		$result = $this->api()->sendRequest("getProducts", array(
			'code2' => $sku
		));
		$output = json_decode($result, true);
		//dd($output);
		if ($output['status']['recordsTotal'] == 0){
			dd($output);
		}



	}

	public function getErplyID($sku){
		// Find the single item
		$erplyID = ErplyProduct::where('code2', $sku)->first();
		if ($erplyID){

			// If the item exists, return the Erply ID
			return $erplyID->productID;
		}
		else {

			// If the item does not exist, send an email to get it fixed
			$this->sendNoProductEmail($sku);
			return 'e404';
		}

	}

	public function getInventoryRegistrations(){
		$result = $this->api()->sendRequest("getInventoryRegistrations", array(

		));

		$output = json_decode($result, true);

		dd($output);
	}

	public function saveRegistration($sku, $amount, $cause){
		$result = $this->api()->sendRequest("saveInventoryRegistration", array(
			'warehouseID' => 1,
			'productID1' => $this->getErplyID($sku),
			'amount1' => $amount,
			'cause' => $cause
		));
		$output = json_decode($result, true);
		dd($output);
	}

	public function saveWriteOff(){
		$result = $this->api()->sendRequest("saveInventoryWriteOff", array(
			'reasonID' => 5,
			'warehouseID' => 1,
			'productID1' => $this->getErplyID("4272"),
			'amount1' => 5

		));
	}

	public function getWebOrders(){
		$items = WebOrder::find(1)->items;
		foreach($items as $item){
			echo "<pre>";
			print_r($item->sku);
			echo "</pre>";
		}
	}

	public function saveWebSale(Request $request){

		// Create a new WebOrder instance
		$webOrder = new WebOrder;
		$webOrder->orderNumber = $request->orderNumber;
		$webOrder->source = $request->source;

		// Create a new array we need to fill
		$webOrderItems = [];
		$x = 0;
		foreach($request->items as $sku => $amount){
			// Loop through the items in the post and create new items to fill
			$webOrderItems[$x] = new WebOrderItem;
			$webOrderItems[$x]->sku = $sku;
			$webOrderItems[$x]->amount = $amount;
			$x++;
		}

		// Save the order
		$webOrder->save();
		// Attach the items to the order
		$webOrder->items()->saveMany($webOrderItems);


	}

	public function sendOrderToErply(){

		// Start creating the request for the API call
		$erplyOrder = array(
			'reasonID' => 5,
			'warehouseID' => 1
		);

		// @TODO Retrieve the web order or pass in the function?
		$order = WebOrder::find(1);

		$x = 1;
		foreach($order->items as $orderItem){
			// Get the Erply ID of the products
			$erplyID = $this->getErplyID($orderItem->sku);

			// If it exists, add it to the $erplyOrder array
			if ($erplyID != 'e404'){
				$erplyOrder["productID$x"] = $erplyID;

				// Assign the quantity to the API request
				$erplyOrder["amount$x"] = $orderItem->amount;
				$x++;
			}
		}


		dd($erplyOrder);

		$result = $this->api()->sendRequest("saveInventoryWriteOff", $erplyOrder);
		$output = json_decode($result, true);
		dd($output);
//		echo "<pre>";
//		print_r(json_decode(json_encode($request->input())));
//		echo "</pre>";
	}

	public function getReasonCodes(){
		$result = $this->api()->sendRequest("getReasonCodes", array(

		));
		$output = json_decode($result, true);
		dd($output);
	}

	public function sendNoProductEmail($sku){

		Mail::raw('SKU ' . $sku . ' does not exist in erply and there was a web order. Please fix immediately.', function ($message) use ($sku) {

			$message->from('jason.ehmke@madvapes.com', $name = null);
			$message->sender('jason.ehmke@madvapes.com', $name = null);
			$message->to('jason.ehmke@madvapes.com', $name = null);
			$message->subject('Erply Error: ' . $sku . ' Does not exist in the database.');
		});
	}

	//@TODO Poll reason codes


}