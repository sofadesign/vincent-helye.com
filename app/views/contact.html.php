<h1>Contact</h1>
      
<div id="contact">
   <address id="contact-infos">Vincent Hélye graphiste<br>
      -<br>
		16 D, rue Léon Étienne<br>
		35000 Rennes<br>
		France<br>
		-<br>
		+33 2 23 46 31 62
	</address>
 

  <form id="join-us" action="<?=url_for('/contact');?>" method="post">
    <?=html_form_token_field();?>
  	<fieldset>
  		<legend class="hidden">Nous contacter</legend>
  		<? if(isset($flash['success'])): ?>
  		  <p class="success">Votre message a bien été envoyé.</p>
  		<? else: ?>
  		  <p class="intro">N’hésitez pas à m’envoyer un message, je vous répondrais aussi vite que possible dans la mesure de ma disponibilité.</p>
  		<? endif;?>
  		<p><label>Nom<br>
  			<input type="text" size="120" id="nom" name="email[nom]" value="<?=h(v($email['nom'], ''));?>"></label></p>
  		<p><label>Adresse Email<br>
  			<input type="text" size="120" id="email" name="email[email]" value="<?=h(v($email['email'], ''));?>"></label></p>
        <p><label>Sujet<br>
  			<input type="text" size="120" id="subject" name="email[subject]" value="<?=h(v($email['subject'], ''));?>"></label></p>
  		<p><label><span class="hidden">Votre message<br></span>
  		<textarea id="message" name="email[message]" rows="8" cols="60"><?=h(v($email['message'], ''));?></textarea></label></p>
      
      <? if(isset($flash['errors'])): ?>
  		<ul>
  		  <? foreach($flash['errors'] as $error): ?>
  		    <li class="alert"><?=h($error)?></li>
  		  <? endforeach;?>
  		</ul>
  		<? endif;?>
      <p class="noseeum" style="display:none;visibility:hidden;">
      			Ne rien saisir dans le champ qui suit (à moins que vous ne soyez
      			un vilain robot):<br>
      			<input type="text" id="captcha" name="captcha" maxlength="50">
      			<br><br>
      			Ne rien saisir dans ce champs non plus:<br>
      			<textarea name="comment" rows="6" cols="60"></textarea>
      		</p>
  		<p><input type="submit" id="envoyer" 
                name="envoyer" value="send" class="bt_envoyer"/></p>
  	</fieldset>
	</form>
</div>