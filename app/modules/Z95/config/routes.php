<?php
	/**
	 * Конфигурация роутера Phalcon
	 */

	// Роутер каталога

	$router->addGet("/catalogue/:params", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'index'
	]);

	$router->add("/brands", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'brands',
	]);

	$router->addGet("/catalogue/top/:params", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'top'
	]);

	$router->addGet("/catalogue/new/:params", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'new'
	]);

	$router->addGet("/catalogue/sale/:params", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'sale'
	]);

	//Странциа всех скидок
	$router->addGet("/catalogue/sale", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'salepage'
	]);

	$router->addGet("/catalogue", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'categories'
	]);

	$router->add("/cart", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'cart',
		'action'        => 'index',
	]);

	$router->addGet("/catalogue/[0-9]+", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'catalogue',
		'action'        => 'item'
	]);

	$router->add("/about", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'index',
		'action'        => 'about',
	]);

	$router->add("/discounts", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'index',
		'action'        => 'discounts',
	]);

	$router->add("/delivery", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'index',
		'action'        => 'delivery',
	]);

	$router->add("/return", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'index',
		'action'        => 'return',
	]);

	$router->add("/opt", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'index',
		'action'        => 'opt',
	]);
	$router->add("/order", [
		'module'    	=>  $module,
		'namespace' 	=> 'Modules\\'.$module.'\Controllers\\',
		'controller'    => 'index',
		'action'        => 'index',
	]);

	// Удаление косых в конце
	$router->removeExtraSlashes(true);