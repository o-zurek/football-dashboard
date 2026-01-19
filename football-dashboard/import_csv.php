<?php
// import_csv.php
// Script pour importer un CSV de matchs dans la base et créer automatiquement les équipes

include __DIR__ . '/config/database.php';

// Fonction pour récupérer l'ID d'une équipe, ou la créer si elle n'existe pas
function getTeamId($conn, $teamName) {
    $teamName = trim($teamName); // Supprimer espaces avant/après

    $res = $conn->query("SELECT id FROM equipe WHERE nom='$teamName'");
    if ($res->num_rows > 0) {
        return $res->fetch_assoc()['id'];
    } else {
        $conn->query("INSERT INTO equipe (nom) VALUES ('$teamName')");
        return $conn->insert_id;
    }
}

// Vérifier si un fichier a été envoyé
if (isset($_POST['submit']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, "r")) !== FALSE) {
        $row = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Ignorer la première ligne (header)
            if ($row == 0) { $row++; continue; }

            if(count($data) < 6) continue; // Vérifier qu’il y a au moins 6 colonnes (date, ligue, dom, ext, score_dom, score_ext)

            // Récupérer les colonnes
            $date = $data[0];
            $ligue = $data[1];
            $dom = $data[2];
            $ext = $data[3];
            $score_dom = $data[4];
            $score_ext = $data[5];

            // Récupérer ou créer les équipes
            $id_dom = getTeamId($conn, $dom);
            $id_ext = getTeamId($conn, $ext);

            // Insérer le match
            $sql = "INSERT INTO `match` (date, ligue, equipe_dom, equipe_ext, score_dom, score_ext)
                    VALUES ('$date', '$ligue', $id_dom, $id_ext, $score_dom, $score_ext)";
            if($conn->query($sql)){
                echo "Match $dom vs $ext inséré<br>";
            } else {
                echo "Erreur SQL : " . $conn->error . "<br>";
            }

            $row++;
        }

        fclose($handle);
        echo "<p style='color:green;'>Import terminé !</p>";
    } else {
        echo "<p style='color:red;'>Impossible d'ouvrir le fichier CSV.</p>";
    }
}
?>

<h1>Importer un fichier CSV de matchs</h1>
<form method="post" enctype="multipart/form-data">
    Fichier CSV : <input type="file" name="csv_file" accept=".csv" required><br><br>
    <input type="submit" name="submit" value="Importer">
</form>

<p><a href="view_matches.php">Voir tous les matchs et le classement</a></p>
