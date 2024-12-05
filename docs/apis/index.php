<?php
    include './db.php';

    // craft response template
    $resp = [
      'success' => false,
      'message' => null,
    ];

    // [GET] query leaderboard
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT code, plays, score FROM `scores` ORDER BY score DESC LIMIT 10;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp['success'] = true;
        $resp['data'] = $rows;
    }

    // [POST] add score
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = $_POST['code'];
        $score = $_POST['score'];
        $secret = $_POST['secret'];

        if ($secret != "DONTHACKMEBRO") { // plz bro plz
          $resp['message'] = 'invalid secret';
        }
        else {
            // find code
            $stmt = $conn->prepare("SELECT plays, score FROM scores WHERE code = ? LIMIT 1;");
            $stmt->execute([ $code ]);
            $row = $stmt->fetch();

            if ($row === false) { // add new code
              $stmt = $conn->prepare("INSERT INTO `scores` (`code`, `plays`, `score`) VALUES (?, ?, ?);");
              $stmt->execute([ $code, 1, $score ]);
            }
            else { // update existing code
              $stmt = $conn->prepare("UPDATE `scores` SET `plays` = ?, `score` = ?, `updated_at` = CURRENT_TIMESTAMP WHERE `code` = ?;");
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
