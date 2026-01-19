<?php
header("Content-Type: text/html; charset=utf-8");
require "config/database.php";
require "models/matchModel.php";

$matches = getMatches($conn);
$classements = getClassements($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Football Analytics</title>

<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.legend {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin: 15px 0;
}
.legend span {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    color: #fff;
}
.ucl { background:#2563eb; }
.uel { background:#ea580c; }
.uecl { background:#16a34a; }
.relegation { background:#dc2626; }

/* FILTRES */
.filters {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 15px;
}
.filters input, .filters select {
    padding: 6px;
    border-radius: 6px;
    border: none;
}
</style>
</head>

<body>
<div class="container">

<h1>Football Analytics Dashboard</h1>

<div class="tabs">
    <button class="tablinks" onclick="openTab(event,'matchs')">Matchs</button>
    <button class="tablinks" onclick="openTab(event,'classement')">Classements</button>
    <button class="tablinks" onclick="openTab(event,'graph')">Graphiques</button>
</div>

<!-- ================= MATCHS ================= -->
<div id="matchs" class="tabcontent">

<div class="filters">
    <select id="filterLigue" onchange="filterMatches()">
        <option value="">Toutes les ligues</option>
        <option>La Liga</option>
        <option>Serie A</option>
        <option>Ligue 1</option>
        <option>Premier League</option>
        <option>Bundesliga</option>
    </select>

    <input type="text" id="filterClub" placeholder="Club..." onkeyup="filterMatches()">
    <input type="date" id="filterDate" onchange="filterMatches()">
</div>

<table class="tableau" id="matchTable">
<tr>
    <th>Date</th>
    <th>Ligue</th>
    <th>Domicile</th>
    <th>Extérieur</th>
    <th>Score</th>
</tr>

<?php while($m=$matches->fetch_assoc()): ?>
<tr class="matchRow"
    data-ligue="<?= strtolower($m['ligue']) ?>"
    data-club="<?= strtolower($m['equipe_dom'].' '.$m['equipe_ext']) ?>"
    data-date="<?= $m['date'] ?>">

<td><?= $m['date'] ?></td>
<td><?= $m['ligue'] ?></td>
<td><?= $m['equipe_dom'] ?></td>
<td><?= $m['equipe_ext'] ?></td>
<td><?= $m['score_dom']." - ".$m['score_ext'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- ================= CLASSEMENTS ================= -->
<div id="classement" class="tabcontent">

<div class="legend">
    <span class="ucl">Champions League</span>
    <span class="uel">Europa League</span>
    <span class="uecl">Conference League</span>
    <span class="relegation">Relégation</span>
</div>

<?php foreach($classements as $ligue=>$teams): ?>
<h2><?= $ligue ?></h2>

<table class="tableau">
<tr><th>Place</th><th>Équipe</th><th>Points</th><th>Buts</th></tr>

<?php
uasort($teams, fn($a,$b)=>$b['points']<=>$a['points']);
$position = 0;

foreach($teams as $team=>$s):
$position++;
$class = "";

/* RÈGLES PAR LIGUE */
if ($ligue === "La Liga") {
    if ($position <= 5) $class="ucl";
    elseif ($position <= 7) $class="uel";
    elseif ($position == 8) $class="uecl";
    elseif ($position >= 18) $class="relegation";
}
elseif ($ligue === "Serie A") {
    if ($position <= 4) $class="ucl";
    elseif ($position == 5 || $position == 9) $class="uel";
    elseif ($position == 6) $class="uecl";
    elseif ($position >= 18) $class="relegation";
}
elseif ($ligue === "Ligue 1") {
    if ($position <= 3) $class="ucl";
    elseif ($position == 5 || $position == 6) $class="uel";
    elseif ($position == 7) $class="uecl";
    elseif ($position >= 17) $class="relegation";
}
elseif ($ligue === "Premier League") {
    if ($position <= 5 || $position == 17) $class="ucl";
    elseif ($position == 6 || $position == 7) $class="uel";
    elseif ($position == 12) $class="uecl";
    elseif ($position >= 18) $class="relegation";
}
elseif ($ligue === "Bundesliga") {
    if ($position <= 4) $class="ucl";
    elseif ($position == 5 || $position == 9) $class="uel";
    elseif ($position == 6) $class="uecl";
    elseif ($position >= 17) $class="relegation";
}
?>

<tr class="<?= $class ?>">
<td><?= $position ?></td>
<td><?= $team ?></td>
<td><?= $s['points'] ?></td>
<td><?= $s['buts'] ?></td>
</tr>

<?php endforeach; ?>
</table>
<?php endforeach; ?>
</div>

<!-- ================= GRAPHIQUES ================= -->
<div id="graph" class="tabcontent">
<canvas id="pointsChart"></canvas>
<br><br>
<canvas id="butsChart"></canvas>
</div>

<script src="assets/js/scripts.js"></script>

<script>
function filterMatches() {
    const ligue = document.getElementById("filterLigue").value.toLowerCase();
    const club = document.getElementById("filterClub").value.toLowerCase();
    const date = document.getElementById("filterDate").value;

    document.querySelectorAll(".matchRow").forEach(row => {
        let show = true;
        if (ligue && !row.dataset.ligue.includes(ligue)) show = false;
        if (club && !row.dataset.club.includes(club)) show = false;
        if (date && !row.dataset.date.includes(date)) show = false;
        row.style.display = show ? "" : "none";
    });
}

const data = <?= json_encode($classements, JSON_UNESCAPED_UNICODE) ?>;
let labels=[], points=[], buts=[];
for (const ligue in data) {
    for (const team in data[ligue]) {
        labels.push(team);
        points.push(data[ligue][team].points);
        buts.push(data[ligue][team].buts);
    }
}

new Chart(pointsChart,{
    type:"bar",
    data:{labels,datasets:[{label:"Points",data:points,backgroundColor:"#38bdf8"}]}
});
new Chart(butsChart,{
    type:"bar",
    data:{labels,datasets:[{label:"Buts marqués",data:buts,backgroundColor:"#22c55e"}]}
});
</script>

</div>
</body>
</html>
