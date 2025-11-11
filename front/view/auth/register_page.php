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
<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<body>
    <div id="next">
        <div class="signin-screen">
            <div class="signin-image">
                <img src="<?= BASE_PATH ?>/res/image/weverse_account.png" width="201" height="18" alt="weverse account" class="signin-image-main">
            </div>
            <div class="signin-box">

            <form method="POST" action="<?= BASE_PATH ?>/front/view/auth/register_password_page.php">
            <h1 class="signin-header"><?php echo $_POST['email'];?></h1>
                <h1 class="signin-subheader">이 이메일은 새로 가입할 수 있는 이메일입니다. 계속하시겠습니까?</h1>
                <div class="signin-button-area">
                <input type="hidden" name="email" value="<?php echo $_POST['email']; ?>">
                    <Button type="submit" class="continue-login-button" Id="continue_login_btn" onclick="location.href='<?= BASE_PATH ?>/front/view/auth/register_password_page.php'">
                        <span class="button-text">가입하기</span>
                    </Button>
            </form>
                
                    <button type="button" class="return-previous-page" onclick="location.href='<?= BASE_PATH ?>/front/view/auth/email_check_page.php'">
                        이전
                    </button>
                </div>
            </div>
        </div>
        <footer></footer>
    </div>
</body>
</html>