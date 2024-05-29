<?php


/**
 * Compte le nombre de visiteur du site web 
 * @return Int: le nombre de visite
 */

function compteur()
{
    $file_path = "visits.txt";

    $file_handle = fopen($file_path, "c+");
    if (!$file_handle) {
        die("Failed to open file");
    }

    flock($file_handle, LOCK_EX);

    $visits_count = 0;
    $file_size = filesize($file_path);
    if ($file_size > 0) {
        $contents = fread($file_handle, $file_size);
        $visits_count = intval($contents);
    }

    $visits_count++;

    fseek($file_handle, 0);
    fwrite($file_handle, "$visits_count");
    fflush($file_handle);

    flock($file_handle, LOCK_UN);
    fclose($file_handle);

    echo "Nombre de visites : $visits_count";
}








/**
 * Fonction qui permets d'avoir le style utilisé de la page web
 * @return String : la valeur de $style
 */

function get_style(): string
{
    $style = "style"; // valeur par défaut si $_GET['style'] n'est pas défini

    if (isset($_GET['style'])) { // vérifie si $_GET['style'] est défini
        $style = $_GET['style']; // s'il est défini, assigner sa valeur à $style
    }

    return $style; //renvoie la valeur de $style
}

/**
 * Fonction qui permets d'avoir le langue utilisé de la page web
 * @return String : la valeur de $lang 
 */

function get_lang(): string
{
    global $lang; //accéder à une variable déclarée à l'extérieur de la fonction, ici la variable $lang. Cela permet à la fonction d'accéder et de modifier cette variable.
    if (isset($_GET['lang'])) { //vérifier si la variable $_GET['lang'] est définie. Cette variable représente les données passées dans l'URL après le point d'interrogation (?).
        $lang = $_GET['lang']; //affecter la valeur de $_GET['lang'] à la variable $lang si elle est définie.
    } else {
        $lang = "fr"; //affecter alors la valeur "fr" (français) à la variable $lang.
    }
    return $lang; //
}


/**

 *Récupère les livres les plus populaires de la semaine depuis l'API Open Library et affiche une grille de couvertures de livres avec leur titre et leur auteur.
 *@param int $num_books Le nombre de livres à afficher (3 par défaut)
 *@return void
 */


function trendingBooks($num_books = 3): void
{
    $link_api = "https://openlibrary.org/trending/weekly.json?limit={$num_books}";

    // Initialisation de curl
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $link_api);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Exécution de la requête HTTP
    $response = curl_exec($curl);

    // Vérification du code de réponse HTTP
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        echo "Sorry, there was an error retrieving the data. Please try again later.";
        return;
    }

    // Traitement des données
    $obj = json_decode($response);
    if ($obj === null) {
        echo "Sorry, there was an error retrieving the data. Please try again later.";
        return;
    }

    $obj = $obj->works;

    echo "<div class='book-grid'>";

    foreach ($obj as $book) {
        if ($num_books-- <= 0) break;

        $title = $book->title;
        $author = is_array($book->author_name) ? implode(", ", $book->author_name) : $book->author_name;

        $bookId = isset($book->cover_i) ? $book->cover_i : null;
        $coverUrl = ($bookId !== null) ? "https://covers.openlibrary.org/b/id/{$bookId}-M.jpg" : "https://via.placeholder.com/150x200.png?text=No+Cover";

        $googleBookId = getGoogleBookId($title, $author);
        $authorName = get_author_details($author);

        echo "<figure>";
        echo "<a href=\"details.php?bookId={$googleBookId}\"><img alt=\"{$title} by {$author}\" class=\"cover\" src=\"{$coverUrl}\" title=\"{$title} Par {$author}\"/></a>";
        echo "<figcaption><p>Titre : <a href=\"details.php?bookId={$googleBookId}\">{$title}</a></p> <p>Auteur:<a href=\"data_auteur.php?authorName={$authorName['id']}\">{$author}</a></p></figcaption>";
        echo "</figure>";
    }

    echo "</div>";

    // Fermeture de curl
    curl_close($curl);
}





/**

 *Cette fonction récupère les informations des livres les plus récents depuis l'API Google Books
 *@param int $num_books Le nombre de livres à récupérer
 *@return void
 */

