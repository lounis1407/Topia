<?php

declare(strict_types=1);

require("include/functions.inc.php");
include("include/util.inc.php");


// Vérifie si l'utilisateur a choisi un mode de couleur
if(isset($_GET['style']) && ($_GET['style'] == 'style' || $_GET['style'] == 'alternatif')) {
    // Met à jour le cookie pour enregistrer la préférence de l'utilisateur
    setcookie('style', $_GET['style'], time() + (86400 * 30), '/');
    
    // Redirige l'utilisateur vers la même page avec le nouveau paramètre de style
    header('Location: ' . $_SERVER['PHP_SELF']);
}

// Vérifie si l'utilisateur a déjà choisi un mode de couleur
if(isset($_COOKIE['style'])) {
    // Si oui, utilise le fichier CSS correspondant
    if($_COOKIE['style'] == 'style') {
        $stylesheet = 'style.css';
    } else {
        $stylesheet = 'alternatif.css';
    }
} else {
    // Si non, utilise le mode clair par défaut
    $stylesheet = 'style.css';
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Mohamed Abdelli" />
    <meta name="description" content=" <?php echo $des ?>" />
    <meta name="subject" content="Projet Dev Web <?php echo $num ?>" />
    <meta name="date" content="2023-02-09T8:36:12+0100" />
    <meta name="lieu" content="CY Cergy Paris Universite" />
    <link rel='stylesheet' href="<?php echo $stylesheet;?>"/>
    <link rel="stylesheet" href="swiper/swiper-bundle.min.css" />
    <link rel="shortcut icon" href="data:image/png;base64,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" />
    <link rel="stylesheet" href="icofont/icofont.min.css" />

    <title><?php echo $title ?></title>
</head>

<body onload="preloader()">

    <div class="preloader">
        <img src="images/preloader.gif" alt="preloader" />
    </div>
    <header>
        <nav>
            <div class="brand">
                <a href="index.php" class="brand-logo" title="Revenir à l'accueil">BookTopia</a>
            </div>
            <ul>
                <li><a href="page-de-recherche.php">Recherche</a></li>
                <li><a href="bibliotheque.php">Bibliothèque</a></li>
                <li><a href="statistique.php">Statistique</a></li>
                <li><a href="a_propos.php">À propos</a></li>
            </ul>
        </nav>
        <div class="items-mode">
            <ul>


                <li><a href="?style=style"><img src="./images/light_mode.png" alt="theme-icon" /></a></li>
                <li><a href="?style=alternatif"><img src="./images/dark_mode.png" alt="theme-icon" /></a></li>

            </ul>
        </div>
    </header>