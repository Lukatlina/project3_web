<?php

include_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// AJAX 요청이 POST 방식으로 전송된 경우
    
    $email = $_SESSION['email'] ?? null;

    if (empty($email)) {
        echo "0"; // 실패 (로그인 필요)
        exit();
    }

    try {
        $pdo->beginTransaction(); // (개선) 트랜잭션 시작

        // 2. (변경) 변수명 camelCase 및 PDO 적용
        if (!empty($_POST['modifyNickname'])) {
            // --- 닉네임 변경 ---
            $nickname = $_POST['modifyNickname'];
            $sql = "UPDATE user SET nickname = ?, update_time = now() WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nickname, $email]);

        } elseif (!empty($_POST['lastname']) && !empty($_POST['firstname'])) {
            // --- 이름 변경 ---
            $lastName = $_POST['lastname'];
            $firstName = $_POST['firstname'];
            $sql = "UPDATE user SET first_name = ?, last_name = ?, update_time = now() WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$firstName, $lastName, $email]);

        } elseif (!empty($_POST['modify_password'])) {
            // --- 비밀번호 변경 ---
            $modifyPassword = $_POST['modify_password'];
            $encryptedModifyPassword = password_hash($modifyPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET password = ?, update_time = now() WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$encryptedModifyPassword, $email]);

        } else {
            // 아무 작업도 하지 않음
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