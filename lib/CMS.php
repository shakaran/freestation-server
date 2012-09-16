<?php
/**
 * CMS class file.
 *
 * @copyright 2011, (c) Ángel Guzmán Maeso
 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
 * @version 1.0
 */
class CMS
{
	/** @var $menu Enable or disable the vertical menu */
	private $vertical_menu = TRUE;
	private $google_maps   = FALSE;
	private $extra_header  = NULL;

	/**
	 * Instance a singleton instance.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return Utils singleton instance
	 */
	public static function getInstance()
	{
		return parent::getInstance(__CLASS__);
	}

	/**
	 * Build a CMS object.
	 *
	 * @author Ángel Guzmán Maeso <angel.guzman@alu.uclm.es>
	 * @return void
	 */
	public function __construct()
	{

	}

	public function setVerticalMenu($status = TRUE)
	{
		$this->vertical_menu = $status;
	}
	
	public function setGoogleMaps($status = FALSE)
	{
		$this->google_maps = $status;
	}

	public function setJS($path)
	{
	    $this->extra_header .= $this->js($path);
	}
	
	public function setCSS($path)
	{
		$this->extra_header .= $this->css($path);
	}
	
	private function js($path = '')
	{
		return '<script type="text/javascript" src="' . $path . '"></script>';
	}

	private function css($path = '')
	{
		return '<link type="text/css" href="' . $path . '" rel="stylesheet" media="all" />';
	}

	public function openPage($title = '', $extra = '', $charset = '')
	{
		$session = Session::getInstance();
		$this->openHTML();
		$this->setHead($title, $extra, $charset);
		$this->openContent();
	}

	public function openHTML()
	{
		# Print standars XHTML 1.0 Strict
		$this->printHeaders();
    	$this->printXmlVersion();
    	$this->printDOCTYPE();
    	$this->printXHTML();
	}

	public function printHeaders()
	{
		# Print headers
		$file_last_modified = time();
    	header("Last-Modified: " . date( "r", $file_last_modified ) );
    
    	$max_age = 300 * 24 * 60 * 60; // 300 days
    	$expires = $file_last_modified + $max_age;
    	header("Expires: ".date( "r", $expires));
    
    	$etag = dechex( $file_last_modified );
    	header("ETag: ".$etag);
    
    	$cache_control = "must-revalidate, proxy-revalidate, max-age=" . $max_age . ", s-maxage=" . $max_age;
    	header("Cache-Control: ".$cache_control);
	}

	public function printXmlVersion()
	{
		# Print Xml Version
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	}

	public function printDOCTYPE()
	{
		# Print DOCTYPE XHTML 1.0 Strict
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	}

	public function printXHTML()
	{
		# Print XHTML
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"es\" lang=\"es\">\n";
	}

	function setTitle($title = '')
	{
		# Return a configurable title with game name
		$default_title = 'FreeStation Server';
	    return '<title>'.$default_title.''.(!empty($title)?' - '.$title:$title).'</title>';
	}

	function setCharset($charset = '')
	{
		# Return a meta with charset (by default utf-8)
		return '<meta http-equiv="Content-Type" content="text/html;'.(empty($charset)?'charset=utf-8':$charset).'" />';
	}

	function setRss($rss = '') 
	{ # Return html code for a not empty rss
		$default_title = 'La pulsera contra la violencia de genero ** YBS';
	    return !empty($rss)?$rss='<link rel="alternate" type="application/rss+xml" title="'.$default_title.'" href="'.$rss.'">':'';
	}

	public function setHead($title = '', $extra = '', $charset = '')
	{
		global $initJS, $gz_path, $urlhost;
		
		#<link type="text/css" href="lib/gz.php?uri=../css/defaults.css" rel="stylesheet" media="all" /> lib/gz.php?uri=../
		$baseref = $_SERVER['HTTP_HOST'];
		echo '<head>
		<base href="http://'.$baseref.'/" />' .
		$this->setCharset() .
		$this->setTitle($title) .
		$this->setRss() .
		$this->js('js/mootools-core-1.4.2.js') .
		//$this->js('js/mootools-core-1.3.1-full-nocompat.js') .
		//$this->js('js/mootools-more-1.3.1.1.js') .
		($this->vertical_menu ? $this->css('css/vertical_menu.css') : '') .
		//$this->js('js/slideitmoo-1.1-mootools-1.3.js') . 
	    '<link type="text/css" href="/css/default.css" rel="stylesheet" media="all" />
		<link href="http://freestation.quijost.com/favicon.ico" type="image/ico" rel="shortcut icon" />' . 
		$this->extra_header;
		
		#'.$initJS.'
		#'; # Default for ShadedBorder

		if($this->google_maps)
		{
		    $widget_google_maps = new WidgetGoogleMaps();
		    $widget_google_maps->render();
		}
		
		echo (!empty($extra))?"\t".$extra."\n":"\n";
		echo "</head>\n";
		#[TODO] Create a good favicon <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
	}

	public function openContent($above_menus = true, $side_menus = true)
	{
		# Open content with header
		require_once ROOT_PATH . 'inc/header.php';

    	echo '<div id="page">';
    
    	if($this->vertical_menu)
    	{
    		$widget_vertical_menu = new WidgetVerticalMenu();
    		$widget_vertical_menu->render();
    	}
    
    	echo '<div id="content">';
	}

	public function closeHTML(){
		echo '</html>';
	} #Print a tag for close html

	public function closeContent($footer_content = true)
	{
		echo '<br style="clear:both" />
		</div>
		</div>';
		# Close content with footer
		require_once ROOT_PATH . 'inc/footer.php';
		@ob_end_flush();
		@ob_flush();
		@flush();
	}

	public function closePage()
	{
		$this->closeContent();
		$this->closeHTML();
	}
}