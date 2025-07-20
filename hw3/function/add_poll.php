<?php
    session_start();
    require_once "../lib/base.php";
    $conn = getDB();

    $topic = $_POST["topic"]; // select value
    $title = $_POST["title"];
    $option = $_POST["option"]; // option array
    $owner = $_SESSION["email"];

    if(isset($_POST['new_topic']) && $_POST['new_topic'] != "")
    {
        $topic = trim($_POST['new_topic']);
    }

    if($topic == "0" && trim($_POST['new_topic']) == "")
    {
        $_SESSION["error"] = "Please enter the new topic name";
        session_write_close();
        echo "<script>alert('Please enter the new topic name.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    // check if the option is empty or contains only whitespace
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

    // check if the title is already exist in the same topic
    $stmt = $conn->prepare("SELECT * FROM topics WHERE topic_name = ? AND title = ?");
    $stmt->bindParam(1, $topic, PDO::PARAM_STR);
    $stmt->bindParam(2, $title, PDO::PARAM_STR);
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

    // insert the poll topic and title
    // fix timezone for Taipei
    date_default_timezone_set('Asia/Taipei');
    $current_timestamp = date('Y-m-d H:i:s');
    $owner = $_SESSION["email"];

    $stmt = $conn->prepare("INSERT INTO topics (topic_name, title, owner, created_time) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $topic, PDO::PARAM_STR);
    $stmt->bindParam(2, $title, PDO::PARAM_STR);
    $stmt->bindParam(3, $owner, PDO::PARAM_STR);
    $stmt->bindParam(4, $current_timestamp, PDO::PARAM_STR);
    $stmt->execute();

    // get the topic_id
    $stmt = $conn->prepare("SELECT * FROM topics WHERE topic_name = ? AND title = ?");
    $stmt->bindParam(1, $topic, PDO::PARAM_STR);
    $stmt->bindParam(2, $title, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $topic_id = $result[0]["id"];

    // insert the poll options
    foreach($option as $opt)
    {
        $stmt = $conn->prepare("INSERT INTO poll_options (topic_id, item_name) VALUES (?, ?)");
        $stmt->bindParam(1, $topic_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $opt, PDO::PARAM_STR);
        $stmt->execute();
    }

    echo "<script>alert('Poll has been added successfully.');</script>";
    echo "<script>window.location.href='../poll.php';</script>";
?>