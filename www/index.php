<?php
$des = "BookTopia";
$title = "BookTopia";
$num = "L2";
$h1 = "BookTopia";
require("include/header.inc.php");
$config_data = file_get_contents('include/config.json');
$config = json_decode($config_data);
if (!isset($_COOKIE["Langue"]) || empty($_COOKIE["Langue"])) {
    $_COOKIE["Langue"] = "fr-FR";
}


?>
<div class="home swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide hero-child">
            <img src="https://source.unsplash.com/1250x800?books" alt="imagealéatoire" />
            <section class="hero-text-section">
                <h1 class="hero-name"><?php echo $h1 ?></h1>
                <span class="story">Plongez dans l'univers envoûtant de la lecture avec Booktopia, votre destination littéraire ultime !</span>
            </section>
        </div>
        <div class="swiper-slide hero-child">
            <img src="https://source.unsplash.com/1250x800?library" alt="imagealéatoire" />
        </div>
    </div>
    <div class="swiper-pagination"></div>
</div>



<main>
    <?php

    if (isset($_COOKIE["LivreConsulté"]) && !empty($_COOKIE["LivreConsulté"]) && ($_COOKIE["LivreConsulté"] != "0")) {
        if (isset($_COOKIE["DateConsultee"]) && !empty($_COOKIE["DateConsultee"])) {  //On récupere le cookie de la date de consultation de la Musique, on s'assure qu'il est bien défini, puis on l'affiche

            // On récupère le cookie de l'id du livre, on s'assure qu'il est bien défini, puis on le retranscrit pour l'afficher
            $url = $config->link_api . "/" . $_COOKIE["LivreConsulté"] . "?apikey=" . $config->key_api;
            if (LienValide($url)) {
                echo "<article>";
                echo "<h3>Dernier livre consulté</h3>\n";
                $obj = json_decode(file_get_contents($url));
                $title = isset($obj->volumeInfo->title) ? $obj->volumeInfo->title : '';
                $idLivre = $_COOKIE["LivreConsulté"];
                $img = isset($obj->volumeInfo->imageLinks->thumbnail) ? $obj->volumeInfo->imageLinks->thumbnail . '&amp;printsec=frontcover&amp;img=1&amp;zoom=0&amp;source=gbs_api' : 'https://via.placeholder.com/150x200.png?text=No+Cover';
                $img = htmlspecialchars($img, ENT_XML1);


                echo "<figure class=\"cookie\">";
                echo "<a href=\"details.php?bookId=" . $idLivre . "\"><img src=\"" . $img . "\" class=\"cover\"/></a>\n";
                echo "<figcaption class=\"name\"><p>Titre du livre : <a href=\"details.php?bookId=$idLivre\">$title</a>, Consulté le : {$_COOKIE["DateConsultee"]}</p></figcaption>";
                echo "</figure>";
                echo "</article>";
            }
        }
    }
















    ?>


    <article>

        <h3>Les livres en tendance</h3>
        <?php
        echo trendingBooks();


        ?>

    </article>
    <div class="similar-books">
        <article>
            <h3>Les dernières sorties</h3>
            <?php
            echo getNewBooks();

            ?>
        </article>
    </div>
    <div class="scroll">
        <i class="icofont-rounded-up"></i>
    </div>
</main>
<?php

require("include/footer.inc.php");


?>