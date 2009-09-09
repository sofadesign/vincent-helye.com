<?php
require '../app/lib/limonade.php';

function configure()
{
  # A. Limonade default options
  $localhost = preg_match('/^localhost(\:\d+)?/', $_SERVER['HTTP_HOST']);
  $env =  $localhost ? ENV_DEVELOPMENT : ENV_PRODUCTION;
  option('env', $env);
  //option('base_path', $env == ENV_DEVELOPMENT ? '/vincent-helye.com/www-2' : '/www-2');
  if($env == ENV_PRODUCTION) option('base_uri', '/');

  option('http_cache', $env == ENV_PRODUCTION);
  
  # B. Helye options
  option('app_dir', file_path(dirname(option('root_dir')), 'app'));
  option('lib_dir', file_path(option('app_dir'), 'lib'));
  option('views_dir', file_path(option('app_dir'), 'views'));
  option('controllers_dir', file_path(option('app_dir'), 'controllers'));
  $datas_dir = file_path(option('app_dir'), 'datas');
  option('lemon_tree_root',         file_path($datas_dir, 'public'));
  option('private_lemon_tree_root', file_path($datas_dir, 'private'));
  option('lemon_tree_root_name',    'home');
  option('auth_config',             file_path(option('app_dir'), 'config', 'users.php'));
	option('title_separator',         ' // ');
	
	$email_options = array();
  $email_options['to'] = $env == ENV_PRODUCTION ? 'info@vincent-helye.com' : 'fabrice.luraine@sofa-design.net';
	$email_options['subject'] = "[vincent-helye.com]: %s (message envoyé par %s)";
	if($env == ENV_DEVELOPMENT)
	{
	  require_once "Mail.php";
	  $email_options['smtp'] = Mail::factory( 'smtp',
	                                          array('host' => 'smtp.sfr.fr', 
	                                                'port' => 587));
	}
	option('email_options', $email_options);
	
	# C. Other settings
	setlocale(LC_TIME, "fr_FR");
	define( 'MARKDOWN_EMPTY_ELEMENT_SUFFIX',  ">");
	
}

function before()
{
  layout('layouts/default.html.php');
  $lemon_tree = _helye_lemon_tree();
  set('portfolio_lemon', $lemon_tree->find('/portfolio'));
  set('lab_lemon',       $lemon_tree->find('/lab'));
  set('site_title',      $lemon_tree->title);
}

function not_found($errno, $errstr, $errfile=null, $errline=null)
{
  $html = '<p><img src="'
        . public_url_for('/img/error-404.jpg')
        . '" alt="Erreur 404: '
        . h($errstr)
        . '"></p>';
  set('page_title', 'Error 404');
  return html($html);
}

function server_error($errno, $errstr, $errfile=null, $errline=null)
{
  $html = '<p><img src="'
        . public_url_for('/img/error-500.jpg')
        . '" alt="Erreur 500 (SERVER_ERROR)"></p>';
  set('page_title', 'Error 500 (SERVER_ERROR)');
  return html($html);
}



# GET /
dispatch('/', 'helye_home');
  function helye_home()
  {
    $lemon_tree = _helye_lemon_tree();
    if($lemon = $lemon_tree->find('/random'))
    {      
      $files       = file_list_dir($lemon->file_path());
      $path        = $files[array_rand($files)];
      $url         = LemonTree::normalize_path('files', $lemon->path(), $path);
      $image_infos = getimagesize(file_path($lemon->file_path(), $path));
      $image       = array( 'url'  => $url, 
                            'attr' => $image_infos[3] );      
      set('image', $image);
    }
    if($infos = $lemon_tree->find('/infos.txt')) set('infos', $infos->file_content());
    if($news  = $lemon_tree->find('/news.txt'))  set('news',  $news->file_content());
    set('page_title', $lemon_tree->find('/')->title());
    set('page_class', 'home');
    
    return html('home.html.php');
  }

# GET /links
dispatch('/links', 'helye_links');
  function helye_links()
  {
    $lemon_tree = _helye_lemon_tree();
    if($lemon = $lemon_tree->find('/liens.txt'))
    {
      set('lemon',      $lemon);
      set('page_title', $lemon->title());
      set('page_class', 'links');
      return html('links.html.php');
    }
    halt(NOT_FOUND);
  }
  
dispatch('/contact', 'helye_contact');
  function helye_contact()
  {
    if(isset($_SESSION['email']))
    {
      set('email', $_SESSION['email']);
      unset($_SESSION['email']);
    }
    $lemon_tree = _helye_lemon_tree();
    set('page_title', $lemon_tree->find('/contact')->title());
    return html('contact.html.php');
  }
