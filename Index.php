<!DOCTYPE html>
<html lang="en">
<head>
    <title>SIAME</title>
    <link rel="stylesheet" href="assets/CSS/index.css">
    <script defer src="assets/JS/test.js"></script>
</head>
<body>
    <div class="main">
        <div class="navbar">
            <div class="icon">
                <h2 class="logo"><a href="http://www.siame.com.tn/"><img src="assets/Images/siame.png"></a></h2>
            </div>

            <div class="menu">
                <ul>
                    <li><a href="http://www.siame.com.tn/index.php">Accueil</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=3">Actualités</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=4">Carrières</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=7">Documentation</a></li>
                    <li><a href="http://www.siame.com.tn/template.php?code=8">Contact</a></li>
                </ul>
            </div>
        </div> 

        <div class="content">
            <h1>Bienvenue sur<br><span>SIAME</span> <br>Espace Employees <br></h1>
            <br><button class="cn"><a href="#">REJOIGNEZ-NOUS</a></button>

            <div class="form">
                <div id="error">
                    <!-- Display error message if available -->
                    <?php
                    session_start();
                    if (isset($_SESSION['error'])) {
                        echo '<p style="color: red; font-size: 14px; text-align: center;">' . $_SESSION['error'] . '</p>';
                        unset($_SESSION['error']); // Clear error message after displaying it
                    }
                    ?>
                </div>

                <div class="form-control">
                    <h2>Connectez-vous ici</h2>
                    <form id="form" action="verif.php" method="POST">
                        <input id="username" type="text" name="username" placeholder="Entre email ici" required>
                        <input id="password" type="password" name="password" placeholder="Entre mot de passe ici" required>
                        <button type="submit" class="btnn">Connexion</button>
                    </form>
                </div>

                <p class="link">Tu n'as pas de compte<br>
                    <a href="http://www.siame.com.tn/inscription.php">S'inscrire</a> ici</p>
                <p class="liw">Connectez-vous avec</p>

                <div class="icons">
                    <a href="https://www.facebook.com/Siame-178809118828721/"><ion-icon name="logo-facebook"></ion-icon></a>
                    <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
                    <a href="#"><ion-icon name="logo-twitter"></ion-icon></a>
                    <a href="http://www.siame.com.tn/"><ion-icon name="logo-google"></ion-icon></a>
                    <a href="https://www.linkedin.com/company/siame"><ion-icon name="logo-linkedin"></ion-icon></a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>
