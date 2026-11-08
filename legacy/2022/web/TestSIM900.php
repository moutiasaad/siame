<?php 
$c="off";
if($_GET['switch']=="on")
{
    $c="on"
};
$bdd = new PDO('mysql:host=localhost;dbname=siame', 'root', '');
$req = $bdd->exec ("UPDATE `machine` SET `etait` = '$c' WHERE `machine`.`nome` = 'machine1';");
echo"$c";
?>