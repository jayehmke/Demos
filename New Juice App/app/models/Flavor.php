<?php
	
	class Flavor extends Eloquent {
		
		public function ingredients(){
			return $this->belongsToMany('Ingredient')->withPivot('amount');
		}
		
		public function brand(){
			return $this->belongsTo('Brand');
		}
		
	}