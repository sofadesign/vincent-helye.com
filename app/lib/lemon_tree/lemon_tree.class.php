<?php
/**
* 
*/

if(!function_exists('file_path'))
{
  /**
   * Creates a file path by concatenation of given arguments
   *
   * @param string $path, ... 
   * @return string normalized path
   */
  function file_path($path)
  {
    $args = func_get_args();
    $ds = DIRECTORY_SEPARATOR;
    $n_path = count($args) > 1 ? implode($ds, $args) : $path;
    $n_path = preg_replace( '/'.preg_quote($ds, $ds).'{2,}'.'/', 
                            $ds, 
                            $n_path);
    return $n_path;
  }
}


class LemonTree
{
  static public $default_options = array(
    'meta_token' => '@',
    'index_extension' => 'yml',
    'default_module' => 'default',
    'root_name' => 'home'
    );
  
  private $_root_dir;
  private $_options;
  private $_indexes = array();
	private $_datas   = array();
	private $_metas   = array();
	private $_caches  = array();
  
  function __construct($root_dir, $options=array())
  {
    $this->_root_dir = $root_dir;
    $this->_options = array_merge(LemonTree::$default_options, $options);
  }
  
  /**
	 * Find a lemon and returns it if exists
	 * If it's an alias, take alias target resource, overloading it with it's own values
	 * If it's DIRECTORY_SEPARATOR, take root as alias target, overloading it with it's own values
	 * If it's root, find DIRECTORY_SEPARATOR
	 *
	 * @param string $path 
	 * @return Lemon, false
	 */
	
	public function find($path)
	{
		$ds = DIRECTORY_SEPARATOR;
		$path = self::normalize_path($path);
		
		$root_name = $this->_options['root_name'];
		// ROOT SPECIAL CASE
		if($path == $ds.$root_name) return $this->find($ds);

		$this->_load_indexes($path);

		if(array_key_exists($path, $this->_datas))
		{
			$datas = $this->_datas[$path];
			// ROOT CASE
			
			if($path == $ds)
			{
				$alias = new Lemon($this->_datas[$ds.$root_name], $this);
			}
			else if(isset($datas['alias']))
			{
				// ALIAS CASE
				$alias = $this->find($datas['alias']);
			}
			
			if(isset($alias))
			{
				foreach($datas as $k=>$v)
				{
					if($k != 'alias') $alias->set($k, $v);
				}
				return $alias;
			}
			else
			{
				return new Lemon($this->_datas[$path], $this);
			}
		}
		
		return false;
	}
  
  /**
	 * returns an array of lemons children for a given path
	 *
	 * @param string $path 
	 * @return Lemon
	 */
	function find_children($path)
	{
		$path = self::normalize_path($path);
		$children = array();
		foreach($this->_datas as $p=>$lemon)
		{
			if($lemon['parent_path'] == $path && $lemon['path'] != $path) $children[] = new Lemon($this->_datas[$p], $this);
		}
		return $children;
	}
  
  /**
	 * return the module name of the lemon for a given path
	 *
	 * @param string $path 
	 * @return string
	 */
	
	public function find_module($path)
	{
		// $ds = DIRECTORY_SEPARATOR;
		//    if(!empty($path))
		//    {
		//      if($path{0} != $ds) $path = $ds.$path;
		//    }
		//    $path = explode($ds, $path);
		//    $current = implode($ds, $path);
		$ds = DIRECTORY_SEPARATOR;
		$current = $path = self::normalize_path($path);
		$path_elts = explode($ds, $path);
		while(!empty($path_elts))
		{
			if(array_key_exists($current, $this->_datas))
			{
				
				return $this->_datas[$current]['module'];
			}
			else
			{
				array_pop($path_elts);
				$current = implode($ds, $path_elts);
				
			}
		}
		return false;
	}
  
  public function root_dir()
  {
    return $this->_root_dir;
  }
  
  public function base_dir()
  {
    return self::normalize_path($this->_root_dir, $this->_options['root_name']);
  }
  
  public function option($name)
  {
    return array_key_exists($name, $this->_options) ? $this->_options[$name] : null;
  }
  
  /**
   * returns the filepath for a given lemon $path in the tree
   *
   * @param string $path 
   * @return void
   */
  public function file_path($path)
  {
    return self::normalize_path($this->base_dir(), $path);
  }
  
  /**
   * build and returns a normal path: starting with a / but not ending with
   *
   * @param string $path,... 
   * @return string normalized path
   */
  public static function normalize_path($path)
  {
    $ds = DIRECTORY_SEPARATOR;
    $args = func_get_args();
    return $ds.trim(call_user_func_array('file_path', $args), $ds);
  }
  
  
  
  
  /**
   * _PRIVATE METHODS___________________________________________________________
   */
  
  private function _load_indexes($path)
	{
		$index_ext  = $this->_options['index_extension']; 
		$index_dir  = $this->_root_dir;
		$root_name  = $this->_options['root_name'];
		$path       = self::normalize_path($path);
		if($path == DIRECTORY_SEPARATOR)
		{
			$path = $root_name;
			$dirs = array($path);
		}
		else
		{
			$path = substr($path,1);
			$dirs = empty($path) ? array() : explode(DIRECTORY_SEPARATOR, $path);
			if($dirs[0] != $root_name ) array_unshift($dirs, $root_name);
		}

		$root = true;
		while($dir = current($dirs))
		{
			if($root)
			{
				//$index = $index_dir.DIRECTORY_SEPARATOR.$root_name.".".$index_ext;
				$index = self::normalize_path($index_dir, $root_name.".".$index_ext);
				$root = false;
			}
			else
			{
				$index_dir = str_replace('.'.$index_ext, '', $index).DIRECTORY_SEPARATOR;
				$index     = $index_dir.$dir.".".$index_ext;
			}
			if(!array_key_exists($index, $this->_caches))
			{
				$this->_load_index($index);
				$this->_caches[$index] = true;
			}
			
			next($dirs);
		}
	}
	
	private function _load_index($index_path = null)
	{
		if(!is_null($index_path) || file_exists($index_path))
		{
			$default_module = $this->_options['default_module'];
			$index = new LemonIndex($index_path, $this);
			$this->_indexes[] = $index;
			$index_paths = $index->paths();
			foreach($index_paths as $k=>$e)
			{
				// save index data in $this->_datas
				// merge if necessary with existing data
				$this->_merge_datas($this->_datas, $k, $e);
				// complete module value for each node
				if(!array_key_exists('module', $this->_datas[$k])){
					$parent_path = $this->_datas[$k]['parent_path'];
					if(array_key_exists($parent_path, $this->_datas)){
						$parent = $this->_datas[$parent_path];
						$module = empty($parent['module']) ?  $default_module : $parent['module'];
					} else {
						$module = $default_module;
					}
					$this->_datas[$k]['module'] = $module;
				}
			}
			// save metas (merge if necessary)
			foreach($index->metas() as $k=>$e)
			{
				if($k{0} != "_")
				{
					$k = substr($k,1);
					$this->$k = $e;
				}
				$this->_merge_datas($this->_metas, $k, $e);
			}
			return true;
		}
		return false;
	}
	
	private function _merge_datas(&$array, $key, $value)
	{
		if(array_key_exists($key, $array)){
			$c_entry = $array[$key];
			unset($array[$key]);
			if(is_array($c_entry) && is_array($value)){
				$array[$key] = array_merge_recursive($c_entry, $value);
			} else {
				$array[$key] = $value;
			}
		} else {
			$array[$key] = $value;
		}
	}
}

?>