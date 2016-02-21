<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErplyProduct extends Model
{
    // Enable mass assignment
	protected $fillable = array('productID', 'name', 'code', 'code2', 'manufacturerName', 'cost', 'groupName','categoryName');
}
