<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferOrderItem extends Model
{
    //
	public function transferOrder()
	{
		return $this->belongsTo('App\TransferOrder');
	}
}