function getNewBooks($num_books = 3): void
{
    $link_api = "https://www.googleapis.com/books/v1/volumes?q=new+books&orderBy=newest&maxResults=$num_books";
    $obj = json_decode(file_get_contents($link_api));

    if ($obj === null) {
        echo "Sorry, there was an error retrieving the data. Please try again later.";
        return;
    }
    echo "<div class='book-grid'>";
    try {
        foreach ($obj->items as $item) {
            $volumeInfo = $item->volumeInfo;

            $title = $volumeInfo->title;
            $authors = isset($volumeInfo->authors) ? implode(", ", $volumeInfo->authors) : "Unknown";
            $publishedDate = isset($volumeInfo->publishedDate) ? $volumeInfo->publishedDate : "Unknown";
            $thumbnail = isset($volumeInfo->imageLinks) ? $volumeInfo->imageLinks->thumbnail : "https://via.placeholder.com/150x200.png?text=No+Cover";
            $thumbnail = htmlspecialchars($thumbnail, ENT_XML1);
            $googleBookId = $item->id;
            $authorName = get_author_details($authors);

            echo "<figure>";
            echo "<a href=\"details.php?bookId={$googleBookId}\"><img alt=\"{$title} by {$authors}\" class=\"cover\" src=\"{$thumbnail}\" title=\"{$title} by {$authors}\"/></a>";
            echo "<figcaption><p>Title: <a href=\"details.php?bookId={$googleBookId}\">{$title}</a></p> <p>Auteur: <a href=\"data_auteur.php?authorName={$authorName['id']}\">{$authors}</a></p> <p>Publié le: {$publishedDate}</p></figcaption>";
            echo "</figure>";
        }
    } catch (Exception $e) {
        echo "Sorry, something went wrong while retrieving the data. Please try again later.";
    }
    echo "</div>";
}



/**

 *Cette fonction récupère les détails d'un auteur depuis l'API Wikipedia
 *@param string $authorName Le nom de l'auteur
 *@return array Les détails de l'auteur
 */
function get_author_details($authorName)
{
    // Add "_" to authorName if it contains spaces
    if ($authorName !== null && strpos($authorName, " ") !== false) {
        $authorName = str_replace(" ", "_", $authorName);
    } else {
        $authorName = $authorName;
    }

    // Set up the Wikipedia REST API endpoint URL
    $url = "https://fr.wikipedia.org/api/rest_v1/page/summary/" . urlencode($authorName);

    // Send a GET request to the API endpoint
    $response = @file_get_contents($url);
    $author_Name = $authorName;
    if ($author_Name !== null && strpos($author_Name, "_") !== false) {
        $author_Name = str_replace("_", " ", $author_Name);
    } else {
        $author_Name = $author_Name;
    }
    // Check if the API returned a valid response
    if ($response === false) {
        return array(
            'name' => $author_Name,
            'id' => $authorName,
            'bio' => 'La biographie de cet auteur n\'est pas disponible',
            'image' => 'https://via.placeholder.com/1000x500.png?text=No+Picture',
            'books' => array(),
        );
    }

    // Decode the JSON response into an associative array
    $json = json_decode($response, true);

    // Check if the author's biography is available in the API response
    $bio = isset($json['extract']) ? $json['extract'] : 'La biographie de cet auteur n\'est pas disponible';
    $image_author = isset($json['thumbnail']['source']) ? $json['thumbnail']['source'] : 'https://via.placeholder.com/400x200.png?text=No+Picture';
    $image_author = htmlspecialchars($image_author, ENT_XML1);



    // Return the author's name, short biography, and image
    return array(
        'name' => $author_Name,
        'id' => $authorName,
        'bio' => $bio,
        'image' => $image_author,
    );
}

/**

 *Cette fonction permet de récupérer les 3 premiers livres d'un auteur à partir de son nom.
 *@param string $authorName Le nom de l'auteur dont on souhaite récupérer les livres.
 *@return array Un tableau associatif contenant les détails des 3 premiers livres de l'auteur (titre, date de publication, ISBN, URL de l'image de couverture). Si la recherche ne renvoie aucun livre ou si une erreur se produit, un tableau vide est renvoyé.
 */
