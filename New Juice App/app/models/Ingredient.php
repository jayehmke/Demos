<?php
	
	class Ingredient extends Eloquent {
		
		public function manufacturer(){
			return $this->belongsTo('Manufacturer', 'manufacturer_id');
		}
		
		public function flavors(){
			return $this->belongsToMany('Flavor')->withPivot('amount');
		}
		
	}