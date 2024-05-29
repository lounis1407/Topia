<?php
$des = "BookTopia";
$title = "Ma bibliothèque";
$num = "L2";
$h1 = "BookTopia";


if (isset($_POST['favorite'])) {
    $bookId = $_POST['bookId'];

    $favorites = isset($_COOKIE['favorites']) ? json_decode($_COOKIE['favorites'], true) : array();

    if (!in_array($bookId, $favorites)) {
        $favorites[] = $bookId;

        if (count($favorites) > 5) {
            array_shift($favorites);
        }

        $favoritesJson = json_encode($favorites);
        setcookie('favorites', $favoritesJson, time() + (86400 * 30), '/');
        header('Location: ' . $_SERVER['PHP_SELF']);
    }
}
if (isset($_POST['clear']) && $_POST['clear'] === 'true') {
    unset($_COOKIE['favorites']);
    setcookie('favorites', '', -1, '/');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}



require("include/header.inc.php");

if (!isset($_COOKIE["Langue"]) || empty($_COOKIE["Langue"])) {
    $_COOKIE["Langue"] = "fr-FR";
}

?>

<main>
    <article>
        <h3>Vos livres favoris</h3>

        <?php
        echo "<div class='results'>";

        // Display the user's favorite books
        if (isset($_COOKIE['favorites']) && !empty($_COOKIE['favorites'])) {
            $favorites = json_decode($_COOKIE['favorites'], true);
            foreach ($favorites as $bookId) {
                $url = "https://www.googleapis.com/books/v1/volumes/{$bookId}?langRestrict=fr&key=AIzaSyDLL3CRwM2_DF-bRow-WYv8yYzBEZQuBzQ";
                $response = @file_get_contents($url);
                if ($response === false) {
                    echo "Error fetching book data. Please try again later.";
                    continue;
                }
                $data = json_decode($response, true);
                $title = $data['volumeInfo']['title'];
                $author = isset($data['volumeInfo']['authors']) ? implode(", ", $data['volumeInfo']['authors']) : "Auteur inconnu";
                $thumbnail = isset($data['volumeInfo']['imageLinks']['thumbnail']) ? $data['volumeInfo']['imageLinks']['thumbnail'] : 'https://via.placeholder.com/150x200.png?text=No+Cover';
                $thumbnail = htmlspecialchars($thumbnail, ENT_XML1);
                $authorName = get_author_details($author);
                echo "<figure>";
                echo "<a href=\"details.php?bookId={$bookId}\"><img alt=\"{$title} by {$author}\" class=\"cover\" src=\"{$thumbnail}\" title=\"{$title} Par {$author}\"/></a>";
                echo "<figcaption><p>Titre : <a href=\"details.php?bookId={$bookId}\">{$title}</a></p><p>Auteur: <a href=\"data_auteur.php?authorName={$authorName['id']}\">{$authorName['name']}</a></p></figcaption>";
                echo "</figure>";
            }
            echo "</div>"; 
            echo "<form method='post' class = 'form'>";
            echo "<input type='hidden' name='clear' value='true'/>";
            echo "<button type='submit' class = 'btn btn-animate'>Nettoyer ma bibliothèque</button>";
            echo "</form>";
        }
        else {
            echo "<figure class='error'>";
            echo '<img src="images/error.png" alt="Votre bibliothèque est vide" title = "Votre bibliothèque est vide"/>';
            echo "<figcaption>";
            echo "<p>Votre bibliothèque est vide pour le moment, veuillez ajouter un livre.</p>";
            echo "</figcaption>";
            echo "</figure>";
            echo "</div>"; 

        }
        ?>

    </article>
    <div class="scroll">
        <i class="icofont-rounded-up"></i>
    </div>
</main>

<?php

require("include/footer.inc.php");


?>