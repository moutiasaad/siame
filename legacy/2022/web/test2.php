<!DOCTYPE html>
<html lang="en">
<head>
    <title>SIAME</title>
    <link rel="stylesheet" href="test2.css">
    <script  src="test1.js"></script>
</head>
<body>

    <div class="main">
        <div class="navbar">
            <div class="icon">
                <h2 class="logo"><a hrf="http://www.siame.com.tn/connexion_user.php"><img src="siame1.png"></a></h2>
            </div>

            <div class="menu">
                <ul>
                    <li><a href="http://www.siame.com.tn/index.php">Accueil</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=3">Actualit&eacute;s<i class="fas fa-clock"></i></a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=4">Carri&eacute;res</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=7">Documentation</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=8">Contact</a></li>
                </ul>
            </div>

            <div class="search">
                <input class="srch" type="search" name="" placeholder="Type To text">
                <a href="#"> <button class="btn">Search</button></a>
            </div>

        </div> 
        <img src="Hercule-h200.png" class="machine">
        <div class="content">
    
        <div class="button-box">
        <div id="btn"></div>
            <label class="switch">
               <input  type="checkbox" name="checkbox" value="ON" action="TestSIM900.php">
               <span class="slider round"></span>
            </label>          
        </div>
        <div class="t">
        <input type="text" class="t1"  value="<?php 
        $bdd = new PDO('mysql:host=localhost;dbname=siame', 'root', '');
        $req=$bdd->query("select température from machine");
        while ($donnees=$req->fetch())
        {
        echo $donnees["température"];
        }
        ?>" >
        </div>
        </div>
        <script src ="test2.js"></script>   
    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>