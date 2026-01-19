<?php

function getMatches($conn) {
    return $conn->query("
        SELECT m.date, m.ligue,
               e1.nom AS equipe_dom,
               e2.nom AS equipe_ext,
               m.score_dom, m.score_ext
        FROM `match` m
        JOIN equipe e1 ON m.equipe_dom = e1.id
        JOIN equipe e2 ON m.equipe_ext = e2.id
        ORDER BY m.ligue, m.date
    ");
}

function getClassements($conn) {
    $data = [];

    $res = $conn->query("
        SELECT m.ligue,
               e1.nom AS dom, e2.nom AS ext,
               m.score_dom, m.score_ext
        FROM `match` m
        JOIN equipe e1 ON m.equipe_dom = e1.id
        JOIN equipe e2 ON m.equipe_ext = e2.id
    ");

    while ($r = $res->fetch_assoc()) {
        foreach (['dom','ext'] as $side) {
            $team = $r[$side];
            if (!isset($data[$r['ligue']][$team])) {
                $data[$r['ligue']][$team] = [
                    'points'=>0, 'buts'=>0
                ];
            }
        }

        if ($r['score_dom'] > $r['score_ext']) {
            $data[$r['ligue']][$r['dom']]['points'] += 3;
        } elseif ($r['score_dom'] < $r['score_ext']) {
            $data[$r['ligue']][$r['ext']]['points'] += 3;
        } else {
            $data[$r['ligue']][$r['dom']]['points'] += 1;
            $data[$r['ligue']][$r['ext']]['points'] += 1;
        }

        $data[$r['ligue']][$r['dom']]['buts'] += $r['score_dom'];
        $data[$r['ligue']][$r['ext']]['buts'] += $r['score_ext'];
    }

    return $data;
}
