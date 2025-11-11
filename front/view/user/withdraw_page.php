<?php 
    include_once __DIR__ . '/../../../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weverse Account</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/user_profile.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/login_style.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/weverse.css">
    <style>
        html{font-size: 10px;}
    </style>
    <script>
        const BASE_PATH = '<?= BASE_PATH ?>';
    </script>
    <script src="<?= BASE_PATH ?>/front/js/user/user_withdraw.js"></script>
</head>
<body style="overflow: auto;">
    <div id="next">
        <header class="user-data-header">
                <a href="/ko" class="user-data-image">
                    <div class="main-image">
                        <img src="<?= BASE_PATH ?>/res/image/weverse_account.png" width="201" height="18" alt="weverse account" class="clickable-main-image">
                    </div>
                </a>
        </header>
        
        <div class="fullscreen-box" style="--display: flex; --flex-direction: column; --justify-content: space-between;">
        <?php
            $email = $_SESSION['email'] ?? null;
            // 2. (초기화) 변수 기본값 설정
            $nickname = '';
            $firstName = '';
            $lastName = '';
            $password = '';


            if ($email) {
                try {
                    // SQL Injection 방지를 위해 ? 사용
                    $sql = "SELECT nickname, first_name, last_name, password FROM user WHERE email = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$email]);

                    // mysqli_fetch_array 대신 fetch 사용
                    $row = $stmt->fetch(PDO::FETCH_ASSOC); 

                    if ($row) {
                        $nickname =  $row[ 'nickname' ];
                        $firstName = $row[ 'first_name' ]; // (수정) first_name
                        $lastName = $row[ 'last_name' ];  // (수정) last_name
                        $password = $row[ 'password' ];
                    }
                } catch (PDOException $e) {
                    // 에러 발생 시 로그 남기기
                    error_log("Failed to fetch user data for withdraw page: " . $e->getMessage());
                }
            }
        ?>
            <header class="withdraw_header">
                <h1 class="withdraw_header_text">Weverse 탈퇴</h1>
                <div class="withdraw_textbox">
                    <strong>유의 사항</strong>
                    <ul>
                        <li>
                            위버스 서비스 탈퇴 시, 계정 정보 복구는 불가하며, 90일 이후 동일 계정으로 재가입 가능합니다. 90일 이후 동일 계정으로 재가입 시, 탈퇴 전 계정 정보 복구는 불가합니다.
                        </li>
                        <li>
                            위버스 서비스 탈퇴 시, 가입한 위버스 이력, 위버스 닉네임, 서비스 활동 이력은 삭제되며 복구가 불가합니다.
                        </li>
                        <li>
                            위버스 서비스 탈퇴 시, 후에도 작성한 포스트와 댓글은 삭제되지 않으며, Unknown 의 게시물로 유지됩니다.
                        </li>
                    </ul>
                </div>
            </header>
            <form id="form_withdraw" class="form_withdraw" method="POST">
                <header>
                    <p>유의 사항을 충분히 숙지하고 동의하신다면, 아래 문구를 직접 입력해주세요.</p>
                    <p>Weverse 탈퇴</p>
                </header>
                <div class="form_withdraw_input_box">
                    <div class="form_withdraw_input">
                        <input type="text" name="confirmText" placeholder="위의 메시지를 똑같이 입력해주세요." class="withdraw_confirmtext" id="withdraw_confirmtext" oninput="compareWithdrawText()">
                        <button tabindex="-1" type="button" class="delete_login"></button>
                        <hr class="form_withdraw_hr">
                    </div>
                </div>
                <div class="form_withdraw_button_box">
                    <button type="button" disabled class="form_draw_btn" id="form_draw_btn" onclick="writeWithdrawText()">
                        <span class="withdraw_btn">Weverse 탈퇴</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>