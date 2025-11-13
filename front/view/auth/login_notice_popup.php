<?php
    include_once __DIR__ . '/../../../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weverse</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/main_style.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/feed_style.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/weverse.css">
</head>
<body>
    <div class="wrap">
        <header>
            <div class="header_wrap">
                <div class="headerview_service">
                    <img src="<?= BASE_PATH ?>/res/image/weverse.png" width="136px" height="20px">
                </div>
                <div class="hearderview_action">
                    <input class="signin_btn" type="button" value="Sign in" onclick="location.href='<?= BASE_PATH ?>/front/view/auth/email_check_page.php'">
                </div>
            </div>
        </header>
    </div>
    <div class="Modal">
        <div id="loginNoticeModal" class="Modal__Overlay Modal__Overlay--after-open LoginModalView_modal_overlay" style="z-index: 20003;">
            <div id="popup_modal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="modal" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 428px; max-width: 428px; border-radius: 14px;">
                    <div class="CommonModalView_modal_inner">
                        <strong class="CommonModalView_title">알림</strong>
                        <p class="CommonModalView_description">
                            로그인이 필요합니다.
                            로그인하시겠습니까?
                        </p>
                        <div class="ModalButtonView_button_wrap">
                            <button aria-label="cancel modal" type="button" class="ModalButtonView_button ModalButtonView_-cancel" onclick="location.href='<?= BASE_PATH ?>/front/index.php'">취소</button>
                            <button aria-label="confirm modal" type="button" class="ModalButtonView_button ModalButtonView_-confirm" onclick="location.href='<?= BASE_PATH ?>/front/view/auth/email_check_page.php'">로그인</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>