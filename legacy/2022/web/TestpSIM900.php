<? php 
$temp=$_GET['température'];
$bdd = new PDO('mysql:host=localhost;dbname=siame', 'root', '');
function __construct($temp)
{
    température`=`this->$temp`
}
$req = $bdd->exec ("UPDATE `machine` SET `température` = '$temp' WHERE `machine`.`nome` = 'machine1'");
?>