<?php 
include_once '../config/config.php';

$email = $_SESSION['email'] ?? null;
// if (empty($email)) {
//     // (개선) 로그인이 안되어있으면 auth/login_page.php로 보냅니다.
//     header("Location: ../auth/login_page.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Weverse Account</title>
    <link rel="stylesheet" type="text/css" href="../css/user_profile.css">
    <link rel="stylesheet" type="text/css" href="../css/login_style.css">
    <link rel="stylesheet" type="text/css" href="../css/weverse.css">

    

    <style>
        html{font-size: 10px;}
    </style>
    <script src="../js/user_profile.js"></script>
    <script src="../js/auth_password_check.js"></script>
    <script src="../js/auth_nickname_check.js"></script>
</head>
<body style="overflow: auto;">
    <div id="next">
        <header class="user-data-header">
                <a class="user-data-image" onclick="location.href='profile_page.php'">
                    <div class="main-image">
                        <img src="../image/weverse_account.png" width="201" height="18" alt="weverse account" class="clickable-main-image">
                    </div>
                </a>
                <button class="user-logout-button" onclick="logoutUser()">로그아웃</button>
        </header>
        
        <div class="fullscreen-box">
        <?php
            try {
                $sql = "SELECT nickname, first_name, last_name, password FROM user WHERE email = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$email]);
                $row = $stmt->fetch();

                $nickname = $row['nickname'] ?? '';
                $firstName = $row['first_name'] ?? '';
                $lastName = $row['last_name'] ?? '';
                $password = $row[ 'password' ] ?? '';

            } catch (\PDOException $e) {
                die("Failed to fetch user data: " . $e->getMessage());
            }
            
            


        ?>
            <div class="screen-box">
                <section class="box-section">
                    <h3 class="box-header">내 정보</h3>
                <dl class="user-data-box">
                    <div class="user-data-item">
                        <dt class="user-label">이메일</dt>
                        <dd class="current-user-data"><?php echo $_SESSION['email'];?></dd>
                    </div>
                    <div class="user-data-item">
                        <dt class="user-label">닉네임</dt>
                        <dd class="current-user-data">
                            <span id="modified-nickname-text"><?php echo $nickname; ?></span>
                            <button type="button" class="mypage-change-button" id="mypage-change-nickname" onclick="openNicknameDialog()">변경</button>
                        </dd>
                    </div>
                    <div class="user-data-item">
                        <dt class="user-label">이름</dt>
                        <dd class="current-user-data">
                            <span id="modified-name-text"><?php echo $last_name . $first_name;?></span>
                            <button type="button" class="mypage-change-button" id="mypage-change-name" onclick="openNameDialog()">변경</button>
                        </dd>
                    </div>
                    <div class="user-data-item">
                        <dt class="user-label">비밀번호</dt>
                        <dd class="current-user-data">
                            <span>
                                <div class="encoded-password">
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                    <span class="circle-password"></span>
                                </div>
                            </span>
                            <button type="button" class="mypage-change-button" id="mypage-change-password" onclick="openPasswordDialog()">변경</button>
                        </dd>
                    </div>
                </dl>
                </section>
                <section class="box-section" style="margin-bottom: 0px; text-align: center;">
                    <button type="submit" class="member-withdraw-button" onclick="location.href='../user/withdraw_page.php'">위버스 계정 탈퇴하기</button>
                </section>
            </div>
        </div> 
    </div>
    <div role="dialog" class="dialog-popup" id="dialog-nickname">
        <div class="dialog-box">
            <header class="dialog-header">
                <h2 class="dialog-header-text">닉네임 변경</h2>
            </header>
            <section class="dialog-section">
                <div class="dialog-inner">
                    <header>
                        <h3>닉네임을 입력해주세요.</h3>
                        <p>1–32자 길이로 숫자, 특수문자 조합의 공통 닉네임이며, 나중에 계정 설정에서 변경할 수 있습니다.</p>
                    </header>
                    <form name="form-nickname" method="POST" id="form-nickname">
                        <div class="change-form-box">
                            <label for="modify-nickname" class="form-label">닉네임</label>
                            <div class="text-box">
                            <input type="hidden" name="email" value="<?php echo $_SESSION['email'];?>">
                                <input
                                    type="text"
                                    aria-required="true"
                                    id="modify-nickname"
                                    name="modify-nickname"
                                    placeholder="nickname"
                                    class="text-input"
                                    value="<?php echo $nickname; ?>"
                                    oninput="checkmodifyNickname()">
                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <hr class="text-input-line">
                            </div>    
                            <div class="form-check-area">
                                <strong class="form-check-message" id="modify-nickname-check">유효한 닉네임을 입력해주세요.</strong>
                            </div>
                        </div>
                        <footer>
                            <button type="button" class="dialog-cancel-button" id="nickname-change-cancel" onclick="closeNicknameDialog()">
                                <span class="button-text">취소</span>
                            </button>
                            <button type="submit" class="dialog-confirm-button" id="nickname-change-complete" onclick="saveNicknameDialog()" disabled>
                                <span class="button-text">저장</span>
                            </button>
                        </footer>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div role="dialog" class="dialog-popup" id="dialog-name">
        <div class="dialog-box">
            <header class="dialog-header">
                <h2 class="dialog-header-text">이름 변경</h2>
            </header>
            <section class="dialog-section">
                <div class="dialog-inner">
                    <header>
                        <h3>이름을 입력해주세요.</h3>
                    </header>
                    <form name="form-name" method="POST" id="form-name">
                    <input type="hidden" name="email" value="<?php echo $_SESSION['email'];?>">
                        <div class="change-form-box">
                            <label for="lastname" class="form-label">성</label>
                            <div class="text-box">
                                <input
                                    type="text"
                                    aria-required="true"
                                    id="lastname"
                                    name="lastname"
                                    placeholder="last name"
                                    class="text-input"
                                    value="<?php echo $last_name; ?>"
                                    oninput="checkmodifyName()">
                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <hr class="text-input-line">
                            </div>    
                            <div class="form-check-area">
                                <strong class="form-check-message" id="modify-lastname-check"></strong>
                            </div>
                        </div>
                        <div class="change-form-box">
                            <label for="firstname" class="form-label">이름</label>
                            <div class="text-box">
                                <input
                                    type="text"
                                    aria-required="true"
                                    id="firstname"
                                    name="firstname"
                                    placeholder="first name"
                                    class="text-input"
                                    value="<?php echo $first_name; ?>"
                                    oninput="checkmodifyName()">
                                <button tabindex="-1" type="button" class="delete-button"></button>
                                <hr class="text-input-line">
                            </div>    
                            <div class="form-check-area">
                                <strong class="form-check-message" id="modify-firstname-check"></strong>
                            </div>
                        </div>
                        <footer>
                            <button type="button" class="dialog-cancel-button" id="name-change-cancel" onclick="closeNameDialog()">
                                <span class="button-text">취소</span>
                            </button>
                            <button type="submit" class="dialog-confirm-button" id="name-change-complete" onclick="saveNameDialog()" disabled>
                                <span class="button-text">저장</span>
                            </button>
                        </footer>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div role="dialog" class="dialog-popup" id="dialog-password">
        <div class="password-dialog-box">
            <header class="dialog-header">
                <h2 class="dialog-header-text">비밀번호 변경</h2>
            </header>
            <section class="dialog-section">
                <div class="dialog-inner">
                    <header>
                        <h3>새 비밀번호를 설정해 주세요.</h3>
                    </header>
                    <form method="POST" id="form-password">
                        <div class="signin-password-area">
                            <div class="signin-password-area">
                                <label for="current-password" class="form-label">현재 비밀번호</label>
                                <div class="text-box">
                                <input type="hidden" name="email" value="<?php echo $_SESSION['email'];?>">
                                    <input type="password" aria-required="true" name="current-password" id="current-password" placeholder="현재 비밀번호"
                                    class="text-input" oninput="checkmodifyPassword()" autocomplete="current-password">
                                    <button tabindex="-1" type="button" class="delete-button"></button>
                                    <button tabindex="-1" type="button" class="show-password-button"></button>
                                    <hr class="password-input-line">
                                </div>
                                <div class="current-password-check-area">
                                    <strong class="current-password-check-message" id="current-password-check-message">기존 비밀번호를 잘못 입력했습니다.</strong>
                                </div>
                            </div>
                        </div>
                        <div class="signin-password-area">
                            <div class="signin-password-area">
                                 <label for="modify-password"class="form-label">새로운 비밀번호</label>
                                <div class="text-box">
                                    <input type="password" aria-required="true" name="modify-password" id="modify-password" placeholder="새로운 비밀번호"
                                    class="text-input" oninput="checkmodifyPassword()" autocomplete="new-password">
                                    <button tabindex="-1" type="button" class="delete-button"></button>
                                    <button tabindex="-1" type="button" class="show-password-button"></button>
                                    <hr class="password-input-line">
                                </div>
                                <div class="password-check-area">
                                    <strong class="password-check-rule" id="modify-password-rule-text-length">8 - 32자</strong>
                                    <strong class="password-check-rule" id="modify-password-rule-text-english">영문 1글자 이상</strong>
                                    <strong class="password-check-rule" id="modify-password-rule-text-number">1글자 이상 숫자</strong>
                                    <strong class="password-check-rule" id="modify-password-rule-text-sc">1글자 이상 특수문자</strong>
                                    <strong class="password-check-rule"></strong>
                                </div>
                            </div>
                        </div>
                        <div class="check-password-area">
                            <div class="check-password-area">
                                <label for="modify-check-password" class="form-label">새로운 비밀번호 확인</label>
                                <div class="text-box">
                                    <input type="password" aria-required="true" name="modify-check-password" id="modify-check-password" placeholder="새로운 비밀번호 확인"
                                    class="text-input" oninput="checkmodifyPassword()" autocomplete="new-password">
                                    <button tabindex="-1" type="button" class="delete-button"></button>
                                    <button tabindex="-1" type="button" class="show-password-button"></button>
                                    <hr class="password-input-line">
                                </div>
                                <div class="password-check-area">
                                    <strong class="password-check-message" id="modify-password-check-message"></strong>
                                </div>
                            </div>
                        </div>
                        <footer>
                            <button type="button" class="dialog-cancel-button" id="password-change-cancel" onclick="closePasswordDialog()">
                                <span class="button-text">취소</span>
                            </button>
                            <button type="submit" class="dialog-confirm-button" id="password-change-complete" onclick="checkCurrentPassword()">
                                <span class="button-text">저장</span>
                            </button>
                        </footer>
                    </form>
                </div>
            </section>
        </div>
    </div>
</body>
</html>