<?php
    require_once "../lib/base.php";
    session_start();
    $topic_id = $_GET["topic_id"];
    $option_id = $_GET["option_id"];
    $user_email = $_SESSION["email"];

    $user_info = get_user_info($user_email);

    $conn = getDB();

    // 檢查是否有這個 topic
    $stmt = $conn->prepare("SELECT * FROM topics WHERE id = ?");
    $stmt->bindParam(1, $topic_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result == null)
    {
        echo "<script>alert('Topic not found.');</script>";
        echo "<script>window.location.href = '../home.php';</script>";
        exit();
    }

    // 檢查是否有這個 option
    $stmt = $conn->prepare("SELECT * FROM poll_options WHERE topic_id = ? AND id = ?");
    $stmt->bindParam(1, $topic_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $option_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result == null)
    {
        echo "<script>alert('Option not found.');</script>";
        echo "<script>window.location.href = '../home.php';</script>";
        exit();
    }

    // 檢查是否已經投過票
    $stmt = $conn->prepare("SELECT * FROM vote_record WHERE username = ? AND topic_id = ?");
    $stmt->bindParam(1, $user_info["username"], PDO::PARAM_STR);
    $stmt->bindParam(2, $topic_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    
    // 假設完全一致，視為取消投票
    if($result != null && $result["option_id"] == $option_id)
    {
        // 更新 option 的票數
        $stmt = $conn->prepare("UPDATE poll_options SET vote_count = vote_count - 1 WHERE topic_id = ? AND id = ?");
        $stmt->bindParam(1, $topic_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $option_id, PDO::PARAM_INT);
        $stmt->execute();

        // 刪除投票紀錄
        $stmt = $conn->prepare("DELETE FROM vote_record WHERE username = ? AND topic_id = ?");
        $stmt->bindParam(1, $user_info["username"], PDO::PARAM_STR);
        $stmt->bindParam(2, $topic_id, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount() > 0)
        {
            echo "<script>alert('Cancel vote successfully.');</script>";
            echo "<script>window.location.href = '../home.php';</script>";
        }
        else
        {
            echo "<script>alert('Cancel vote failed.');</script>";
            echo "<script>window.location.href = '../home.php';</script>";
        }
        exit();
    }

    if($result != null)
    {
        echo "<script>alert('You have already voted!');</script>";
        echo "<script>window.location.href = '../home.php';</script>";
        exit();
    }

    // 更新 option 的票數
    $stmt = $conn->prepare("UPDATE poll_options SET vote_count = vote_count + 1 WHERE topic_id = ? AND id = ?");
    $stmt->bindParam(1, $topic_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $option_id, PDO::PARAM_INT);
    $stmt->execute();

    // 新增投票紀錄
    date_default_timezone_set('Asia/Taipei');
    $vote_time = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO vote_record (topic_id, option_id, username, vote_time) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $topic_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $option_id, PDO::PARAM_INT);
    $stmt->bindParam(3, $user_info["username"], PDO::PARAM_STR);
    $stmt->bindParam(4, $vote_time, PDO::PARAM_STR);

    $stmt->execute();

    if($stmt->rowCount() > 0)
    {
        echo "<script>alert('Vote successfully.');</script>";
        echo "<script>window.location.href = '../home.php';</script>";
    }
    else
    {
        echo "<script>alert('Vote failed.');</script>";
        echo "<script>window.location.href = '../home.php';</script>";
    }

?>