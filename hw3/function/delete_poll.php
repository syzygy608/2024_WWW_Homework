<?php
    require_once "../lib/base.php";
    session_start();
    $id = $_GET["id"];
    
    $conn = getDB();

    $stmt = $conn->prepare("SELECT * FROM topics WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // check if the id is exist
    if(count($result) == 0)
    {
        echo "<script>alert('Poll not found.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    // check if the owner is the same as the current user
    if($result["owner"] != $_SESSION["email"] && $_SESSION["email"] != "bey0nd@csie.io")
    {
        echo "<script>alert('You are not the owner of this poll.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    // first delete the poll options
    $stmt = $conn->prepare("DELETE FROM poll_options WHERE topic_id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);

    if(!$stmt->execute())
    {
        echo "<script>alert('Failed to delete poll options.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    // check poll owner

    // then delete the poll topic
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);

    if(!$stmt->execute())
    {
        echo "<script>alert('Failed to delete poll topic.');</script>";
        echo "<script>window.location.href='../poll.php';</script>";
        exit();
    }

    echo "<script>alert('Poll has been deleted successfully.');</script>";
    echo "<script>window.location.href='../poll.php';</script>";
?>