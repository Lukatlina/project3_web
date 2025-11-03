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
        <script src="../js/auth_email.js"></script>
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
                    <h1 class="signin-header">위버스 계정으로 로그인이나 회원가입해주세요.</h1>
                    <form name="checkEmail" method="POST" id="email-check-form">
                        <div class="form-box">
                            <label class="form-label">이메일</label>
                            <div class="text-box">
                                <input
                                    type="text"
                                    aria-required="true"
                                    id="email"
                                    name="email"
                                    placeholder="your@email.com"
                                    class="text-input"
                                    oninput="printEmail()"
                                    value>

                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <hr class="text-input-line">
                            </div>
                            <div class="email-check-area">
                                <strong class="email-check-message" id="email-check-message"></strong>
                            </div>
                        </div>
                        <div class="signin-button-area">
                            <button
                                type="submit"
                                class="continue-login-button"
                                id="continue-login-button"
                                onclick="loadData()"
                                disabled="disabled">
                                <span class="button-text">이메일로 계속하기</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <footer></footer>
        </div>
    </body>
</html>