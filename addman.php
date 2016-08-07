<?php
// Processes form submission and adds a GMO to the database

// Git .ignore excludes conf/mysql.php, because credentials don't belong in source control
include 'conf/mysql.php';
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

$man = new Manufacturer();
if ($_POST['id']) { 
    $man->id = $_POST['id'];
}
$man->name = $_POST['name']; 
$man->phone_number = $_POST['phone_number'];
$man->email = $_POST['email'];
$man->website_url = $_POST['website_url'];
$insert_id = $man->set($mysqli);

// If the query fails, we'll pass the error to the template
// This is a hacky way to do it.  The class should probably raise an exception or send back an error object.
$context['error'] = $mysqli->error;  
$context['man'] = $man;

$tpl = $mustache->loadTemplate('addman.mustache');
echo $tpl->render($context);
?>


