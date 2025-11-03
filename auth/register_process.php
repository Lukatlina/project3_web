<?php
    include_once '../config/config.php';

    // 2. (변경) 변수명을 camelCase로 변경 (POST 변수 가져오기)
    $nickname = $_POST['nickname'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null; // (이전 단계에서 이미 해시된 비밀번호가 넘어옴)

    // 3. (★필수 변경) mysqli_* -> PDO Prepared Statement로 변경
    // PDO의 에러 처리 방식(try...catch) 사용
    try {
        // 4. (변경) SQL Injection 보안 적용: 변수 자리에 물음표(?) 사용
        $sql = "INSERT INTO user (nickname, email, join_time, password) 
                VALUES (?, ?, NOW(), ?)";

        // 5. 쿼리 준비
        $stmt = $pdo->prepare($sql);
        
        // 6. 쿼리 실행 (물음표에 변수를 안전하게 바인딩)
        $stmt->execute([$nickname, $email, $password]);

        // 7. (성공) 회원가입 성공 시 아래의 HTML 표시
        
    } catch (\PDOException $e) {
        // 8. (실패) DB 에러 발생 시 (예: 이메일 중복)
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) 설정으로 인해 예외 발생
        
        // error_log($e->getMessage()); // 실제 서비스에서는 로그를 남깁니다.
        echo "<h1>회원가입 중 오류가 발생했습니다.</h1>";
        echo "<p>오류 내용: " . $e->getMessage() . "</p>";
        echo "<a href='email_check_page.php'>이전 페이지로 돌아가기</a>";
        exit(); // 에러 발생 시 HTML 표시 중단
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weverse Account</title>
    <link rel="stylesheet" type="text/css" href = "../css/login_style.csss">
    <link rel="stylesheet" type="text/css" href = "../css/weverse.css">
    <style>
        html{font-size: 10px;}    
    </style>
</head>
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<body>
    <div id="next">
        <div class="signin-screen">
            <div class="signin-image">
                <img src="../image/weverse_account.png" width="201" height="18" alt="weverse account" class="signin-image-main">
            </div>
            <div class="signin-box">
                <h1 class="signin-header">가입이 완료되었습니다.</h1>
                <h1 class="signin-subheader"
                        style="color: rgb(142, 142, 142); font-weight: 400;">위버스 계정으로 weverse 서비스를 모두 이용할 수 있습니다.</h1>
                <div class="signin-button-area">
                    <Button type="submit" class="continue-login-button" id="back-to-login-button" onclick="location.href='email_check_page.php'">
                        <span class="button-text">로그인으로 돌아가기</span>
                    </Button>
                </div>
            </div>
        </div>
        <footer></footer>
    </div>

</body>
</html>