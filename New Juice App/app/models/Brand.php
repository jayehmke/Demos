<?php
	
	class Brand extends Eloquent {
		
		public function flavors(){
			return $this->hasMany('Flavor');
		}
		
		public function users(){
			return $this->belongsToMany('User')->withPivot('has_access');
		}
		
	}