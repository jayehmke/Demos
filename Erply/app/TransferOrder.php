<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Model
{
	public function items()
	{
		return $this->hasMany('App\TransferOrderItem');
	}
}
