<?php
include __DIR__ . '/config/database.php';

// Si formulaire soumis
if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $dom = $_POST['equipe_dom'];
    $ext = $_POST['equipe_ext'];
    $score_dom = $_POST['score_dom'];
    $score_ext = $_POST['score_ext'];

    $sql = "INSERT INTO `match` (date, equipe_dom, equipe_ext, score_dom, score_ext)
            VALUES ('$date', $dom, $ext, $score_dom, $score_ext)";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Match ajouté avec succès !</p>";
    } else {
        echo "<p style='color:red;'>Erreur : " . $conn->error . "</p>";
    }
}

// Récupérer les équipes pour le formulaire
$teams_result = $conn->query("SELECT id, nom FROM equipe");
$teams = [];
while($row = $teams_result->fetch_assoc()) {
    $teams[] = $row;
}
?>

<h1>Ajouter un match</h1>
<form method="post">
    Date : <input type="date" name="date" required><br><br>

    Équipe domicile : 
    <select name="equipe_dom" required>
        <?php foreach($teams as $team) echo "<option value='{$team['id']}'>{$team['nom']}</option>"; ?>
    </select><br><br>

    Équipe extérieure : 
    <select name="equipe_ext" required>
        <?php foreach($teams as $team) echo "<option value='{$team['id']}'>{$team['nom']}</option>"; ?>
    </select><br><br>

    Score domicile : <input type="number" name="score_dom" min="0"><br><br>
    Score extérieur : <input type="number" name="score_ext" min="0"><br><br>

    <input type="submit" name="submit" value="Ajouter le match">
</form>

<p><a href="view_matches.php">Voir tous les matchs et le classement</a></p>

