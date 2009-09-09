<?php
# HELPERS (used in views)

function mkd($string)
{
  return SmartyPants(Markdown($string));
}

function file_human_readable_size($file)
{
 	$size = filesize($file);
  if ($size <= 1024) return $size.' octets'; 
  else if ($size <= (1024*1024)) return sprintf('%d Ko',(int)($size/1024)); 
  else if ($size <= (1024*1024*1024)) return sprintf('%.2f Mo',($size/(1024*1024))); 
  else return sprintf('%.2f Go',($size/(1024*1024*1024)));
}

function public_url_for($path)
{
  $args = func_get_args();
  $paths = array(option('base_path'));
  $paths[] = 'public';
  $paths = array_merge($paths, $args);
  
  return call_user_func_array('file_path', $paths);
}

function html_menu_branch($lemon, $current_path)
{
  $lemons = $lemon->children();
  foreach($lemons as $lemon)
  {
    if(strpos($current_path, $lemon->path()) === 0)
    {
      $selected_path = $lemon->path();
      break;
    }
  }
  return render('_menu_branch.html.php', null, array('lemons' => $lemons, 'selected_path' => $selected_path));
}

function html_pages_nav($parent, $lemon = null)
{
  $children = $parent->children();
  if(is_null($lemon))
  {
    $lemon = $parent;
    $current_is_info = true;
  }
  $current_path = $lemon->path();
  
  $pages = array();
  $previous_path = null;
  $next_path = null;
  foreach($children as $child)
  {
    $path = $child->path();
    $pages[] = $path;
    if(is_null($next_path) && isset($next_child_path))
    {
      $next_path = $path;
      unset($next_child_path);
    }
    
    if($path == $current_path)
    {
      if(is_null($previous_path) && isset($previous_child_path))
      {
        $previous_path = $previous_child_path;
        unset($previous_child_path);
      }
      $next_child_path = true;
    }
    
    $previous_child_path = $path;
  }
  
  //if(is_null($next_path)) $next_path = $current_path;
  if(is_null($next_path)) $next_path = $current_path.'/info';
  if(is_null($previous_path))
  {
    $previous = $parent->previous_sibling();
    $previous_path = $previous ? $previous->path().'/info' : $current_path;
  }

  $info = $parent->has('description') ? $parent->description() : null;
  
  if($current_is_info)
  {
    $current_path  = $current_path.'/info';
    // $previous_path = $current_path;
    $last = array_pop($children);
    $previous_path = $last->path();
    
    $next = $parent->next_sibling();
    $next_path     = $next ? $next->path() : $current_path;
  }
  
  $options = array(
    'pages' => $pages,
    'current_path' => $current_path,
    'previous_path' => $previous_path,
    'next_path' => $next_path,
    'info' => $info
    );
    
  
  return render('_pages_nav.html.php', null, $options);
}

function html_pages_nav_item($name, $path, $current_path, $separator='-')
{
  $options = array();
  $options['name'] = $name;
  $options['path'] = $path;
  $options['separator'] = $separator;
  if($name == 'previous' || $name == 'next')
  {
    $options['class'] = $name;
    $options['name'] = $name == 'previous' ? '&lt;' : '&gt;';
  }
  else if($path === $current_path) $options['class'] = "selected";
  return render('_html_pages_nav_item_tpl', null, $options);
}

function html_page_title($title)
{
  $args = func_get_args();
  return implode(option('title_separator'), $args);
}

# INLINE TEMPLATES

function _html_pages_nav_item_tpl($vars){ extract($vars);?>
  <li<?if(!empty($class)) echo ' class="'.$class.'"';?>>
    <a href="<?=url_for($path)?>"><?=$name;?></a>
    <?if($separator):?><span class='separator'><?=h($separator)?></span><?endif;?>
  </li>
<?}
?>