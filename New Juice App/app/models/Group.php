<?php
	
	class Group extends Eloquent {
		
		public function user(){
			return $this->hasMany('User');
		}
		
	}