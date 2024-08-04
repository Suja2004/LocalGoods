<?php
session_start();
include_once("dbcon.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        header("Location:index.html");
        exit;
    }

    $email = $_POST['email'];

    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        $con->begin_transaction();

        try {
           
            // Manually delete related records in userdata table
            $stmt = $con->prepare("DELETE FROM userdata WHERE user_id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();

            // Delete the user record
            $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();

            $con->commit();

            echo "<script>alert('Account deleted successfully.');
                window.location.href = 'index.html';
                </script>";
        } catch (Exception $e) {
            $con->rollback();
            echo "<script>alert('Failed to delete account: " . addslashes($e->getMessage()) . "');
                window.location.href = 'home.php';
                </script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }
}
?>
