<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebOrderItem extends Model
{
	public function webOrder()
	{
		return $this->belongsTo('App\WebOrder', 'orderNumber', 'orderNumber');
	}
}
