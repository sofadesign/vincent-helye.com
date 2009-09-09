<h1>Espace client</h1>
      
<div id="espace_client">
	<form action="<?=url_for('/espace_client/login');?>" method="post">
	<?=html_form_token_field();?>
	
	<fieldset>
		<legend class="hidden">Espace client</legend>
		<p><label>Identifiant<br />
			<input type="text" name="username" id="username" value="<?=v($flash['username'], '');?>" />
			</label></p>
		<p><label>Mot de passe<br />
			<input type="password" name="password" id="password" value="" />
			</label>
			</p>
		<? if(isset($flash['errors'])): ?>
		<ul>
		  <? foreach($flash['errors'] as $error): ?>
		    <li class="alert"><?=h($error)?></li>
		  <? endforeach;?>
		</ul>
		<? endif;?>

		<p>
			<input type="submit" name="envoyer" id="envoyer" value="Send" />
			</p>
	</fieldset>
	</form>
</div>