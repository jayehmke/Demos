<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebOrder extends Model
{
	public function items()
	{
		return $this->hasMany('App\WebOrderItem', 'orderNumber', 'orderNumber');
	}
}
