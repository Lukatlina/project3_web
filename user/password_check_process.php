<?php
include_once '../config/config.php';

/**
 * (변경) 함수가 $pdo를 받도록 수정 (의존성 주입)
 * @param PDO $pdo
 * @param string $email
 * @param string $password
 * @return string "1" (일치) 또는 "0" (불일치)
 */
function comparePassword($pdo, $email, $password) {
    try {
        // 2. (변경) mysqli_* -> PDO Prepared Statement로 변경
        $sql = "SELECT password FROM user WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return "1"; // 일치
        } else {
            return "0"; // 불일치
        }
    } catch (\PDOException $e) {
        error_log("Password check failed: " . $e->getMessage());
        return "0";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'] ?? null;
    $currentPassword = $_POST['current_password'] ?? null;

    // 3. (변경) $pdo 변수를 함수로 전달
    $result = comparePassword($pdo, $email, $currentPassword);

    echo $result;
}
?>