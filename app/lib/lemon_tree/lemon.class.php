<?php
class Lemon
{
  private $_lemon_tree;
  private $_datas;
  
  function __construct($datas, &$lemon_tree)
  {
      $this->_datas = $datas;
      $this->_lemon_tree =& $lemon_tree;
      $this->set($datas);
  }
  
  public function __call($name, $arguments)
  {
    if(array_key_exists($name, $this->_datas))
    {
      return $this->_datas[$name];
    }
    $trace = debug_backtrace();
    trigger_error(
        'Undefined method through __call(): ' . $name .
        ' in ' . $trace[0]['file'] .
        ' at line ' . $trace[0]['line'],
        E_USER_NOTICE);
  }
  
  /**
	 * set a value or all the values	
	 *
	 * @return void
	 * @param  mixed one param passed: array of datas
	 *               two params: (string) key, (mixed) value
	 */
	public function set()
	{
		$args = func_get_args();
		$cnt  = count($args);
		if($cnt > 1)
		{
			$key = $args[0];
			$value = $args[1];
			if($key{0} != "_")
			{
				$this->_datas[$key] = $value;
				// $this->$key = $value;
			}
		}
		else if($cnt === 1)
		{
			$this->_datas = $args[0];
			// populate instance with tags
			// each tag became an instance attribute, excepted those beginin
			// with an underscore _
      // foreach($this->_datas as $key=>$value)
      // {
      //  if($key{0} != "_") $this->$key = $value;
      // }
		}
	}
  
  /**
	 * returns a value or all datas of this lemon.
	 *
	 * @return mixed  return a lemon meta value or all values (array)
	 */
	function get($key = null)
	{
		if(is_null($key))
		{
			return $this->_datas;
		}
		return $this->has($key) ? $this->_datas[$key] : null;
	}
	
	/**
	 * check if this lemon is empty
	 * A lemon is empty if it has no module value
	 *
	 * @return boolean
	 */
	
	function is_empty()
	{
		$datas = $this->_datas;
		if(array_key_exists('module',$datas)) return empty($datas['module']);
	}
	
	/**
	 * test if a lemon value is set
	 *
	 * @param string $key
	 * @return boolean
	 **/
	public function has($key)
	{
		return array_key_exists($key, $this->_datas);
	}
	
	public function children()
	{
		return $this->_lemon_tree->find_children($this->_datas['path']);
	}
	
	public function parent()
	{
		return $this->_lemon_tree->find($this->_datas['parent_path']);
	}
	
	public function previous_sibling()
	{
	  if($parent = $this->parent())
	  {
	    $siblings = $parent->children();
  	  foreach($siblings as $sibling)
  	  {
  	    if($sibling->is_same_as($this)) break;
  	    $previous = $sibling;
  	  }
  	  return $previous;
	  }
	}
	
	public function next_sibling()
	{
	  if($parent = $this->parent())
	  {
	    $siblings = $parent->children();
  	  foreach($siblings as $sibling)
  	  {
  	    if($is_next)
  	    {
  	      $next = $sibling;
  	      break;
  	    }
  	    $is_next = $sibling->is_same_as($this);
  	  }
  	  return $next;
	  }
	}
	
	
	public function file_path()
	{
	  return LemonTree::normalize_path($this->_lemon_tree->base_dir(), $this->_datas['path']);
	}
	
	public function file_content()
	{
	  $path = $this->file_path();
	  if(file_exists($path)) return file_get_contents($path);
	  trigger_error("Unable to get file content: unknown file '$path'" , E_USER_NOTICE);
	  return;
	}
	
  public function is_dir()
  {
    $path = $this->file_path();
	  return is_dir($path);
  }
  
  /**
   * Returns all datas
   *
   * @return array
   */
  public function datas()
  {
    return $this->_datas;
  }
  
  /**
   * Compares this lemon with another one and returns true if they have the same datas
   *
   * @param Lemon $lemon 
   * @return boolean
   */
  public function is_same_as($lemon)
  {
    return $this->_datas === $lemon->datas();
  }
  
  /**
   * returns the level of the lemon in the tree. if it's root, returns 0
   *
   * @return integer
   */
  public function level()
  {
    $level = 0;
    // TODO: needs implementation
    //while()
  }
  
  public function  __toString()
  {
    return "Lemon " . $this->file_path();
  }
  
}

?>