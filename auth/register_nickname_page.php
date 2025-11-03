<?php
    include_once '../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Weverse Account</title>
        <link rel="stylesheet" type="text/css" href="../css/login_style.css">
        <link rel="stylesheet" type="text/css" href="../css/weverse.css">
        <style>
            html {
                font-size: 10px;
            }
        </style>
<script src="../js/auth_nickname_check.js"></script>
    </head>
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <body>
        <div id="next">
            <div class="signin-screen">
                <div class="signin-image">
                    <img
                        src="../image/weverse_account.png"
                        width="201"
                        height="18"
                        alt="weverse account"
                        class="signin-image-main">
                </div>
                <div class="signin-box">
                    <h1 class="signin-header">닉네임을 입력해주세요.</h1>
                    <h1
                        class="signin-subheader"
                        style="color: rgb(142, 142, 142); font-weight: 400;">1–32자 길이로 숫자, 특수문자 조합의 공통 닉네임이며, 나중에 계정 설정에서 변경할 수 있습니다.</h1>
                    <div class="signin-nickname-area">
                        <form
                            name="register-nickname-form"
                            method="POST"
                            id="register-nickname-form"
                            action="register_process.php">
                            <div class="form-box">
                                <label class="form-label">닉네임</label>
                                <div class="text-box">
                                    <input
                                        type="text"
                                        aria-required="true"
                                        id="nickname"
                                        name="nickname"
                                        placeholder="nickname"
                                        class="text-input"
                                        oninput="checkNickname()">
                                    <?php
                                    $encrypted_password = password_hash( $_POST['password'], PASSWORD_DEFAULT );
                                    ?>
                                    <input type="hidden" name="email" value="<?php echo $_POST['email']; ?>">
                                    <input type="hidden" name="password" value="<?php echo $encrypted_password; ?>">
                                    <button tabindex="-1" type="button" class="delete-button"></button>
                                    <hr class="text-input-line">
                                </div>
                                <div class="email-check-area">
                                    <strong class="email-check-message" id="nickname-check-message">유효한 닉네임을 입력해주세요.</strong>
                                </div>
                            </div>
                            <div class="signin-button-area">
                                <button
                                    type="submit"
                                    class="continue-login-button"
                                    id="continue-join-button">
                                    <span class="button-text">회원가입</span>
                                </button>
                                <button
                                    type="button"
                                    class="return-previous-page"
                                    onclick="location.href='register_password_page.php'">
                                    이전
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <footer></footer>
    </body>
</html>