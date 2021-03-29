<?php
  header('Access-Control-Allow-Origin: *'); 
  header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type'); 
  header('Content-Type: application/json');

  $postData = file_get_contents('php://input');
  $data = json_decode($postData, true);
  
  include "database.php";

  $mysqli = new mysqli($host, $user, $pass, $db);

  if ($data["tododate"] && $data["status"] && $data["title"]) {
    if (!$data["id"]) {
      $result = $mysqli->query("INSERT INTO todos (tododate, status, title, description) VALUES ('" . $data["tododate"] . "','" . $data["status"]. "','" . $data["title"] . "','" . $data["description"] . "')");
      $data["id"] = $mysqli->insert_id;
    } else {
      $result = $mysqli->query("UPDATE todos SET status='" . $data["status"] . "', title='" . $data["title"]. "', description='" . $data["description"]. "' WHERE id='" . $data["id"] . "'");
    }
    if ($data["newComment"]) {
      $result = $mysqli->query("INSERT INTO comments (todoid, comment) VALUES ('" . $data["id"] . "','" . $data["newComment"] . "')");
      
    }
  } else {
 
  $result = $mysqli->query("SELECT * FROM todos ORDER BY tododate");
  if ($result) {
    $aResult = [];
    while ($row = $result->fetch_assoc()) {
      $comments = $mysqli->query("SELECT comment FROM comments WHERE todoid=".$row["id"]);
      if ($comments) {
        $aComments = [];
        while ($comment = $comments->fetch_array()) {
          array_push($aComments, $comment[0]);
        }
        $row["comments"] = $aComments;
      }
      array_push($aResult, $row);
    }
    echo json_encode($aResult);
  }
  
}
$mysqli->close();
?>