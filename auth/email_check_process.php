<?php
// 1. 뼈대(Config) 파일 포함
// 이 파일 안에 $pdo 객체와 에러 설정이 이미 들어있음.
include_once '../config/config.php';




// (참고) 파일명을 바꿨으니 JS에서 이 파일을 호출해야 합니다.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    
    // 2. 함수 이름 변경 (camelCase 컨벤션 적용)
    $result = checkEmailStatus($email);
    echo $result;
}

/**
 * 이메일 상태를 확인하여 0, 1, 2를 반환합니다.
 * (0: 가입 가능, 1: 로그인 필요, 2: 가입 불가-90일 대기)
 *
 * @param string|null $email 확인할 이메일
 * @return int 상태 코드
 */
function checkEmailStatus($email) {
    // 3. $pdo 변수를 함수 안으로 가져오기
    global $pdo;

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return -1; // 잘못된 요청
    }

    try {
        // 4. PDO Prepare/Execute (SQL Injection 방어)
        $sql = "SELECT email, withdraw_time FROM user WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            // --- 1. 사용자가 존재함 ---
            $withdrawTime = strtotime($row['withdraw_time']);

            if (is_null($row['withdraw_time']) || $withdrawTime === false) {
                // 1-1. 활성 사용자 (탈퇴한 적 없음)
                return 1; // "로그인 페이지"로 이동
            } else {
                // 1-2. 탈퇴한 사용자
                $after90Days = strtotime('+90 days', $withdrawTime);
                $currentTime = time();

                if ($after90Days <= $currentTime) {
                    // 1-3. 90일 지남
                    return 0; // "가입 페이지"로 이동
                } else {
                    // 1-4. 90일 안 지남
                    return 2; // "에러 메시지" 표시
                }
            }
        } else {
            // --- 2. 사용자가 존재하지 않음 (신규 유저) ---
            return 0; // "가입 페이지"로 이동
        }

    } catch (\PDOException $e) {
        // DB 에러 발생
        return -1;
    }
}
?>