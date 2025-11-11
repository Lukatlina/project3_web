<?php
// 1. (변경) Day 1, 1단계에서 만든 config 파일을 포함합니다.
//    이 파일이 PDO($pdo)와 세션 시작, 에러 리포팅을 모두 처리합니다.
    include_once __DIR__ . '/../../../config/config.php';
// 2. (기존 로직 유지 - 경로만 수정)
//    만약 이메일 없이 이 페이지에 직접 접근하면,
//    같은 auth/ 폴더의 email_check_page.php로 돌려보냅니다.
if (empty($_POST['email'])) {
    // HTTP의 헤더를 전송하는 역할을 하는 함수
    header('Location: ' . BASE_PATH . '/front/view/auth/email_check_page.php');
    // 스크립트의 실행 중지 및 종료
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weverse Account</title>
    <link rel="stylesheet" type="text/css" href = "<?= BASE_PATH ?>/front/css/login_style.css">
    <link rel="stylesheet" type="text/css" href = "<?= BASE_PATH ?>/front/css/weverse.css">
    <style>
        html{font-size: 10px;}    
    </style>
    <script>
        const BASE_PATH = '<?= BASE_PATH ?>';
    </script>
</head>
<body>
    <div id="next">
        <div class="signin-screen">
            <div class="signin-image">
                <img src="<?= BASE_PATH ?>/res/image/weverse_account.png" width="201" height="18" alt="weverse account" class="signin-image-main">
            </div>
            <div class="signin-box">
                <h1 class="signin-header">위버스 계정으로 로그인해주세요.</h1>
                <form method="POST" id="login-form">
                    <div class="form-box">
                        <label class="form-label">이메일</label>
                        <div class="text-box">
                            <input type="text" aria-required="true" name="email"
                            class="text-input" value="<?php echo $_POST['email']; ?>" readonly>
                        </div>
                        <div class="email-check-area">
                            <strong class="email-check-message"></strong>
                        </div>
                    </div>
                    <div class="signin-password-area">
                        <div class="form-box">
                            <label class="form-label">비밀번호</label>
                            <div class="text-box">
                                <input type="password" aria-required="true" name="password" placeholder="비밀번호" Id="login-password"
                                class="text-input" oninput="passwordCheck()">
                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <button tabindex="-1" type="button" class="show-password-button"></button>
                                <hr class="password-input-line">
                            </div>
                            <div class="password-check-area">
                                <strong class="password-check-message" Id="login-password-check-message">유효한 비밀번호를 입력해주세요.</strong>
                            </div>
                            <div>
                                <input type="checkbox" id="auto-login-checkbox" class="checkbox-button">
                                <label for="auto-login-checkbox">자동 로그인</label>
                            </div>
                        </div>
                    </div>
                    <div class="signin-button-area">
                        <button type="submit" class="continue-login-button" Id="login-button" onclick="loginData()" disabled>
                            <span class="button-text">로그인</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <footer></footer>
    </div>
    <script src="<?= BASE_PATH ?>/front/js/auth/auth_login.js"></script>
</body>
</html>