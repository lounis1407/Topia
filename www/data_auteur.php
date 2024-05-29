<?php
function dateDuJour($lang)
{
    $lang;
    $date1 = date('Y-m-d');
    $months = array(
        'fr' => array(
            'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
        ),
        'en' => array(
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        )
    );

    $days = array(
        'fr' => array(
            'dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'
        ),
        'en' => array(
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        )
    );

    if ($lang == 'en') {
        return $days['en'][date('w')] . ', ' . $months['en'][(int)date('m') - 1] . ' ' . date('j') . ', ' . date('Y');
    } elseif ($lang == 'fr') {
        return $days['fr'][date('w')] . ' ' . date('d') . ' ' . $months['fr'][(int)date('m') - 1] . ' ' . date('Y');
    } else {
        return "La langue n'a pas été choisie";
    }
}

if (isset($_GET['authorName'])) {
    $author_name = $_GET['authorName'];
    if ($author_name !== null && strpos($author_name, "_") !== false) {
        $author_name = str_replace("_", " ", $author_name);
    }
    setcookie("AuteurConsultée", $author_name, time() + (86400 * 365));

    $fichier3 = fopen("include/dataAuteur.csv", "a");
    fputcsv($fichier3, array(date("Y-m-d H:i:s"), $author_name, $_SERVER['REMOTE_ADDR']));
    fclose($fichier3);
}


setcookie("DateConsultee", dateDuJour("fr"), time() + 365 * 24 * 3600, "/");


$des = "BookTopia";
$title = "Biographie - Auteur";
$num = "";
$h1 = "BookTopia";
require("include/header.inc.php");

if (!isset($_COOKIE["Langue"]) || empty($_COOKIE["Langue"])) {
    $_COOKIE["Langue"] = "fr-FR";
}




?>
<main>
    <?php
    if (!isset($_GET['authorName'])) {
        echo '<meta http-equiv="refresh" content="0; url=error.html"/>';
        exit();
    }
    $authorName = $_GET['authorName'];

    // Get details for the author
    $authorDetails = get_author_details($authorName);
    ?>
    <article>
        <h3>Biographie de <?php echo $authorDetails['name']; ?></h3>
        <?php if ($authorDetails['image'] !== '') : ?>
            <figure>
                <img src="<?php echo $authorDetails['image'] ?>" alt="Photo de l'auteur <?php echo $authorName ?>" class="auteur" />
                <figcaption>
                    <?php if ($authorDetails['bio'] !== '') : ?>
                        <p><?php echo $authorDetails['bio'] ?></p>
                    <?php else : ?>
                        <p>Aucune biographie trouvée pour cet auteur.</p>
                    <?php endif; ?>
                </figcaption>
            </figure>
        <?php endif; ?>
    </article>

    <article>
        <?php
        $authorName = isset($_GET['authorName']) ? $_GET['authorName'] : null;
        $topBooks = get_top_books_by_author($authorName);
        if ($authorName !== null && strpos($authorName, "_") !== false) {
            $authorName = str_replace("_", " ", $authorName);
        }

        // Display the top 4 books by the author
        echo "<h3>Top 3 livres de " . $authorName . "</h3>";
        echo "<div class='book-grid'>";
        if (!empty($topBooks)) {
            foreach ($topBooks as $book) {
                $googleBookId = getGoogleBookId($book['title'], $authorName);
                echo "<figure>";
                echo "<a href=\"details.php?bookId={$googleBookId}\"><img alt=\"{$book['title']} by {$authorName}\" class=\"cover\" src=\"{$book['image_url']}\" title=\"{$book['title']} by {$authorName}\"/></a>";
                echo "<figcaption>";
                echo "<p>Titre : <a href=\"details.php?bookId={$googleBookId}\">{$book['title']}</a></p>";
                echo "<p>Publié en : " . $book['published_date'] . "</p>";
                echo "<p>ISBN : " . $book['isbn'] . "</p>";
                echo "</figcaption>";
                echo "</figure>";
            }
        } else {
            echo "<p>Aucun livre trouvé pour cet auteur.</p>";
        }
        echo "</div>";
        ?>

    </article>
    <div class="scroll">
        <i class="icofont-rounded-up"></i>
    </div>
</main>

<?php

require("include/footer.inc.php");


?>