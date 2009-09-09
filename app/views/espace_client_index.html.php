<h1>Espace client / Liste</h1>
      
<div id="espace_client">
   <ul>
      <? if($parent_path): ?>
      <li>[ <a href="<?=url_for($parent_path);?>">..</a> ]</li>
      <? endif; ?>
      
      <? foreach($files as $file): ?>
        <? if(is_file($file['filepath'])): ?>
        <li><a href="<?=url_for($file['path']);?>"><?=h($file['name'])?></a>
          <span class="filesize">(<?= file_human_readable_size($file['filepath'])?>)</span></li>
        <? else: ?>
        <li>[ <a href="<?=url_for($file['path']);?>"><?=h($file['name'])?></a> ]</li>
        <? endif; ?>
      <? endforeach; ?>
   </ul>

   <p class="bt logout">
      ( <a href="<?=url_for('/espace_client/logout');?>" onclick="if (confirm('Are you sure?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"> se déconnecter</a> )
   </p>
</div>