<?php
include_once '../config/config.php';

// 2. (변경) 변수명을 camelCase로 변경
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$autoLogin = $_POST['autologin'] ?? 'false'; // 'true' 또는 'false' 문자열로 받음

/**
 * 사용자 로그인을 검증하고 세션을 생성합니다.
 *
 * @param PDO $pdo PDO 연결 객체
 * @param string $email 사용자 이메일
 * @param string $password 사용자 비밀번호
 * @return string "1" (성공) 또는 "0" (실패)
 */
function validateUserLogin($pdo, $email, $password) {
    // 3. (변경) mysqli_* -> PDO Prepared Statement로 변경 (SQL Injection 방어)
    $sql = "SELECT user_number, password FROM user WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 4. (변경) mysqli_fetch_assoc -> fetch() 결과 및 password_verify로 검증
    if ($user && password_verify($password, $user['password'])) {
        // 성공: 세션 데이터 생성
        $_SESSION['email'] = $email;
        $_SESSION['user_number'] = $user['user_number'];
        $_SESSION['loggedin'] = true;
        return "1";
    } else {
        // 실패: 세션 데이터 삭제 (혹시 모를 경우 대비)
        unset($_SESSION['email']);
        unset($_SESSION['user_number']);
        unset($_SESSION['loggedin']);
        return "0";
    }
}

/**
 * 자동 로그인을 위한 토큰을 DB에 저장하고 쿠키를 설정합니다.
 *
 * @param PDO $pdo PDO 연결 객체
 * @param string $email 사용자 이메일
 */
function setAutoLoginToken($pdo, $email) {
    try {
        $token = uniqid('', true); // 더 강력한 토큰 생성
        
        // 5. (변경) UPDATE 쿼리도 PDO Prepared Statement로 변경
        $sql = "UPDATE user SET uniq_id = ?, update_time = NOW() WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token, $email]);

        // 쿠키 설정 (30일)
        setcookie("UserToken", $token, time() + (86400 * 30), "/");
        return true;
    } catch (\PDOException $e) {
        // 에러 로그 기록 (실제 서비스에서 중요)
        // error_log($e->getMessage());
        return false;
    }
}

// 6. (변경) $pdo 변수를 함수로 전달
$loginResult = validateUserLogin($pdo, $email, $password);

if ($loginResult === "1") {
    // 로그인 성공
    if ($autoLogin === 'true') {
        // 자동 로그인 체크 시 토큰 발행
        setAutoLoginToken($pdo, $email);
    }
    echo "1";
} else {
    // 로그인 실패
    echo "0";
}


?>
