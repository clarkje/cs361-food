<?php

// Git .ignore excludes conf/mysql.php, because credentials don't belong in source control
include 'conf/mysql.php';

// The GMO class
include 'gmo.php';
include 'manufacturer.php';

// Mustache Template Library (similar to Handlebars)
// Docs: https://github.com/bobthecow/mustache.php
include 'vendor/mustache/mustache/src/Mustache/Autoloader.php'; 
Mustache_Autoloader::register();

$mustache = new Mustache_Engine(array(
    'template_class_prefix' => '__MyTemplates_',
    'cache' => dirname(__FILE__).'/tmp/cache/mustache',
    'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
    'cache_lambda_templates' => true,
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
    'helpers' => array('i18n' => function($text) {
        // do something translatey here...
    }),
    'escape' => function($value) {
        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    },
    'charset' => 'ISO-8859-1',
    'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
    'strict_callables' => true,
    'pragmas' => [Mustache_Engine::PRAGMA_FILTERS],
));


//Turn on error reporting
ini_set('display_errors', 'On');

//Connects to the database
$mysqli = new mysqli($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);

if ($mysqli->connect_errno) {
	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

$gmo = New GMO();  
$gmo->m_id = $_POST['m_id']; 
$gmo->name = $_POST['name'];
$gmo->sci_name = $_POST['sci_name']; 
$gmo->description = $_POST['desc'];
$gmo->type = $_POST['type'];
$gmo->active = $_POST['active'];
$gmo->set($mysqli);

if ($mysqli->error) { 
    $contex['error'] = $mysqli->error; 
}
    
// You put whatever you want show in the template in $context
$context['gmo'] = $gmo;
$context['update'] = true;      // Hint to the UI that we want the update variant strings

$tpl = $mustache->loadTemplate('update_gmo.mustache');
echo $tpl->render($context);
?>
