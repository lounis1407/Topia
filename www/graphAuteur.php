<?php
// Charger la bibliothèque jpgraph
require_once("jpgraph/src/jpgraph.php");
require_once("jpgraph/src/jpgraph_bar.php");

// Lire les données du fichier CSV
$data = array();
$fichier3 = fopen("include/dataAuteur.csv", "r");
while (($ligne = fgetcsv($fichier3)) !== false) {
    $date = strtotime($ligne[0]);
    $author_name = $ligne[1];
    if (isset($data[$author_name])) {
        $data[$author_name]++;
    } else {
        $data[$author_name] = 1;
    }
}
fclose($fichier3);

// Trier les données par ordre décroissant de fréquence
arsort($data);

// Sélectionner les 4 recherches les plus fréquentes
$data = array_slice($data, 0, 4);

// Vérifier si $data est vide
if (empty($data)) {
    // Si $data est vide, afficher un message et ne pas dessiner le graphique
    echo "Aucune donnée disponible";
} else {
    // Créer un graphique à barres
    $graph = new Graph(600, 400);
    $graph->SetScale("textlin");
    $graph->SetShadow();
    $graph->img->SetMargin(60, 30, 20, 40);
    $graph->title->Set("Les auteur les plus cliqués");
    $graph->xaxis->SetTitle("Auteurs");
    $graph->yaxis->SetTitle("Nombre de fois");

    // Ajouter les données au graphique
    $bar = new BarPlot(array_values($data));
    $bar->SetFillGradient('#FF5F6D', '#FFC371', GRAD_HOR);
    $bar->SetColor('transparent');
    $bar->value->SetFont(FF_ARIAL, FS_BOLD, 12);
    $bar->value->SetColor("black");
    $bar->value->Show();
    

    // Ajouter les étiquettes des barres
    $graph->xaxis->SetTickLabels(array_keys($data));
    $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8); // réduire la taille de la police



    // Ajouter le graphique à l'image
    $graph->Add($bar);

    // Afficher le graphique
    $graph->Stroke();
}
?>