dispatch_post('/contact', 'helye_contact_post');
  function helye_contact_post()
  {
    if(lemon_csrf_require_valid_token('"Cross site request forgery" détectée. Requête interrompue.'))
    {
      $errors = _helye_contact_email_is_valid($_POST['email']);
      if(empty($errors))
      {
        $email = _helye_contact_email_sanitize($_POST['email']);
        if(_helye_contact_email_send($email))
        {
          flash('success', true);
        }
        else
        {
          $errors[] = "Une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer ultérieurement.";
        }
      }
      else
      {
        $_SESSION['email'] = $_POST['email'];
      }
      flash('errors', $errors);
      redirect_to('/contact');
    }
  }
  
  function _helye_contact_email_is_valid($email)
  {
    $errors = array();
    if(!empty($_POST['captcha']) || !empty($_POST['comment'])) $errors[] = "No spam please...";
    if(!filter_var($email['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Veuillez saisir une adresse email valide.";
    if(empty($email['nom'])) $errors[] = "Veuillez saisir votre nom.";
    if(empty($email['message'])) $errors[] = "Veuillez saisir votre message.";
    
    return $errors;
  }
  
  function _helye_contact_email_sanitize($email)
  {
    $email['email'] = filter_var($email['email'], FILTER_SANITIZE_EMAIL);
    $email['nom'] = filter_var($email['nom'], FILTER_SANITIZE_STRING);
    $email['message'] = filter_var($email['message'], FILTER_SANITIZE_STRING);
    return $email;
  }
  
  function _helye_contact_email_send($email)
  {
    extract(option('email_options'));
    $subject = sprintf($subject, $email['subject'], $email['email']);
    $headers = array();
    $headers['MIME-Version'] = "1.0";
  	$headers['Content-type'] = "text/html; charset=UTF-8";
  	$headers['From'] = $email['email'];
  	$headers['To'] = $to;
		$headers['Return-Path'] = $to;
		$headers['X-Mailer'] = 'PHP/' . phpversion();
		$headers['X-Limonade'] = LIM_SESSION_NAME . ' / '.LIMONADE;
		$headers['Subject'] = $subject;
		$body = render('contact_email.html.php', null, array('email' => $email));
		if(isset($smtp))
		{
		  $sended = $smtp->send($to, $headers, $body);
		  return !PEAR::isError($sended);
		}
		else
		{
		  unset($headers['Subject']); // pb with shared hosts, avoid duplicated subject
		  $headers_lines = array();
		  foreach($headers as $k=>$v) $headers_lines[] = $k.": ".$v;
		  return mail($to, $subject, $body, implode("\r\n",$headers_lines));
		}
    
  }
  

# GET /espace_client/login
dispatch('/espace_client/login', 'helye_espace_client_ask_login');
  function helye_espace_client_ask_login()
  {
    if(isset($_SESSION['username'])) redirect_to('/espace_client');
    lemon_csrf_unset_token();
    $lemon_tree = _helye_lemon_tree();
    if($lemon = $lemon_tree->find('/espace_client'))
    {
      set('page_title', $lemon->title());
      set('page_class', 'espace_client');
    }
    return html('espace_client_access.html.php');
  }

# POST /espace_client/login  
dispatch_post('/espace_client/login', 'helye_espace_client_login');
  function helye_espace_client_login()
  {
    if(lemon_csrf_require_valid_token('"Cross site request forgery" détectée. Requête interrompue.'))
    {
      $errors = array();
      if($token_expired = lemon_csrf_token_expired())
      {
        $errors[] = 'Temps de connexion dépassé. Merci de vous bien vouloir vous identifier à nouveau.';
      }
      lemon_csrf_unset_token();
      if(!$token_expired)
      {
        $auth = lemon_auth(option('auth_config'));
        // redirect to '/espace_client' if login successfuly
        $errors = array_merge(lemon_auth_login($_POST['username'], $_POST['password'], '/espace_client'), $errors);
      }
      // else
      unset($_SESSION['username']);
      flash('errors', $errors);
      flash('username', $_POST['username']);
      redirect_to('/espace_client/login');
    }
  }

# DELETE /espace_client/logout
dispatch_delete('/espace_client/logout', 'helye_espace_client_logout');
  function helye_espace_client_logout()
  {
    lemon_auth_logout();
    redirect_to('/espace_client/login');
  }
  
# GET /espace_client/
# GET /espace_client/username/file.ext
dispatch('/espace_client/**', 'helye_espace_client');
  function helye_espace_client()
  {
    $auth = lemon_auth(option('auth_config'));
    if($username = lemon_auth_logged_in())
    {
      $is_admin = lemon_auth_is_admin($username);

      $path = params(0);
      $base_path = option('private_lemon_tree_root');

      if(!$is_admin && !preg_match("/^$username/", $path))
      {
        redirect_to('/espace_client', $username);
        exit;
      }
      $folder_path =  file_path($base_path, $path);

      if(file_exists($folder_path))
      {
        $root_path   = $is_admin ? file_path('') : file_path($username);
        if($path != $root_path)
        {
          $dirs = explode('/', $path);
          array_pop($dirs);
          $parent_path = file_path('espace_client', implode('/', $dirs));
        }

        $files = array();

        foreach($names = file_list_dir($folder_path) as $name)
        {
          $file = array();
          $file['name'] = $name;
          $file['filepath'] = file_path($folder_path, $name);
          $file['path'] = is_dir($file['filepath']) ? 
                            file_path('espace_client', $path, $name) : 
                            file_path('files', 'espace_client', $path, $name);
          $files[] = $file;
        }
        $lemon_tree = _helye_lemon_tree();
        if($lemon = $lemon_tree->find('/espace_client'))
        {
          set('page_title', $lemon->title());
          set('page_class', 'espace_client');
        }
        
        set('parent_path', $parent_path);
        set('files', $files);
        return html('espace_client_index.html.php'); 
      }
      halt(NOT_FOUND);
    }
    redirect_to('/espace_client/login');
  }

# GET /files/espace_client/username/file.ext
dispatch('/files/espace_client/**', 'helye_espace_client_files');
  function helye_espace_client_files()
  {
    $auth = lemon_auth(option('auth_config'));
    if($username = lemon_auth_logged_in())
    {
      $is_admin = lemon_auth_is_admin($username);
      $path = rawurldecode(params(0));
      if(!$is_admin && !preg_match("/^$username/", $path)) halt(HTTP_FORBIDDEN);

      return _render_file_with_http_cache(file_path(option('private_lemon_tree_root'), $path));
    }
    redirect_to('/espace_client/login');
  }

# GET /files/any/public/file.ext  
dispatch('/files/**', 'helye_public_lemons_files');
  function helye_public_lemons_files()
  {
    $params     = params();
    if(empty($params[0])) halt(NOT_FOUND);
    $lemon_tree = _helye_lemon_tree();
    $path       = $lemon_tree->file_path(rawurldecode($params[0]));
    _render_file_with_http_cache($path);
  }

# GET /any/public/lemon/info  
dispatch('/**/info', 'helye_lemons_info');
  function helye_lemons_info()
  {
    return helye_lemons(true);
  }

# GET /any/public/lemon  
dispatch('/**', 'helye_lemons');
  function helye_lemons($info = false)
  {  
    $lemon_tree = _helye_lemon_tree();
    $params     = params();
    $path       = '/'.$params[0];
    if($lemon = $lemon_tree->find($path))
    {
      $c = $lemon->children();
      if(empty($c) && $info)
      {
        return redirect_to($lemon->parent_path().'/info');
      }
      else if(!empty($c) && !$info)
      {
        $first_lemon = $c[0];
        $new_path    = $first_lemon->path();
        return redirect_to($new_path);
      }

      set('lemon',        $lemon);
      set('current_path', $path);
      set('page_title',   _helye_lemon_title($lemon));
      set('page_class',   array_shift(explode('/', substr($path,1))));
      
      if($info)
      {
        $description = $lemon->description();
        set('description', $description);
        return html('portfolio_info.html.php');
      }
      
      return html('portfolio_show.html.php');
    }
    halt(NOT_FOUND);
  }
run();

# PRIVATE

function _helye_lemon_tree()
{
  $root = option('lemon_tree_root');
  return new LemonTree($root);
}

function _helye_private_lemon_tree()
{
  $root = option('private_lemon_tree_root');
  return new LemonTree($root);
}

function _helye_lemon_title($lemon)
{
  $title = array();
  while($lemon->parent_path() != '/')
  {
    if($lemon->has('title')) $title[] = $lemon->title();
    $lemon = $lemon->parent();
  }
  return implode(option('title_separator'), array_reverse($title));
}

function _render_file_with_http_cache($path)
{
  if(file_exists($path))
  {
    if(option('http_cache'))
    {
      $modified_timestamp = filemtime($path);
			if($modified_timestamp) http_cache($modified_timestamp);
    }
    render_file($path);
  }
  else halt(NOT_FOUND);
}

?>