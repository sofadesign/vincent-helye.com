<?php
if(!defined('LIMONADE')){$h="HTTP/1.0 401 Unauthorized";header($h);die($h);}// Security check

/**
 * Users definitions
 *
 * @author Fabrice Luraine
 * @version $Id$
 * @copyright Sofa Design, 16 mars, 2007
 * @package limonade
 **/

$users = array();
$admin_users = array();

//
// Add a line to add a new user:
// 
// 	$users['username'] = 'password';
// 
//	(replace username and pasword with the required values.)
//
//  !!! Username and passwword must be only alphanumeric characters       !!!
//  !!! Username and passwword must be between 3 and 30 characters length !!!
//
$users['vincent']      = 'test';
$users['test']         = 'test';
$users['fabrice']      = 'test';



//
// Following lines are registering users with admin rights.
// Admin users can access all protected ressources.
//
$admin_users[] = 'vincent';
$admin_users[] = 'fabrice';

?>