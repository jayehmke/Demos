<?php
	
	class Manufacturer extends Eloquent {
		
		public function ingredient(){
			return $this->hasMany('Ingredient');
		}
		
	}