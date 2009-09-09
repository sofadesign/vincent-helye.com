<?php

class LemonIndex
{
  private $_lemon_tree;
  private $_filepath;
  private $_paths = array();
  private $_metas = array();
  
  function __construct($filepath = null, &$lemon_tree)
  {
    $this->_filepath = $filepath;
    $this->_lemon_tree =& $lemon_tree;
    $this->load($filepath);
  }
  
  public function load($filepath = null)
	{
		if(!is_null($filepath))
		{
			$this->parse(yaml($filepath));
		}
		return $this->get();
	}
	
	public function get()
	{
		// return datas
		$datas = array(
			'paths' => $this->_paths,
			'metas'	=> $this->_metas
		);
		return $datas;
	}
	
	public function paths()
	{
	  return $this->_paths;
	}
	
	public function metas()
	{
	  return $this->_metas;
	}
	
	public function parse($datas = array())
	{
		$index_ext = $this->_lemon_tree->option('index_extension');
		$base_path = str_replace($this->_lemon_tree->root_dir(), '', $this->_filepath);
		$base_path = trim($base_path, DIRECTORY_SEPARATOR);
		$base_path = str_replace($this->_lemon_tree->option('root_name'), '', $base_path);
		$base_path = str_replace('.'.$index_ext, '', $base_path);
		$base_path = LemonTree::normalize_path($base_path);
    
		foreach($datas as $k=>$e)
		{
			if(is_null($e)) continue;
			if($k[0] == $this->_lemon_tree->option('meta_token'))
			{
				
				$this->_metas[$k] = $e;
			}
			else
			{
				if(strpos($k, DIRECTORY_SEPARATOR) !== 0)
				{
					// conversion des urls relatives -> absolues
					$k = LemonTree::normalize_path($base_path, $k);
				}
				$this->_paths[$k]         = $e;
				$this->_paths[$k]['path'] = $k;
				$this->_paths[$k]['parent_path'] = $k == DIRECTORY_SEPARATOR ? null : dirname($k);
			}
		}
	}
	
	public function reset()
	{
	  $this->_paths = array();
    $this->_metas = array();
	}
}


?>