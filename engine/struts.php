<?php
/**
 * strutsEngine Templating Engine Class
 *
 * A simplistic PHP Templating engine that seperates all code from design.
 * 
 * This class provides a templating engine to easily share 1 template over multiple PHP files
 * In your page files put a ##variable name##,  and set the variable in the php file
 * This class will recursively replace all ##var## in a HTML file with the variable provided in PHP.
 * 
 * @package default
 * @author Technoguru Aka. Johnathan Pulos  (mailto:johnathan@jpulos.com)
 **/
class strutsEngine
{
	/**
	 * set the required variables for the Struts Engine
	 *
	 * @var	array 	$layoutVars 	The array of variables for the layout
	 * @var	array 	$pageVars		The array of variables for the page
	 * @var	string	$strutContents	The content for the specific page
	 * @var	string	$strutTemplate	The final layout that will be rendered
	 * @access	private
	 * 
	 **/
	private $layoutVars = array();
	private $pageVars = array();
	private $strutContents = '';
	private $strutTemplate = '';
	/**
	 * set the required variables for the Struts Engine
	 *
	 * @var	string	$documentRoot The document root for the site
	 * @var	string	$jsFormat The HTML format for including javascript files in sprint_f() format
	 * @var	string	$cssFormat The HTML format for including CSS files in sprint_f() format
	 * @var	string	$compressedCssFileName The file name for the compressed css file
	 * @access	public
	 * 
	 **/
	public $documentRoot = '';
	public $jsFormat = "<script type=\"text/javascript\" charset=\"utf-8\" src=\"%s\"></script>\r\n";
	public $cssFormat = "<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\" />\r\n";
	public $compressedCssFileName = 'styles.min.css';
	
	/**
	 * Construct the class
	 */
	function __construct ()
	{
		$this->documentRoot = ((isset($_SERVER)) && (isset($_SERVER['DOCUMENT_ROOT']))) ? $_SERVER['DOCUMENT_ROOT'] : '';
	}
	
