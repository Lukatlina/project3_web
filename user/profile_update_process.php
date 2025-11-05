<?php

include_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// AJAX 요청이 POST 방식으로 전송된 경우
    
    $email = $_SESSION['email'] ?? null;

    if (empty($email)) {
        echo "0"; // 실패 (로그인 필요)
        exit();
    }

$modifyNickname = $_POST['modify-nickname'] ?? null;
    $lastName = $_POST['lastname'] ?? null;
    $firstName = $_POST['firstname'] ?? null;
    $modifyPassword = $_POST['modify-password'] ?? null;

    try {
        $pdo->beginTransaction(); // (개선) 트랜잭션 시작

        // 3. (변경) PDO Prepared Statement로 변경
        if (!empty($modifyNickname)) {
            // --- 닉네임 변경 ---
            $sql = "UPDATE user SET nickname = ?, update_time = now() WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$modifyNickname, $email]);

        } elseif (!empty($lastName) || !empty($firstName)) {
            // --- 이름 변경 ---
            $sql = "UPDATE user SET first_name = ?, last_name = ?, update_time = now() WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$firstName, $lastName, $email]);

        } elseif (!empty($modifyPassword)) {
            // --- 비밀번호 변경 ---
            $encryptedModifyPassword = password_hash($modifyPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET password = ?, update_time = now() WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$encryptedModifyPassword, $email]);

        } else {
            throw new Exception("No data received.");
        }

        $pdo->commit(); // 모든 작업 성공 시 최종 저장
        echo "1"; // 성공

    } catch (\Exception $e) {
        $pdo->rollBack(); // 실패 시 롤백
        error_log("Profile update failed: " . $e->getMessage());
        echo "0"; // 실패
    }
}
?>