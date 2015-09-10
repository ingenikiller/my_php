<?php
$l_time = timer();
require 'ClassAutoLoader.php';

$l_classA = new ClassA();

$l_classB = new ClassB();
//$l_classD = new ClassD();

$l_time = timer() - $l_time;
echo "temps: " . $l_time . '<br>';

//require "scripts/ClassD.php";

$name = 'ClassD';
$l_args= array();
$new_class = create_function('$name, $args', 'return new $name();');
 define(strtoupper($name), NULL);
$l_class = $new_class($name, $l_args);


function timer()
{
	$time=explode(' ',microtime());
	return $time[0] + $time[1];
}

echo '<br>' . $_SERVER['SCRIPT_NAME'] . '<br>';

//echo "result:" . eregi("^[a-zA-Z]+",$_SERVER['SCRIPT_NAME'], $l_result) . '<br>';
//echo "result:" . eregi("^[/][a-zA-Z]+",'/autoload/index.php', $l_result) . '<br>';
/*echo "result:" . eregi("^/{php}$",'/autoload/index.php', $l_result) . '<br>';

echo "nb: " . count($l_result) . '<br>';
echo "chaine: " . $l_result[0] . '<br>';
foreach($l_result as $key=>$value) {
	echo "$key=>$value<br>";
}*/

 $l_chaine= split('/', $_SERVER['SCRIPT_NAME']);
 $l_chaine = $l_chaine[2];
 echo $l_chaine;
 
 

?>