	/**
	 * Sets an individual variable for the layout file
	 *
	 * @var	string	$tag	reference of the variable (##variable##)
	 * @var	string	$value	the value you want to set the variable to	
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutVar($tag, $value)
	{
		$this->layoutVars = array_merge($this->layoutVars,array($tag => $value));
		return true;
	}
	
	/**
	 * Sets multiple variables from an array for the layout file
	 *
	 * @var	array 	$layout_vars	an array of variables set up like array($tag => $value)	
	 * @var	string	$prefix			prefix to append to all references of the variable (Helps to protect from overwriting other variables)
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutVarFromArray($layout_vars, $prefix = '')
	{
		foreach($layout_vars as $key => $val)
		{
			$title = $prefix . "" . $key;
			$this->layoutVars = array_merge($this->layoutVars,array($title => $val));
		}
		return true;
	}
	
	/**
	 * Sets the ##strutJavascript## from an array of javascript filenames
	 * 
	 * @var	string	$js_files			an array of javascript files with extension
	 * @var	string	$directory			optional directory for files
	 * @var	Boolean	$compress			should we set the url to a compression file ($js_compress_dir/$cache_js/$files_to_compress (commas seperated))
	 * @var	String	$js_compress_dir	location of the javascript compression file
	 * @var	Boolean	$cache_js			Do you want the Javascript files cached	
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutJavascriptFromArray($js_files, $directory = null, $compress = null, $js_compress_dir = null, $cache_js = false)
	{
		$page_javascript = '';
		$files_to_compress = '';
		if(!empty($js_files)){
			foreach($js_files as $key => $val)
			{
				if($compress === true){
					$files_to_compress .= $val . ',';
				}else{
					$page_javascript .= sprintf($this->jsFormat, $directory . '/' . $val);
				}
			}
			if($compress === true){
				$files_to_compress = substr($files_to_compress,0,-1);
				$cache_js = ($cache_js === true)? 1 : 0;
				$page_javascript = sprintf($this->jsFormat, $js_compress_dir . '/' . $cache_js . '/' . $files_to_compress);
			}
		}else{
			$page_javascript = '';
		}
		$this->setLayoutVar("strutJavascript", $page_javascript);
		return true;
	}
	
	/**
	 * Sets the ##strutCSS## from an array of CSS filenames
	 * 
	 * @var	string	$css_files			an array of CSS files with extension
	 * @var	string	$directory			optional directory for files
	 * @var	Boolean	$compress			should we set the url to a compression file ($css_compress_dir/$cache_css/$files_to_compress (commas seperated))
	 * @var	String	$css_compress_dir	location of the CSS compression file
	 * @var	Boolean	$cache_css			Do you want the CSS files cached	
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutCSSFromArray($css_files, $css_compress_files = null, $directory = '/', $cache_css = false)
	{
		$page_css = '';
		$files_to_compress = '';
		if(!empty($css_files)){
			foreach($css_files as $key => $val)
			{
				$page_css .= sprintf($this->cssFormat, $directory . '/' . $val);
			}
		}else{
			$page_css = '';
		}
		if(!empty($css_compress_files)){
			$page_css .= sprintf($this->cssFormat, $directory . '/' . $this->compressedCssFileName);
			$new_file_contents = '';
			foreach($css_compress_files as $key => $val)
			{
				if(strstr($val, 'http://')){
					if(file_exists($val)){
						$new_file_contents .= file_get_contents($val);	
					}
				}else{
					echo file_exists($directory . '/' . $val);
					if(file_exists(realpath($this->documentRoot . '/') . $directory . '/' . $val)){
						
						$new_file_contents .= file_get_contents($this->cssFormat, realpath($this->documentRoot . '/') . $directory . '/' . $val);
						echo file_get_contents($this->cssFormat, realpath($this->documentRoot . '/') . $directory . '/' . $val);
					}
				}
			}
		}
		exit;
		$this->setLayoutVar("strutCSS", $page_css);
		return true;
	}
	
	/**
	 * A debug function to print out the current set layout variables
	 * 
	 * This function exits code, and displays all the current layout variables set up to where this function is called.
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function printlayoutVars()
	{
		foreach($this->layoutVars as $key => $val)
		{
			echo $key . " = " . $val . "<br />";
		}
		exit();
		return true;
	}
	
	/**
	 * Unsets all layout variables in $this->layoutVars
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function emptyLayoutVars()
	{
		unset($this->layoutVars);
		$this->layoutVars = array();
		return true;
	}
	
	/**
	 * Set an individual variable for the page design file
	 * 
	 * All variables can be displayed by adding ##tag## in the page design file.
	 *
	 * @var string $tag reference for this specific variable
	 * @var string $value value for this variable
	 * 
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageVar($tag, $value)
	{
		$this->pageVars = array_merge($this->pageVars,array($tag => $value));
		return true;
	}
	
	/**
	 * Sets multiple variables for a page design file based on a supplied array formated like array($tag => $value)
	 *
	 * @var	array 	$page_vars	An array of page variables
	 * @var	string	$prefix		prefix to append to all references of the variable (Helps to protect from overwriting other variables)
	 * 
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageVarFromArray($page_vars, $prefix)
	{
		foreach($page_vars as $key => $val)
		{
			$title = $prefix . "" . $key;
			$this->pageVars = array_merge($this->pageVars,array($title => $val));
		}
		return true;
	}
	
	/**
	 * A debug function to print out the current set page variables
	 * 
	 * This function exits code, and displays all the current page specific variables set up to where this function is called.
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function printPageVars()
	{
		foreach($this->pageVars as $key => $val)
		{
			echo $key . " = " . $val . "<br />";
		}
		exit();
		return true;
	}
	
	/**
	 * Unsets all page specific variables in $this->pageVars
	 *
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function emptyPageVars()
	{
		unset($this->pageVars);
		$this->pageVars = array();
		return true;
	}
	
	/**
	 * Get the page content, and parse it.
	 * 
	 * Searches the supplied page design file, and replaces all ##tag## with the set variable in $this->pageVars
	 * then it puts the content into ##strutContent## layout variable.
	 *
	 * @var	string	$file	The page specific design file
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setPageElement($file)
	{
		$file_code = $this->prepareFile($file);
		$this->strutContents .= $this->replaceAllVars($this->pageVars, $file_code);
		$this->setLayoutVar("strutContent", $this->strutContents);
		return true;
	}
	
	/**
	 * Get the layout content, and parse it.
	 * 
	 * Searches the supplied layout design file, and replaces all ##tag## with the set variable in $this->layoutVars
	 * then it puts the content into $this->strutTemplate for final displaying.
	 *
	 * @var	string	$file	The page specific design file
	 * @return Boolean
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function setLayoutElement($file)
	{
		$file_code = $this->prepareFile($file);
		$this->strutTemplate .= $this->replaceAllVars($this->layoutVars, $file_code);
		return true;
	}
	
	/**
	 * render the final layout
	 *
	 * @return string $this->strutTemplate the final layout with all tags replaced with the variable values
	 * 
	 * @access	public
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	public function renderLayout()
	{
		return $this->strutTemplate;
	}
	
	/**
	 * Prepares the given file for variable replacment
	 *
	 * @var	string	$file	the file to prepare
	 * @return string	the prepared file
	 * 
	 * @access	private	
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	private function prepareFile($file)
	{
		return implode(file($file),'');
	}
	
	/**
	 * Replaces the tags with the variable values
	 * 
	 * This function itterates through the variables and finds them based on ##tag## in the file.
	 * Then it replaces the tag with the value.  This is the core of the templating engine.
	 *
	 * @var	array 	$vars		The variables for the specific file setup like array($tag => $value)
	 * @var	string	$file_code	The prepared file code
	 * 
	 * @return string	the file_code with all variables set to the correct values
	 * 
	 * @access private
	 * @author Technoguru Aka. Johnathan Pulos
	 **/
	private function replaceAllVars($vars, $file_code)
	{
		if(!empty($vars)){
		    foreach($vars as $key => $val)
			{
				$file_code = str_replace("##$key##",$val,$file_code);
			}		
		}
		return $file_code;
	}
	
}// END class 
?>