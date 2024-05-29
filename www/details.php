    <?php
    function dateDuJour($lang)
    {
        $lang;
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
    if (isset($_GET['bookId'])) {
        $bookId = $_GET['bookId'];
        setcookie("LivreConsulté", $bookId, time() + (86400 * 365));

        // Requête à l'API Google Books
        $url = "https://www.googleapis.com/books/v1/volumes/$bookId";
        $response = file_get_contents($url);
        $book = json_decode($response, true);

        // Récupération du titre du livre
        $title_Book = $book['volumeInfo']['title'];

        // Enregistrement dans le fichier CSV
        $fichier2 = fopen("include/dataLivre.csv", "a");
        fputcsv($fichier2, array(date("Y-m-d H:i:s"), $bookId, $title_Book, $_SERVER['REMOTE_ADDR']));
        fclose($fichier2);
    }


    setcookie("DateConsultee", dateDuJour("fr"), time() + 365 * 24 * 3600, "/");



    $des = "BookTopia";
    $title = "Détail Livre";
    $num = "L2";
    $h1 = "BookTopia";
    require("include/header.inc.php");

    if (!isset($_COOKIE["Langue"]) || empty($_COOKIE["Langue"])) {
        $_COOKIE["Langue"] = "fr-FR";
    }




    ?>

    <main>
        <?php
        // Check if the 'bookId' parameter exists in the $_GET array
        if (!isset($_GET['bookId'])) {
            echo '<meta http-equiv="refresh" content="0; url=error.html"/>';
            exit(); // arrêter l'exécution du script après avoir redirigé vers la page d'erreur

        }

        // Get the book ID from the query string
        $bookId = $_GET['bookId'];

        // Construct the API URL with the book ID and API key
        $url = "https://www.googleapis.com/books/v1/volumes/{$bookId}?langRestrict=fr&key=AIzaSyDLL3CRwM2_DF-bRow-WYv8yYzBEZQuBzQ";

        // Make the API request and decode the JSON response
        $response = @file_get_contents($url);
        if ($response === false) {
            echo "Error fetching book data. Please try again later.";
            exit();
        }

        $data = json_decode($response, true);

        // Check if the API response contains any book data
        if (empty($data) || isset($data['error'])) {
            echo '<meta http-equiv="refresh" content="0; url=error.html"/>';
            exit();
        }

        // Extract the book details from the API response
        $title = isset($data['volumeInfo']['title']) ? $data['volumeInfo']['title'] : 'Titre non disponible';
        $authors = isset($data['volumeInfo']['authors']) ? implode(", ", $data['volumeInfo']['authors']) : 'Auteur non disponible';
        $description = isset($data['volumeInfo']['description']) ? strip_tags($data['volumeInfo']['description']) : 'Description non disponible';

        $image = isset($data['volumeInfo']['imageLinks']['thumbnail']) ? $data['volumeInfo']['imageLinks']['thumbnail'] . '&printsec=frontcover&img=1&zoom=0&source=gbs_api' : 'https://via.placeholder.com/150x200.png?text=No+Cover';
        $image = htmlspecialchars($image, ENT_XML1);

        $isbn = isset($data['volumeInfo']['industryIdentifiers']) ? implode(", ", array_column(array_filter($data['volumeInfo']['industryIdentifiers'], function ($item) {
            return in_array($item['type'], ['ISBN_10', 'ISBN_13']);
        }), 'identifier')) : 'ISBN non disponible';
        $publisher = isset($data['volumeInfo']['publisher']) ? $data['volumeInfo']['publisher'] : 'Editeur non disponible';
        $page_number = isset($data['volumeInfo']['pageCount']) ? $data['volumeInfo']['pageCount'] : 'Nombre de pages non disponible';
        $authorName = get_author_details($authors);

        // Output the book details in HTML
        echo "<article>";
        echo "<h3>{$title}</h3>";
        echo "<figure>";
        echo "<img src='{$image}' alt='{$title}' class=\"cover\" title='{$title}'/>";
        echo "<figcaption>";
        echo "<p><strong>Auteur:</strong> <a href=\"data_auteur.php?authorName={$authorName['id']}\">{$authors}</a></p>";
        echo "<p><strong>Nombre de pages:</strong> {$page_number} pages</p>";

        echo "<p><strong>ISBN:</strong> {$isbn}</p>";
        echo "<p><strong>Edition:</strong> {$publisher}</p>";
        echo "<p><strong>Date de publication:</strong> {$publisher}</p>";
        echo "<p><strong>Description:</strong> {$description}</p>";
        echo "</figcaption>";
        echo "</figure>";
        // Afficher le formulaire pour ajouter le livre aux favoris
        echo "<form method='post' action='bibliotheque.php' class= 'form'>";
        echo "<input type='hidden' name='bookId' value='{$bookId}'/>";
        echo "<button type='submit' name='favorite' class='btn btn-animate'>Ajouter aux favoris</button>";
        echo "</form>";

        // Ajouter le livre aux favoris si le formulaire est soumis
        if (isset($_POST['favorite'])) {
            // Récupérer l'ID du livre à ajouter
            $bookId = $_POST['bookId'];

            // Vérifier si le tableau de session existe déjà
            if (!isset($_SESSION['favorites'])) {
                $_SESSION['favorites'] = array();
            }

            // Vérifier si le livre n'est pas déjà dans la liste des favoris
            if (!in_array($bookId, $_SESSION['favorites'])) {
                // Ajouter le livre à la liste des favoris
                array_push($_SESSION['favorites'], $bookId);

                // Vérifier si le nombre maximal de livres favoris est atteint
                if (count($_SESSION['favorites']) > 5) {
                    // Supprimer le premier livre ajouté pour maintenir une liste de 5 livres
                    array_shift($_SESSION['favorites']);
                }
            }
        }

        echo "</article>";
        ?>
        <?php
        // Get the book's industry identifier (ISBN-10 or ISBN-13)
        $industryIdentifiers = isset($data['volumeInfo']['industryIdentifiers']) ? array_filter($data['volumeInfo']['industryIdentifiers'], function ($item) {
            return in_array($item['type'], ['ISBN_10', 'ISBN_13']);
        }) : [];

        // Convert the industry identifiers to a comma-separated string
        $industryIdentifiers = implode(",", array_column($industryIdentifiers, 'identifier'));

        // Define the API URL to fetch similar books
        $bookTitle = urlencode($data['volumeInfo']['title']);
        $authorName = urlencode(implode(", ", $data['volumeInfo']['authors']));
        $similarUrl = "https://www.googleapis.com/books/v1/volumes?q={$bookTitle}+inauthor:{$authorName}&langRestrict=fr&maxResults=3";

        // Make the API request and decode the JSON response
        $similarResponse = @file_get_contents($similarUrl);
        if ($similarResponse !== false) {
            $similarData = json_decode($similarResponse, true);
            if (isset($similarData['items'])) {

                // Output the similar books section
                echo "<article>";
                echo "<h3>Livres similaires</h3>";
                echo "<div class=\"book-grid\">";

                // Loop through each similar book and output its details
                foreach ($similarData['items'] as $similarBook) {
                    $similarTitle = isset($similarBook['volumeInfo']['title']) ? $similarBook['volumeInfo']['title'] : '';
                    $similarImage = isset($similarBook['volumeInfo']['imageLinks']['thumbnail']) ? $similarBook['volumeInfo']['imageLinks']['thumbnail'] . '&printsec=frontcover&img=1&zoom=0&source=gbs_api' : 'https://via.placeholder.com/150x200.png?text=No+Cover';
                    $similarImage = htmlspecialchars($similarImage, ENT_XML1);
                    $similarLink = isset($similarBook['selfLink']) ? $similarBook['selfLink'] : '';
                    $similarAuthor = isset($similarBook['volumeInfo']['authors']) ? implode(", ", $similarBook['volumeInfo']['authors']) : '';
                    $similarId = isset($similarBook['id']) ? $similarBook['id'] : '';
                    // Get the author details
                    $authorDetails = get_author_details($similarAuthor);
                    $authorLink = "data_auteur.php?authorName={$authorDetails['id']}";

                    // Output the details of the similar book
                    echo "<figure>";
                    echo "<a href=\"details.php?bookId={$similarId}\">";
                    echo "<img src=\"{$similarImage}\" alt=\"{$similarTitle}\" class=\"cover\" title=\"{$similarTitle}\"/>";
                    echo "</a>";
                    echo "<figcaption>";
                    echo "<p>Titre : <a href=\"details.php?bookId={$similarId}\">{$similarTitle}</a></p>";
                    echo "<p>Auteur : <a href=\"{$authorLink}\">{$similarAuthor}</a></p>";
                    echo "</figcaption>";
                    echo "</figure>";
                }

                // Close the similar books section
                echo "</div>";
                echo "</article>";
            }
        }
        ?>


        <div class="scroll">
            <i class="icofont-rounded-up"></i>
        </div>
    </main>

    <?php

    require("include/footer.inc.php");


    ?>