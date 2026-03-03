<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])) 
{
    $userId = $_SESSION['user_id'];

    $checkStmt = $conn->prepare("SELECT id FROM remember_tokens WHERE user_id = ? AND expires_at > NOW()");
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) 
    {
        $deleteStmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();
        $deleteStmt->close();

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 30 * 24 * 3600);
        
        $insertStmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iss", $userId, $token, $expires);
        $insertStmt->execute();
        $insertStmt->close();

        setcookie('remember_token', $token, time() + 30 * 24 * 3600, '/', '', false, true);
    }
    else
    {
        if (!isset($_COOKIE['remember_token'])) 
        {
            $tokenRow = $checkResult->fetch_assoc();
            $tokenStmt = $conn->prepare("SELECT token FROM remember_tokens WHERE user_id = ? AND expires_at > NOW() ORDER BY expires_at DESC LIMIT 1");
            $tokenStmt->bind_param("i", $userId);
            $tokenStmt->execute();
            $tokenResult = $tokenStmt->get_result();
            
            if ($tokenResult->num_rows > 0) 
            {
                $tokenData = $tokenResult->fetch_assoc();
                setcookie('remember_token', $tokenData['token'], time() + 30 * 24 * 3600, '/', '', false, true);
            }
            
            $tokenStmt->close();
        }
    }
    $checkStmt->close();
}

if (!isset($_SESSION['loggedin']) && isset($_COOKIE['remember_token'])) 
{
    $token = $_COOKIE['remember_token'];
    
    $stmt = $conn->prepare("SELECT u.* FROM users u INNER JOIN remember_tokens rt ON u.id_users = rt.user_id WHERE rt.token = ? AND rt.expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = !empty($row['surname_users']) ? $row['surname_users'] . " " . $row['name_users'] . " " . $row['patronymic_users'] : $row['person_users'];
        $_SESSION['user_id'] = $row['id_users'];

        $updateStmt = $conn->prepare("UPDATE remember_tokens SET expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE token = ?");
        $updateStmt->bind_param("s", $token);
        $updateStmt->execute();
        $updateStmt->close();
        
        setcookie('remember_token', $token, time() + 30 * 24 * 3600, '/', '', false, true);
    }
    else
    {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }
    
    $stmt->close();
}
?>