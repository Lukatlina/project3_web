<?php
// header('Cache-Control: no cache'); //no cache
// session_cache_limiter('private_no_expire'); // works

    include_once __DIR__ . '/../config/config.php';
    include_once PROJECT_ROOT .'/back/feature/auth/check_auto_login.php';

// 이동한 페이지에서 뒤로가기를 눌러서 다시 main으로 이동했을 때 양식 다시 제출 확인 오류를 없애기 위한 코드
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Location: ' . BASE_PATH . '/front/index.php');
    exit();
}
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
    <script>
        const BASE_PATH = '<?= BASE_PATH ?>';
    </script>
</head>
<body>
    <div class="wrap">
        <header>
            <div class="header_wrap">
                <div class="headerview_service">
                    <img src="<?= BASE_PATH ?>/res/image/weverse.png" width="136px" height="20px" onclick="location.href='<?= BASE_PATH ?>/front/index.php'">
                </div>
                <div class="hearderview_action">
                    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) : ?>
                        <input class="signin_btn" type="button" value="Sign in" onclick="location.href='<?= BASE_PATH ?>/front/view/auth/email_check_page.php'">
                        
                    <?php else : ?>
                        <button type="button" class="user_data_btn" onclick="location.href='<?= BASE_PATH ?>/front/view/user/profile_page.php'">
                            <img src="<?= BASE_PATH ?>/res/image/userdata_btn_img.png" width="38px" height="38px">
                        </button>
                    <?php endif ?>
                </div>
            </div>
        </header>
        <div class="body">
            <div class="HomePageView_container">
                <div class="HomePageView_home_intro">
                    <div class="banner" style="text-align: center;">
                        <img src="<?= BASE_PATH ?>/res/image/home_banner_slogan.gif" width="531" height="299" alt="We're Starting a New Verse W">
                    </div>
                </div>
                <div class="main">
                    <div class ="container">
                        <h2 class="homeview_title">
                            "아티스트와 소통을 해보세요!"
                        </h2>
                        <div class="HomeComponentView_component">
                            <div>
                                <ul class="homeview_list">
                                    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) : ?>
                                    <li class="HomeArtistListSlotView_artist_item">
                                        <a class="HomeArtistListSlotView_artist_link" >
                                            <div class="HomeArtistListSlotView_artist_cover_wrap">
                                                <img src="<?= BASE_PATH ?>/res/image/newjeans_thumbnail_fanboard.jpeg" width="208" height="208" class="HomeArtistListSlotView_cover_img">
                                            </div>
                                            <div class="HomeArtistListSlotView_artist_thumb_wrap">
                                                <div class="ProfileThumbnailView_thumbnail_area ProfileThumbnailView_-community" style="width: 43px; height: 43px;">
                                                    <div class="ProfileThumbnailView_thumbnail_wrap">
                                                        <div style="aspect-ratio: auto 43 / 43; content-visibility: auto; contain-intrinsic-size: 43px; width: 100%; height: 100%;">
                                                            <img src="<?= BASE_PATH ?>/res/image/newjeans_bunny.jpeg" class="ProfileThumbnailView_thumbnail" width="43" height="43" alt>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="HomeArtistListSlotView_artist_text_wrap" style="text-align: center;">
                                                <strong class="HomeArtistListSlotView_artist_name">
                                                    <div class="MarqueeView_container">
                                                        <span class="MarqueeView_content">
                                                        NewJeans
                                                        </span>
                                                    </div>
                                                </strong>
                                            </div>
                                        </a>
                                    </li>
                                    <?php else : ?>
                                    <li class="HomeArtistListSlotView_artist_item">
                                        <a class="HomeArtistListSlotView_artist_link" onclick="location.href='<?= BASE_PATH ?>/front/view/feed/feed_page.php'">
                                            <div class="HomeArtistListSlotView_artist_cover_wrap">
                                                <img src="<?= BASE_PATH ?>/res/image/newjeans_thumbnail_fanboard.jpeg" width="208" height="208" class="HomeArtistListSlotView_cover_img">
                                            </div>
                                            <div class="HomeArtistListSlotView_artist_thumb_wrap">
                                                <div class="ProfileThumbnailView_thumbnail_area ProfileThumbnailView_-community" style="width: 43px; height: 43px;">
                                                    <div class="ProfileThumbnailView_thumbnail_wrap">
                                                        <div style="aspect-ratio: auto 43 / 43; content-visibility: auto; contain-intrinsic-size: 43px; width: 100%; height: 100%;">
                                                            <img src="<?= BASE_PATH ?>/res/image/newjeans_bunny.jpeg" class="ProfileThumbnailView_thumbnail" width="43" height="43" alt>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="HomeArtistListSlotView_artist_text_wrap" style="text-align: center;">
                                                <strong class="HomeArtistListSlotView_artist_name">
                                                    <div class="MarqueeView_container">
                                                        <span class="MarqueeView_content">
                                                        NewJeans
                                                        </span>
                                                    </div>
                                                </strong>
                                            </div>
                                        </a> 
                                    </li>
                                    <?php endif ?>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="Modal">
        <div id="noticeModal" class="Modal__Overlay Modal__Overlay--after-open NoticeModalView_modal_overlay" style="z-index: 20000;">
            <div id="NoticePopupModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="modal" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 768px; border-radius: 20px;">
                    <div class="NoticeModalView_notice_wrap">
                        <div class="NoticeModalView_header">
                            <h3 class="NoticeModalView_title">[Weverse Con Festival] 2024 Weverse Con Festival - 개최 안내</h3>
                        </div>
                        <div class="NoticeModalView_detail">
                            <div class="WeverseViewer">
                                <div data-media-attachment="photo.3-282560788" style="position: relative; min-height: 981.51px;">
                                    <div class="WidgetMedia WidgetPhoto">
                                        <div class="photo_wrap" style="aspect-ratio: 723 / 1024;">
                                            <img src="<?= BASE_PATH ?>/res/image/2024_WEVERSECON.jpg" class="photo" data-display-placeholder="false" style="aspect-ratio: 723 / 1024;">
                                        </div>
                                    </div>
                                </div>
                                <p class="p">
                                    안녕하세요.
                                    <br>
                                    하이브입니다.
                                    <br>
                                    <br>
                                    글로벌 뮤직&팬 라이프 페스티벌 ‘Weverse Con Festival’이 오는 6월 개최됩니다.
                                    <br>
                                    <br>
                                    지금의 K-팝을 있게 한 한국 대중음악의 레거시를 조명하고, 전 세계에서 사랑받는 아티스트들의 생생한 무대가 펼쳐지는 Weverse Con Festival!
                                    <br>
                                    세대와 장르를 뛰어넘는 다양한 아티스트와 글로벌 팬들이 하나가 되는 축제의 장에서, 글로벌 팬덤 라이프 플랫폼 위버스와 함께하는 색다른 팬 경험을 기대해 주세요.
                                    <br>
                                    <br>
                                    <strong>- 일시 : </strong>2024년 6월 15일(토), 16일(일)
                                    <br>
                                    <strong>- 장소 : </strong>인스파이어 엔터테인먼트 리조트 내 인스파이어 아레나, 디스커버리 파크
                                    <br>
                                    <br>
                                    반짝이는 여름 바다의 파도와 함께 펼쳐질 우리들의 파라다이스, ‘Weverse Con Festival’에 여러분의 많은 관심 부탁드립니다.
                                    <br>
                                    <br>
                                    감사합니다.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="NoticeModalView_button_area">
                        <input type="checkbox" id="NoticeCheckbox" class="CheckBox_button" onclick="closePopup()">
                        <label for="NoticeCheckbox">24시간 동안 다시 열람하지 않습니다.</label>
                    </div>
                </div>
                <button type="button" class="BaseModalView_close_button" onclick="closeNoticeModal()">
                    <span class="blind">close popup</span>
                </button>
            </div>
        </div>
    </div>
    <script src="<?= BASE_PATH ?>/front/js/weverse_main.js"></script>
</body>
</html>