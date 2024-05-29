<?php
$des = "BookTopia";
$title = "BookTopia";
$num = "";
$h1 = "BookTopia";
require("include/header.inc.php");

if (!isset($_COOKIE["Langue"]) || empty($_COOKIE["Langue"])) {
    $_COOKIE["Langue"] = "fr-FR";
}
?>
<main>
    <article>
        <h3>Qui sommes nous ?</h3>
        <p>Nous sommes, Lounis BOUHADOUN et Mohamed ABDELLI. Nous sommes des étudiants en deuxième années de licence informatique à l'université de Cergy-Pontoise.</p>
    </article>
    <article>
        <h3>Pourquoi ce site ?</h3>
        <p>Nous avons créé le site <strong>"BookTopia"</strong> pour un projet en deuxième année de licence mathématique et informatique, à l'université de Cergy-Pontoise.</p>
        <p>Pour ce faire, nous avons mis en avant l’ensemble des éléments techniques de l’UE <strong>« Développement Web »</strong> : HTML 5 / CSS 3 / PHP 7, dans le cadre d’une réalisation en binôme.</p>
        <p>C’est pourquoi nous avons utilisé plusieurs langages informatiques, pour arriver à notre but (<strong>PHP, HTML, CSS et JS</strong>).</p>
        <p>Le projet est un petit site web orienté sur les <strong>Livres</strong>. Pour cela nous nous sommes appuyés sur plusieurs ressources disponibles en ligne comme l'API <strong></strong></p>
        <p>Nous souhaitons que notre site soit <strong>simple, divers et accessible à tous</strong>, donc pour cela plusieurs langues sont disponibles sur le site et vous pouvez aussi filtrer vos recherches de livre et auteur en consultant leurs différents genres.</p>
        <p>Notre mission pour l'avenir serait de pouvoir installer des sessions d'utilisateurs, pour pouvoir recevoir des commentaires sur notre site ou bien que l'utilisateur puisse avoir un historique plus complet de toutes ses visites de livres.</p>
    </article>
    <article>
        <h3>Plan du site</h3>
        <figure class="nasa">
            <img src="images/plan.png" alt="Plan du site" title="Plan du site"/>
            <figcaption>Plan du site</figcaption>
        </figure>
        <nav>
            <ul>
                <li><a href="page-de-recherche.php">Recherche</a></li>
                <li><a href="bibliotheque.php">Bibliothèque</a></li>
                <li><a href="statistique.php">Statistique</a></li>
                <li><a href="a_propos.php">À propos</a></li>
                <li><a href="index.php">Accueil </a></li>
                <li><a href="tech.php">Tech</a></li>
            </ul>
        </nav>
    </article>
    <div class="scroll">
        <i class="icofont-rounded-up"></i>
    </div>
</main>
<?php
require 'include/footer.inc.php';
?>
