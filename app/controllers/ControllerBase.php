<?php

/**
 * Class ControllerBase Расширяет остальные контроллеры
 *
 * Инициализация моделей
 *
 * @var $this->shopModel
 * @var $this->productsModel
 * @var $this->categoriesModel
 * @var $this->pricesModel
 *
 * Инициализация настроек, локализаций, магазина
 *
 * @var $this->_config      доступ ко всем настройкам
 * @var $this->_translate   доступ к переводчику
 * @var $this->_shop        параметры текущего магазина
 *
 * @package Shop
 * @subpackage Controllers
 */

class ControllerBase extends Phalcon\Mvc\Controller
{
	protected

	   /**
		* Допустимые для магазина языки
		* @var null
		*/
		$_languages   	=   ['ru' => 'Русский', 'en' => 'English'],

		/**
		 * Текущая локаль
		 * @var null
		 */
		$_lang   	    =   'ru',

		/**
		 * Объект конфигурации
		 * @var object Phalcon\Config()
		 */
		$_config        = null,

		/**
		 * Объект Кэширования из Di
		 * @var object Phalcon\DI
		 */
		$_cache        = null,

		/**
		 * Объект переводов (можно юзать в контроллерах)
		 * @var object Phalcon\Translate\Adapter\NativeArray()
		 */
		 $_translate   	=   null,

		/**
		 * Текущий магазин
		 * @var object
		 */
		$_shop       	=   null;

	public

		/**
		 * Определение моделей
		 * @var bool | Instance objects
		 */
		$shopModel          =   false,
		$categoriesModel    =   false,
		$productsModel      =   false,
		$pricesModel        =   false;

	/**
	 * _getTransPath() Получаю путь у языковом файлу
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getTransPath()
	{
		$translationPath = '../app/messages/';
		$language = $this->session->get("language");

		if(!$language)
		{
			// устанавливаю язык по умолчанию

			$this->session->set("language", $this->_lang);

			if(file_exists($translationPath.$this->_lang))
				return $translationPath.$this->_lang;
		}
		else
		{
			if(in_array($language, array_flip($this->_languages)))
			{
				$this->_lang = $language;
				$this->session->set("language", $this->_lang);

			}
			else
			{
				$this->_lang = array_values(array_flip($this->_lang))[0];
				$this->session->set("language", $this->_lang);
			}

			if(file_exists($translationPath.$this->_lang))
				return $translationPath.$this->_lang;
		}
	}

	/**
	 * loadMainTrans() Загружаю перевод для layout шаблонов
	 * @access public
	 * @return null
	 */
	public function loadMainTrans()
	{
		$translationPath = $this->_getTransPath();
		$messages = [];
		require $translationPath."/layout.php";

		$mainTranslate = new Phalcon\Translate\Adapter\NativeArray([
			"content" => $messages
		]);

		// $layoutTranslate переводы в layout шаблонов
		$this->view->setVar("layoutTranslate", $mainTranslate);
	}

	/**
	 * loadCustomTrans() Загружаю перевод для конкретного контроллера
	 * @access public
	 * @return null
	 */
	public function loadCustomTrans($transFile)
	{
		$translationPath = $this->_getTransPath();
		$messages = [];
		require $translationPath.'/'.$transFile.'.php';

		//Return a translation object
		$this->_translate = new Phalcon\Translate\Adapter\NativeArray([
			"content" => $messages
		]);

		// $viewTranslate - переводы во views
		$this->view->setVar("viewTranslate", $this->_translate);
	}

	/**
	 * initialize() Инициализация стэша
	 * @access public
	 * @return null
	 */
	public function initialize()
	{
		// Загрузка конфигураций

		$this->_config	=	$this->di->get('config');
		$this->_cache   =   $this->di->get('frontendCache');

		// Загрузка локалей

		$this->loadMainTrans();

		// Инициализация моделей (объекты доступны в контроллерах и views)

		$this->shopModel        =   new Models\Shops();
		$this->productsModel    =   new Models\Products();
		$this->categoriesModel  =   new Models\Categories();
		$this->pricesModel      =   new Models\Prices();

		// Получение параметров текущего магазина

		$this->_shop = $this->shopModel->get(['host'	=>	$this->request->getHttpHost()],[], 1, true);

		$sqlCategories = "SELECT ".Models\Categories::TABLE.".*
			FROM ".Models\Categories::TABLE." WHERE ".Models\Categories::TABLE.".parent_id = 0";
			
		$categories = (object)$this->db->query($sqlCategories)->fetchAll();

		$sqlNewProducts = "SELECT ".Models\Products::TABLE.".*, ".Models\Prices::TABLE.".price
			FROM ".Models\Products::TABLE."
			INNER JOIN ".Models\Prices::TABLE." ON ".Models\Products::TABLE.".id = ".Models\Prices::TABLE.".product_id
			WHERE ".Models\Products::TABLE.".published = 1
			AND ".Models\Prices::TABLE.".id = " . $this->_shop->id .
			" ORDER BY ".Models\Products::TABLE.".date_create DESC LIMIT 6";

		$newProducts = $this->db->query($sqlNewProducts)->fetchAll();

		// Инициализация навигации

		$nav = $this->di->get('navigation');

		$nav->setActiveNode(
			$this->router->getActionName(),
			$this->router->getControllerName()
		);

		// Установка директории с шаблонами
		$this->view->setViewsDir($this->_config->application->viewsDir.'/'.$this->_shop->code);

		// В конце запись переменных для шаблонов
		$this->view->setVars([
			'language'	    =>	$this->_lang,       // текущий язык
			'languages'	    =>	$this->_languages,  // все доступные языки
			'shop' 		    => 	$this->_shop,       // параметры магазина
			'topnav' 	    => 	$nav,               // топ меню навигации
			'categories'    =>  $categories,        // главные категории
			'newProducts'   =>  $newProducts        // новые товары
		]);
	}

	/**
	 * Страница для кэша
	 * @param $method Метод action из контроллера
	 * @return string
	 */
	public function cachePage($method)
	{
		if($this->_shop)
			return strtolower($this->_shop->code.'-'.$this->_lang.'-'.substr($method, 0, -6).'.cache');
	}
}
