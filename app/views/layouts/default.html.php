<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang='fr'>
  <head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-type'>
    <title>
      <?= html_page_title($site_title, $page_title) ?>
    </title>
    <link href='<?=public_url_for('/css/blueprint/screen.css')?>' media='screen, projection' rel='stylesheet' type='text/css'>
    <link href='<?=public_url_for('/css/blueprint/print.css')?>' media='print, projection' rel='stylesheet' type='text/css'>
    <!--[if lt IE 8]> 
      <link href='<?=public_url_for('/css/blueprint/ie.css')?>' media='screen, projection' rel='stylesheet' type='text/css'>
    <![endif]-->
    <link href='<?=public_url_for('/css/screen.css')?>' media='screen, projection' rel='stylesheet' type='text/css'>
    <meta content='IE=8' http-equiv='X-UA-Compatible'>
    <meta content='Powered by Limonade (http://limonade.sofa-design.net), Edited with TextMate (http://macromates.com/), Prototyped with Webby (http://webby.rubyforge.org)' name='generator'>
    <meta content='15 days' name='revisit-after'>
    <meta content='index,follow' name='robots'>
    
    <meta name="keywords" content="designer,graphic designer,graphisme rennes">
    <meta name="description" content="">
    
    <script src='<?=public_url_for('/js/jquery.min.js')?>' type='text/javascript'></script>
    <script src='<?=public_url_for('/js/jquery.cookie.js')?>' type='text/javascript'></script>
    <script src='<?=public_url_for('/js/jquery.helyenav.js')?>' type='text/javascript'></script>
    <script src='<?=public_url_for('/js/letsgo.js')?>' type='text/javascript'></script>
    <link rel="shortcut icon" type="image/x-icon" href="<?=public_url_for('/favicon.ico')?>">
    <link rel="icon" type="image/png" href="<?=public_url_for('/favicon.png')?>">
    
  </head>
  <body class='<?=$page_class;?>'>
    <div id='nav'>
      <ul class='main'>
        <li class='home'>
          <h2>
            <a href='<?=url_for('/');?>'>Vincent Hélye</a>
          </h2>
        </li>
        <li class='portfolio'>
          <h2>
            <a href='<?=url_for('/portfolio/');?>'>Portfolio</a>
          </h2>
          <?=html_menu_branch($portfolio_lemon, $current_path);?>
        </li>
        <li class='lab'>
          <h2>
            <a href='<?=url_for('/lab');?>'>Lab</a>
          </h2>
          <?=html_menu_branch($lab_lemon, $current_path);?>
        </li>
        <li class='links'>
          <h2>
            <a href='<?=url_for('/links/');?>'>Links</a>
          </h2>
        </li>
        <li class='espace_client'>
          <h2>
            <a href='<?=url_for('/espace_client/');?>'>Espace client</a>
          </h2>
        </li>
        <li class='contact'>
          <h2>
            <a href='<?=url_for('/contact/');?>'>Contact</a>
          </h2>
        </li>
      </ul>
      <? if(!empty($infos) || !empty($news)): ?>
      <div id='home'>
        <? if(!empty($infos)): ?>
        <p id='about'>
          <?=nl2br($infos);?>
        </p>
        <? endif; ?>
        <? if(!empty($news)): ?>
        <div id='news'>
          <?=mkd($news);?>
        </div>
        <? endif; ?>
      </div>
      <? endif; ?>
    </div>
    <div id='content'>
      <?= $content; ?>
    </div>
    
    
    
    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
    var pageTracker = _gat._getTracker("UA-13047420-1");
    pageTracker._trackPageview();
    } catch(err) {}</script>
    
  </body>
</html>