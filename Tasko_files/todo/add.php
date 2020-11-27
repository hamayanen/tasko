<?php

if (isset($_POST['title'])) {
    require("../db_connect.php");

    $title = $_POST['title'];

    if (empty($title)) {
        header("Location: todo.php?mess=error");
    } else {
        $stmt = $db->prepare("INSERT INTO todo(title) VALUE(?)");
        $res = $stmt->execute([$title]);

        if($res) {
            header("Location: todo.php?mess=success");
        } else {
            header("Location: todo.php");
        }
        $db = null;
        exit();
    }
} else {
    header("Location: todo.php?mess=error");
}

?>
