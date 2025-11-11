<?php
    include_once __DIR__ . '/../../../config/config.php';
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
                <h1 class="signin-header">새 비밀번호를 설정해 주세요.</h1>
                <form method="POST" Id="register-password-form" action="<?= BASE_PATH ?>/front/view/auth/register_nickname_page.php">                                                                                             
                    <div class="signin-password-area">
                        <div class="signin-password-area">
                            <label class="form-label">새로운 비밀번호</label>
                            <div class="text-box">
                                <input type="password" aria-required="true" name="password" id="password" placeholder="비밀번호"
                                class="text-input" oninput="checkPassword()">
                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <button tabindex="-1" type="button" class="show-password-button"></button>
                                <hr class="password-input-line">
                            </div>
                            <div class="password-check-area">
                                <strong class="password-check-rule" id="password-rule-length">8 - 32자</strong>
                                <strong class="password-check-rule" id="password-rule-english">영문 1글자 이상</strong>
                                <strong class="password-check-rule" id="password-rule-number">1글자 이상 숫자</strong>
                                <strong class="password-check-rule" id="password-rule-special">1글자 이상 특수문자</strong>
                                <strong class="password-check-rule"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="check-password-area">
                        <div class="check-password-area">
                            <label class="form-label">새로운 비밀번호 확인</label>
                            <div class="text-box">
                                <input type="password" aria-required="true" name="check-password" id="check-password" placeholder="비밀번호"
                                class="text-input" oninput="checkPassword()">
                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <button tabindex="-1" type="button" class="show-password-button"></button>
                                <hr class="password-input-line">
                            </div>
                            <div class="password-check-area">
                                <strong class="password-check-message" id="password-check-message"></strong>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="email" value="<?php echo $_POST['email']; ?>">
                    <div class="signin-button-area">
                        <button type="submit" class="continue-login-button" id="password-next-button" value="submit" disabled>
                            <span class="button-text">다음</span>
                        </button>
                        <button type="button" class="return-previous-page" onclick="location.href='<?= BASE_PATH ?>/front/view/auth/email_check_page.php'">
                        이전
                    </button>
                    </div>
                </form>
            </div>
        </div>
        <footer></footer>
    </div>
    <script src="<?= BASE_PATH ?>/front/js/auth/auth_password_check.js"></script>
</body>
</html>