<?php
    session_start();
    require_once "../lib/base.php";
    $conn = getDB();

    $id = $_POST["id"];
    $topic = $_POST["topic"]; // select value
    $title = $_POST["title"];
    $option = $_POST["option"]; // option array
    $owner = $_SESSION["email"];

    if(isset($_POST['new_topic']) && $_POST['new_topic'] != "")
    {
        $topic = $_POST['new_topic'];
    }

    // check if the id is exist
    $stmt = $conn->prepare("SELECT * FROM topics WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if(count($result) == 0)
    {
        $_SESSION["error"] = "Poll not found.";
        session_write_close();
        echo "<script>alert('Poll not found.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    // check if the title is already exist in the same topic and not the same id
    $stmt = $conn->prepare("SELECT * FROM topics WHERE topic_name = ? AND title = ? AND id != ?");
    $stmt->bindParam(1, $topic, PDO::PARAM_STR);
    $stmt->bindParam(2, $title, PDO::PARAM_STR);
    $stmt->bindParam(3, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if(count($result) > 0)
    {
        $_SESSION["error"] = "Title already exists in the same topic";
        session_write_close();
        echo "<script>alert('Title already exists in the same topic.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    foreach($option as $opt)
    {
        if(trim($opt) == "")
        {
            $_SESSION["error"] = "Please fill in the option name";
            session_write_close();
            echo "<script>alert('Please fill in the option name.');</script>";
            echo "<script>window.location.href='../poll.php';</script>";
            exit();
        }
    }

    // check if the poll owner is the same as the current user
    $stmt = $conn->prepare("SELECT * FROM topics WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result["owner"] != $owner && $owner != "bey0nd@csie.io")
    {
        $_SESSION["error"] = "You are not the owner of this poll.";
        session_write_close();
        echo "<script>alert('You are not the owner of this poll.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    // update the poll topic and title

    date_default_timezone_set('Asia/Taipei');
    $current_timestamp = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE topics SET topic_name = ?, title = ?, created_time = ? WHERE id = ?");
    $stmt->bindParam(1, $topic, PDO::PARAM_STR);
    $stmt->bindParam(2, $title, PDO::PARAM_STR);
    $stmt->bindParam(3, $current_timestamp, PDO::PARAM_STR);
    $stmt->bindParam(4, $id, PDO::PARAM_INT);
    $stmt->execute();

    // add new option
    foreach($option as $opt)
    {
        $stmt = $conn->prepare("INSERT INTO poll_options (topic_id, item_name) VALUES (?, ?)");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->bindParam(2, $opt, PDO::PARAM_STR);
        $stmt->execute();
    }

    // finish the update

    echo "<script>alert('Update successfully.');</script>";
    echo "<script>window.location.href='../poll.php';</script>";
?>