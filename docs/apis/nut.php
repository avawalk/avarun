<?php
    include './db.php';

    // craft response template
    $resp = [
      'success' => false,
      'message' => null,
    ];

    // [GET] query data
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $q = isset($_GET['q']) ? $_GET['q'] : '';

        // [GET] query board summary
        if ($q == 'sum') {
          $sql = "SELECT TIMESTAMPDIFF(SECOND, MIN(created_at), MAX(updated_at)) as life_in_seconds, SUM(score) as sum_score FROM avanut;";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $resp['success'] = true;
          $resp['data'] = $row;
        }
        // [GET] query leaderboard
        else {
          $sql = "SELECT code, plays, score FROM `avanut` ORDER BY score DESC LIMIT 10;";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
          $resp['success'] = true;
          $resp['data'] = $rows;
        }
    }

    // [POST] add score
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = $_POST['code'];
        $score = $_POST['score'];
        $secret = $_POST['secret'];

        if ($secret != "DONTHACKMENABRO") { // plz bro plz
          $resp['message'] = 'invalid secret';
        }
        else {
            // find code
            $stmt = $conn->prepare("SELECT plays, score FROM avanut WHERE code = ? LIMIT 1;");
            $stmt->execute([ $code ]);
            $row = $stmt->fetch();

            if ($row === false) { // add new code
              $stmt = $conn->prepare("INSERT INTO `avanut` (`code`, `plays`, `score`) VALUES (?, ?, ?);");
              $stmt->execute([ $code, 1, $score ]);
            }
            else { // update existing code
              $stmt = $conn->prepare("UPDATE `avanut` SET `plays` = ?, `score` = ?, `updated_at` = CURRENT_TIMESTAMP WHERE `code` = ?;");
              $stmt->execute([ $row['plays'] + 1, $row['score'] + $score, $code ]);
            }
            // stamp success
            $resp['success'] = true;
        }
    }

    // return json
    header("Content-Type: application/json");
    echo json_encode($resp);
?>
