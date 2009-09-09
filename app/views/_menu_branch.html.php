
          <ul><? foreach($lemons as $lemon): ?>
          
            <li<?if($lemon->path() === $selected_path):?> class="selected"<?endif;?>>
              <a href="<?=url_for($lemon->path())?>"><?=h($lemon->title())?></a></li>
          <? endforeach; ?></ul>
