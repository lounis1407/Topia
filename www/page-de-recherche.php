<?php
//set Cookie
$nomLivre = isset($_GET['query']) ? $_GET['query'] : '';

if ($nomLivre) {
    setcookie("recherche", $nomLivre, time() + (86400 * 365));

    $fichier = fopen("include/dataRecherche.csv", "a");
    fputcsv($fichier, array(date("Y-m-d H:i:s"), $nomLivre, $_SERVER['REMOTE_ADDR']));
    fclose($fichier);
}

$des = "BookTopia";
$title = "BookTopia";
$num = "L2";
$h1 = "BookTopia";
// Récupérer la liste des recherches précédentes depuis le cookie
require("include/header.inc.php");
$config_data = file_get_contents('include/config.json');
$config = json_decode($config_data);





?>

<main>
    <article>
        <h3>Recherche par titre ou auteur</h3>
        <form method="get" class="form">
            <input type="text" name="query" placeholder="Rechercher des livres" pattern=".{2,}" title="Entrez au moins 2 caractères" required="required" />
            <select name="searchType">
                <option value="intitle">Titre</option>
                <option value="inauthor">Auteur</option>
            </select>
            <button type="submit" class="btn btn-animate">Recherche</button>
        </form>


        <?php

        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            $searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'intitle';
            $startIndex = isset($_GET['startIndex']) ? $_GET['startIndex'] : 0;
            $maxResults = 10;
            $url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode("$searchType:$query") . "&startIndex=$startIndex&maxResults=$maxResults&key=" . $config->key_api;

            $json = file_get_contents($url);
            $data = json_decode($json, true);

            if (isset($data['totalItems']) && $data['totalItems'] > 0) {
                if (isset($data['items'])) {
                    echo "<div class=\"results\">";

                    foreach ($data['items'] as $item) {
                        $title = isset($item['volumeInfo']['title']) ? $item['volumeInfo']['title'] : '';
                        $authors = isset($item['volumeInfo']['authors']) ? implode(', ', $item['volumeInfo']['authors']) : '';
                        if (isset($item['volumeInfo']['imageLinks']['thumbnail'])) {
                            $thumbnail = $item['volumeInfo']['imageLinks']['thumbnail'];
                            $thumbnail = htmlspecialchars($thumbnail, ENT_XML1);
                            $thumbnail .= '&amp;printsec=frontcover&amp;img=1&amp;zoom=0&amp;source=gbs_api';
                        } else {
                            $thumbnail = 'https://via.placeholder.com/150x200.png?text=No+Cover';
                        }
                        $bookId = isset($item['id']) ? $item['id'] : '';
                        $authorName = get_author_details($authors);
                        echo "<figure>";
                        echo "<a href=\"details.php?bookId=$bookId\"><img src=\"$thumbnail\" alt=\"$title\" title=\"$title\" class=\"cover\"/></a>";
                        echo "<figcaption>";
                        echo "<p>Title: <a href=\"details.php?bookId={$bookId}\">{$title}</a></p>";
                        echo "<p>Auteur: <a href=\"data_auteur.php?authorName={$authorName['id']}\"> $authors</a></p>";
                        echo "</figcaption>";
                        echo "</figure>";
                    }
                    echo "</div>";

                    echo "<div class=\"pagination\">";


                    // Add pagination links
                    // Add pagination links
                    $totalPages = 20;
                    $currentPage = ($startIndex / $maxResults) + 1;
                    $laquo = htmlspecialchars('«', ENT_XML1);
                    $raquo = htmlspecialchars('»', ENT_XML1);

                    if ($currentPage > 1) {
                        echo "<a href=\"?query=$query&amp;searchType=$searchType&amp;startIndex=" . ($startIndex - $maxResults) . "\" class=\"page-link\">$laquo</a>";
                    }

                    for ($i = 1; $i <= $totalPages; $i++) {
                        if ($i == $currentPage) {
                            echo "<a href=\"#\" class=\"current-page current page-link\">$i</a>";
                        } else {
                            echo "<a href=\"?query=$query&amp;searchType=$searchType&amp;startIndex=" . (($i - 1) * $maxResults) . "\" class=\"page-link\">$i</a>";
                        }
                    }

                    if ($currentPage < $totalPages) {
                        echo "<a href=\"?query=$query&amp;searchType=$searchType&amp;startIndex=" . ($startIndex + $maxResults) . "\" class=\"page-link\">$raquo</a>";
                    }
                    echo "</div>";
                } else {
                    echo "<p>Aucun résultat trouvé.</p>";
                }
            } else {
                echo "<p>Aucun résultat trouvé.</p>";
            }
        }





        ?>
    </article>

    <article>
        <h3>Les livres les plus recherchés</h3>
        <?php
        echo popularBooks();

        ?>
    </article>

    <div class="scroll">
        <i class="icofont-rounded-up"></i>
    </div>
</main>

<?php

require("include/footer.inc.php");

?>