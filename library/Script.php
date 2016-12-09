<?php
class Register {
	public static function set($script){
		$scripts =self::get();
		$scripts[] = $script;
		Yaf_Registry::set('scripts', $scripts);
	}
	public static function get(){
		return Yaf_Registry::get('scripts');
	}
	public static function render(){
		$scripts = self::get();
		foreach ($scripts as $script) {
			echo '<script src="/public/'.$script.'"></script>';
		}
	}
}