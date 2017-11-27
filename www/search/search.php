<?php

include_once('manager.php');

$manager = new SearchManager('../../config.ini');

echo($manager->run());

?>
