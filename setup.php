<?php

//ini_set('display_errors',1);  error_reporting(E_ALL);

$configfile = 'settings.ini.php';
$examplefile = 'example.ini.php';

if(isset($_GET["action"])){$action = $_GET["action"];}

if(!file_exists($filename) && !file_exists($examplefile)){
    die('You are missing the ini configuration file, please download and refresh this page');
}

if(!file_exists($configfile)){
    echo "The file $configfile does not exist, we will make a copy now...<br/><br/>";
    if (!is_writable(dirname($examplefile)))
        die('We don\'t have access to write to the current directory, please change the permissions to this directory.');
    else {
        copy($examplefile, $configfile);
        sleep(2);
        echo "<!DOCTYPE html>";
        echo "<head>";
        echo "<title>Form submitted</title>";
        echo "<script type='text/javascript'>window.parent.location.reload()</script>";
        echo "</head>";
        echo "<body></body></html>";
    }
}

try {
    $config = parse_ini_file('settings.ini.php', true);
} catch(Exception $e) {
    die('<b>Unable to read config.ini.php. Did you rename it from settings.ini.php-example?</b><br><br>Error message: ' .$e->getMessage());
}

foreach ($config as $keyname => $section) {
    
    if(($keyname == "general")) { $hash_pass = $section["password"]; }

}

$pass = isset( $_POST["pass"] ) ? $_POST["pass"] : "none" ;

$parts = explode('$', $hash_pass);
$test_hash = crypt($pass, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3]));

if(($action == "write" && $hash_pass == $test_hash)){ 
    setcookie("logged", $hash_pass, time() + (86400 * 7), "/");
    $error = "You got it dude!";
    echo "<!DOCTYPE html>";
    echo "<head>";
    echo "<title>Form submitted</title>";
    echo "<script type='text/javascript'>window.parent.location.reload()</script>";
    echo "</head>";
    echo "<body></body></html>";
}

if(isset( $_POST["pass"] ) && ($hash_pass !== $test_hash)){
    $error = "Wrong Password!";
}
    
if($_COOKIE["logged"] == $hash_pass){
    
    echo "<!DOCTYPE html>";
    echo "<head>";
    echo "<title>Form submitted</title>";
    echo "<script type='text/javascript'>window.location.replace('settings.php');</script>";
    echo "</head>";
    echo "<body></body></html>";
    
}

if($hash_pass !== $test_hash){

    echo "<center><B>Please Login to Contiune<br/><br/>";
    echo $error . "<br/>";
    echo "<style> .css-input { padding:8px; border-radius:47px; border-width:3px; border-style:double; font-size:17px; border-color:#0a090a; box-shadow: 2px 6px 8px 0px rgba(42,42,42,.75); font-weight:bold;  } 
		 .css-input:focus { outline:none; } </style>";
    echo "<form action=\"?action=write\" method='POST'>";
    echo "<b>Password: </b><input class='css-input' type='password' name='pass'></input>            ";
    echo "<input class='css-input' type='submit' name='submit' value='Go'></input>";
    echo "</form></center>";
    
}
?>