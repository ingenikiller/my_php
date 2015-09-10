<?php
$l_time = timer();
//require 'ClassAutoLoader.php';

include 'classes/ClassA.php';
include 'classes/ClassB.php';
include 'scripts/ClassD.php';

$l_classA = new ClassA();

$l_classB = new ClassB();
//$l_classD = new ClassD();

$l_time = timer() - $l_time;
echo "temps: " . $l_time;


function timer()
{
	$time=explode(' ',microtime());
	return $time[0] + $time[1];
}

?>