<?php
header('Content-Type: text/html; charset=utf-8');
include 'config/database.php';
include 'models/matchModel.php';

$ligue = $_GET['ligue'] ?? '';
$dateDebut = $_GET['date_debut'] ?? '';
$dateFin = $_GET['date_fin'] ?? '';

$matches = getMatches($conn, $ligue, $dateDebut, $dateFin);
$classement = getClassement($conn);

// Palmarès statique
$palmares = [
    "Ligue 1"=>"Paris SG",
    "Premier League"=>"Manchester City",
    "Champions League"=>"Real Madrid",
    "Europa League"=>"Sevilla",
    "Coupe de France"=>"Lyon"
];

// Préparer données Chart.js
$labels = [];
$pointsData = [];
$butsData = [];

foreach($classement as $lig=>$equipes){
    foreach($equipes as $team=>$stats){
        $labels[] = $team;
        $pointsData[] = $stats['points'];
        $butsData[] = $stats['buts'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Football Dashboard</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
<h1>Football Dashboard</h1>

<!-- Onglets -->
<div>
<button class="tablinks" onclick="openTab(event,'Matchs')" id="defaultOpen">Matchs</button>
<button class="tablinks" onclick="openTab(event,'Classement')">Classement</button>
<button class="tablinks" onclick="openTab(event,'Graphiques')">Graphiques</button>
<button class="tablinks" onclick="openTab(event,'Podium')">Podium / Europe</button>
<button class="tablinks" onclick="openTab(event,'Palmares')">Palmarès</button>
</div>

<!-- Onglet Matchs -->
<div id="Matchs" class="tabcontent">
<h2>Matchs</h2>
<form method="get">
<input type="text" name="ligue" placeholder="Ligue" value="<?= htmlspecialchars($ligue) ?>">
<input type="date" name="date_debut" value="<?= htmlspecialchars($dateDebut) ?>">
<input type="date" name="date_fin" value="<?= htmlspecialchars($dateFin) ?>">
<input type="submit" value="Filtrer">
<a href="index.php">Réinitialiser</a>
</form>
<table class="tableau">
<thead>
<tr><th>Date</th><th>Ligue</th><th>Domicile</th><th>Extérieur</th><th>Score</th></tr>
</thead>
<tbody>
<?php while($row=$matches->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['date'], ENT_QUOTES,'UTF-8') ?></td>
<td><?= htmlspecialchars($row['ligue'], ENT_QUOTES,'UTF-8') ?></td>
<td><?= htmlspecialchars($row['equipe_dom'], ENT_QUOTES,'UTF-8') ?></td>
<td><?= htmlspecialchars($row['equipe_ext'], ENT_QUOTES,'UTF-8') ?></td>
<td><?= $row['score_dom']?>-<?=$row['score_ext']?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- Onglet Classement -->
<div id="Classement" class="tabcontent">
<h2>Classement</h2>
<?php foreach($classement as $ligue=>$equipes): ?>
<h3><?= htmlspecialchars($ligue, ENT_QUOTES,'UTF-8') ?></h3>
<table class="tableau">
<tr><th>Équipe</th><th>Points</th><th>Buts</th></tr>
<?php arsort($equipes); foreach($equipes as $team=>$stats): ?>
<tr>
<td><?= htmlspecialchars($team, ENT_QUOTES,'UTF-8') ?></td>
<td><?= $stats['points'] ?></td>
<td><?= $stats['buts'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endforeach; ?>
</div>

<!-- Onglet Graphiques -->
<div id="Graphiques" class="tabcontent">
<h2>Graphiques</h2>
<canvas id="pointsChart" width="400" height="200"></canvas>
<canvas id="butsChart" width="400" height="200" class="mt-6"></canvas>
</div>

<!-- Onglet Podium -->
<div id="Podium" class="tabcontent">
<h2>Podium et qualifications européennes</h2>
<?php
foreach($classement as $ligue=>$equipes){
    uasort($equipes, function($a,$b){ return $b['points'] - $a['points']; });
    echo "<h3>".htmlspecialchars($ligue,ENT_QUOTES,'UTF-8')."</h3>";
    echo "<ol>";
    $count=0;
    foreach($equipes as $team=>$stats){
        $count++;
        echo "<li>".htmlspecialchars($team,ENT_QUOTES,'UTF-8')." - ".$stats['points']." pts</li>";
        if($count>=4) break;
    }
    echo "</ol>";
}
?>
</div>

<!-- Onglet Palmarès -->
<div id="Palmares" class="tabcontent">
<h2>Palmarès Saison</h2>
<table class="tableau">
<tr><th>Compétition</th><th>Vainqueur</th></tr>
<?php foreach($palmares as $comp=>$winner): ?>
<tr>
<td><?= htmlspecialchars($comp, ENT_QUOTES,'UTF-8') ?></td>
<td><?= htmlspecialchars($winner, ENT_QUOTES,'UTF-8') ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<script src="assets/js/scripts.js"></script>

<script>
var ctxPoints = document.getElementById('pointsChart').getContext('2d');
var pointsChart = new Chart(ctxPoints, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{ label: 'Points', data: <?= json_encode($pointsData) ?>, backgroundColor: 'rgba(59, 130, 246, 0.7)' }]
    },
    options: { responsive: true }
});

var ctxButs = document.getElementById('butsChart').getContext('2d');
var butsChart = new Chart(ctxButs, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{ label: 'Buts', data: <?= json_encode($butsData) ?>, backgroundColor: 'rgba(16, 185, 129, 0.7)' }]
    },
    options: { responsive: true }
});
</script>

</div>
</body>
</html>
