
<? if(count($pages) > 0): ?><ul id='pages-nav'>
  <?php
    echo html_pages_nav_item('previous', $previous_path, $current_path);
    echo html_pages_nav_item('next', $next_path, $current_path);
  
  
    for($i=0, $cnt=count($pages); $i<$cnt; $i++)
    {
      $path = $pages[$i];
      $name = sprintf("%02s", $i + 1);
      echo html_pages_nav_item($name , $path, $current_path);
    }
    
    if($info) echo html_pages_nav_item('Info', $lemon->path().'/info', $current_path, null); 
  ?>
</ul><? endif; ?>
