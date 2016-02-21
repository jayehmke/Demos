<?php

class Mverp_InventorySync_Model_Mysql4_InventorySync extends Mage_Core_Model_Mysql4_Abstract

{
	public function _construct()
	{
		$this->_init('<inventorysync>/<inventorysync>', 'id');
	}
}