function get_top_books_by_author($authorName)
{
    if ($authorName !== null && strpos($authorName, "_") !== false) {
        $authorName = str_replace("_", " ", $authorName);
    } else {
        $authorName = $authorName;
    }
    // Set up the OpenLibrary API endpoint URL to fetch the author's books
    $ol_url = "https://openlibrary.org/search.json?author=" . urlencode($authorName);

    // Send a GET request to the OpenLibrary API endpoint
    $ol_response = @file_get_contents($ol_url);

    // Check if the API returned a valid response
    if ($ol_response === false) {
        return array();
    } else {
        // Decode the JSON response into an associative array
        $ol_json = json_decode($ol_response, true);

        // Extract the top 4 book details from the OpenLibrary API response
        $books = array();
        if (isset($ol_json['docs'])) {
            foreach ($ol_json['docs'] as $book) {
                if (count($books) >= 3) {
                    break;
                }
                $books[] = array(
                    'title' => isset($book['title']) ? $book['title'] : 'N\'a pas de titre',
                    'published_date' => isset($book['publish_date']) ? $book['publish_date'][0] : 'N\'a pas de publication',
                    'isbn' => isset($book['isbn'][0]) ? $book['isbn'][0] : 'N\'a pas d\'ISBN',
                    'image_url' => isset($book['cover_i']) ? 'https://covers.openlibrary.org/b/id/' . $book['cover_i'] . '-M.jpg' : 'https://via.placeholder.com/150x200.png?text=No+Cover',
                );
            }
        }
        return $books;
    }
}


/**
 *Cette fonction permet de récupérer l'identifiant Google Books d'un livre à partir de son titre et de l'auteur.
 *@param string $title Le titre du livre.
 *@param string $author Le nom de l'auteur du livre.
 *@return string|null L'identifiant Google Books du premier livre correspondant à la recherche, ou null s'il n'y a pas de résultat ou si une erreur se produit.
 */

function getGoogleBookId($title, $author)
{
    $query = urlencode("$title $author");
    $link_api = "https://www.googleapis.com/books/v1/volumes?q={$query}";
    $obj = json_decode(file_get_contents($link_api));

    if ($obj === null || !isset($obj->items) || count($obj->items) == 0) {
        return null;
    }

    return $obj->items[0]->id;
}


/**
 * Vérifie si un lien URL donné correspond à un livre valide sur Google Books.
 * @param string $url L'URL à vérifier.
 * @return bool Retourne true si l'URL correspond à un livre valide sur Google Books ou pas.
 */

function LienValide($url)
{
    $response = file_get_contents($url);
    $json = json_decode($response);

    if ($json === null || !property_exists($json, 'kind') || $json->kind != 'books#volume') {
        return false;
    }

    $headers = get_headers($url);
    if ($headers === false || strpos($headers[0], '200') === false) {
        return false;
    }

    return true;
}

/**
 * Affiche les 3 derniers livres de fiction depuis l'API Google Books.
 * Si aucun livre n'est trouvé, affiche un message d'erreur.
 * @return void
 * 
 */
function popularBooks()
{
    $url = 'https://www.googleapis.com/books/v1/volumes?q=subject:fiction&orderBy=newest&maxResults=3';
    $data = file_get_contents($url);
    $books = json_decode($data, true);

    if (!$books) {
        echo "Aucun livre n'a été trouvé.";
        return;
    }
    echo "<div class='book-grid'>";

    foreach ($books['items'] as $book) {
        $title = $book['volumeInfo']['title'];
        $author = isset($book['volumeInfo']['authors']) ? implode(", ", $book['volumeInfo']['authors']) : "Auteur inconnu";
        $thumbnail = isset($book['volumeInfo']['imageLinks']['thumbnail']) ? $book['volumeInfo']['imageLinks']['thumbnail'] : 'no_cover_img.png';
        $thumbnail = htmlspecialchars($thumbnail, ENT_XML1);
        $id = $book['id'];
        $authorName = get_author_details($author);


        echo "<figure>";
        echo "<a href=\"details.php?bookId={$id}\"><img alt=\"{$title} by {$author}\" class=\"cover\" src=\"{$thumbnail}\" title=\"{$title} Par {$author}\"/></a>";
        echo "<figcaption><p>Titre : <a href=\"details.php?bookId=" . $id . "\">" . $title . "</a></p> <p>Auteur: <a href=\"data_auteur.php?authorName={$authorName['id']}\">{$author}</a></p></figcaption>";
        echo "</figure>";
    }
    echo "</div>";
}
