<?php

    // header('Cache-Control: no cache'); //no cache
    // session_cache_limiter('private_no_expire'); // works

    include_once __DIR__ . '/../../../config/config.php';
    include_once PROJECT_ROOT .'/back/feature/auth/check_auto_login.php';
    include_once PROJECT_ROOT .'/back/common/functions.php';

    // 이동한 페이지에서 뒤로가기를 눌러서 다시 main으로 이동했을 때 양식 다시 제출 확인 오류를 없애기 위한 코드
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header('Location: ' . BASE_PATH . '/front/view/feed/feed_page.php');
        exit();
    }

    // TODO: 할 일
    /*
    1. 동영상 CRUD 추가
    2. 좋아요 기능 추가
    3. 페이징 기능 추가
    4. 프레임 워크 사용해서 기능 추가
    5. 개인 유저 페이지에서 사진 추가 가능하도록 만들기
    */
    // FIXME: 수정할 일
    /*
    1. 로그인이 풀렸을 때 로그인 페이지로 넘어가도록 만들기
    2. 게시판에서 텍스트가 일정 길이 이상이거나 사진 갯수가 일정 갯수 이상이면 내용이 생략되거나 프리뷰처럼 보이도록 만들기
    3. 윈도우 크롬에서 웹페이지가 보이도록 만들기
    */

    // TODO: 동영상 Create
    /*
    2. 웹 브라우저에서 formData에 추가하기 전에 유저가 삭제한 데이터가 있는지 한번 확인한다.
    3. 길이가 일치하면 그대로 진행하고 아니라면 for문으로 검사한 후 추가한다.
    4. 검사한 데이터를 웹 서버로 다시 보내 준 후에 임시 폴더에 있는 데이터를 본 폴더로 옮겨준다.
    5. 옮긴 데이터를 동영상 먼저 저장한다.
    6. 저장 후 생성된 동영상 video_id를 이미지 저장시 썸네일 이미지와 함께 저장해 준다.
    7. 세부 게시판 글로 들어갔을 때 video 이미지가 제대로 불러와지는지 확인한다.
    */

        
            
?>


<!DOCTYPE html>
<html class="scrollbar-custom use-webfont">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color"content="#fff">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weverse</title>
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/feed_style.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>/front/css/weverse.css">
    <script>
        const BASE_PATH = '<?= BASE_PATH ?>';
    </script>
</head>
<body>
    <div class="root">
        <div class="Toastify" id="WEV2-TOAST-CONTAINER-ID"></div>
        <div class="App" style>
            <div class="GlobalLayoutView_layout_container" data-is-responsive-mode="false">
                <div class="GlobalLayoutView_header">
                    <header class="header">
                        <div class="HeaderView_content">
                            <div class="HeaderView_service">
                                <a href="<?= BASE_PATH ?>/front/index.php">
                                    <img src="<?= BASE_PATH ?>/res/image/weverse.png" width="136px" height="20px">
                                </a>
                            </div>
                            <div class="HeaderView_action">
                                <button class="user_data_btn" onclick="location.href='<?= BASE_PATH ?>/front/view/user/profile_page.php'">
                                    <img src="<?= BASE_PATH ?>/res/image/userdata_btn_img.png" width="38px" height="38px">
                                </button>
                            </div>
                        </div>
                    </header>
                </div>
                <div class="body">

                <?php
                    $userNumber = (int)($_SESSION['user_number'] ?? 0);
                    $modifiedBoardNumber = (int)($_POST['Modify_board_number'] ?? 0);
                    
                    $nickname = '';

                    try {
                        // PDO 쿼리로 닉네임 가져오기
                        $sql = "SELECT nickname FROM user WHERE user_number = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$userNumber]);
                        $userRow = $stmt->fetch(); // fetch() 사용

                        if ($userRow) {
                            $nickname = $userRow['nickname'];
                        }
                    } catch (PDOException $e) {
                        error_log("Failed to fetch user nickname: " . $e->getMessage());
                    }
                ?>

                    <div class="CommunityNavigationLayoutView_navigation">
                        <nav class="CommunityHeaderNavigationView_community_header_navigation"
                        style="background-image: linear-gradient(90deg, #07D8E2 54.07%, #35e99d 99.24%);">
                            <a href="newjeansofficial/feed.php" class="CommunityHeaderNavigationView_link" aria-current="false">Feed</a>
                            <a href="newjeansofficial/artist.php" class="CommunityHeaderNavigationView_link" aria-current="false">Artist</a>
                        </nav>
                    </div>
                    <div class="CommunityNavigationLayoutView_content">
                        <div class="container">
                            <div class="FeedArtistLayoutView_content">
                                <div class="FeedArtistLayoutView_main FeedArtistLayoutView_feed">
                                    <!-- 1. 포스트 작성창을 연다 -->
                                    <div class="EditorInputView_editor_input_wrap" data-client="FEED" data-testid="write" onclick="openWriteTextModal()">
                                        <div role="button" tabindex="0" class="DivAsButtonView_div_as_button EditorInputView_input_button">
                                            <div class="EditorInputView_thumbnail_area">
                                                <div class="ProfileThumbnailView_thumbnail_area" style="width: 46px; height: 46px;">
                                                    <div class="ProfileThumbnailView_thumbnail_wrap ProfileThumbnailView_-has_border">
                                                        <div style="aspect-ratio: auto 46 / 46; content-visibility: auto; contain-intrinsic-size: 46px; width: 100%; height: 100%;">
                                                            <img src="<?= BASE_PATH ?>/res/image/icon_empty_profile.png" class="ProfileThumbnailView_thumbnail" width="46" height="46" alt>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <em class="EditorInputView_text_area">위버스에 포스트를 남겨보세요.</em>
                                        </div>
                                        <div class="EditorInputView_attach_area">
                                            <!-- 1. 이미지 버튼을 누른다. -->
                                            <label for="apei" class="EditorInputView_attach_button" data-testid="attach-photo">
                                                <span class="blind">Attach photo</span>
                                                <input class="blind" id="apei" type="file" multiple accept="image/*">
                                            </label>
                                            <!-- 1. 동영상 버튼 클릭시 동영상 선택을 할 수 있음 -->
                                            <label for="avei" class="EditorInputView_attach_button EditorInputView_-video">
                                                <span class="blind">Attach photo</span>
                                                <input class="blind" id="avei" type="file" multiple accept="video/mp4, video/*">
                                            </label>
                                        </div>
                                    </div>
                                    <?php
                                        const POSTS_PER_PAGE = 20;
                                        $posts = [];

                                        try {
                                            // 1. (변경) PDO 쿼리 준비 (LIMIT 포함)
                                        $sql = "SELECT board_number, board.user_number, contents, contents_save_time, cheering, nickname 
                                                FROM board 
                                                LEFT JOIN user ON board.user_number=user.user_number 
                                                ORDER BY board.board_number DESC 
                                                LIMIT :limit";
                                        $stmt = $pdo->prepare($sql);
                                        
                                        // 2. LIMIT 값 바인딩
                                        $limitCount = (int)POSTS_PER_PAGE;
                                        $stmt->bindParam(':limit', $limitCount, PDO::PARAM_INT);
                                        $stmt->execute();

                                        // 3. fetchAll()로 20개 게시물 한 번에 가져오기
                                        $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($allPosts as $post) {
                                            $boardNumber = (int)$post['board_number'];
                                            $boardUserNumber = (int)$post['user_number']; // writeUserNumber
                                            $contents = $post['contents'];
                                            $contentsSaveTime = $post['contents_save_time'];

                                            // 5. (★핵심 변경) 'include find_image.php' -> 'injectMediaPaths()' 함수 호출
                                            $processedContents = injectMediaPaths($pdo, $boardNumber, $contents, 'feed');

                                            // 6. (변경) 날짜 포맷팅
                                            $dateTime = new DateTime($contentsSaveTime);
                                            if ($dateTime->format('Y') === date('Y')) {
                                                $formattedDateTime = $dateTime->format('m. d. H:i');
                                            } else {
                                                $formattedDateTime = $dateTime->format('Y. m. d. H:i');
                                            }

                                            // 7. (★핵심 변경) 'include count_likes.php' -> 'getLikeStatus()' 함수 호출
                                            // $userNumber는 이 파일 상단(7-1)에서 정의했습니다.
                                            $likeStatus = getLikeStatus($pdo, $userNumber, $boardNumber, null); 

                                            // 8. (변경) $posts 배열에 camelCase 키로 저장 (JS와 일치시킴)
                                            $posts[] = [
                                                'id' => $boardNumber,
                                                'writeUserNumber' => $boardUserNumber,
                                                'writeUserNickname' => $post['nickname'],
                                                'dateTime' => $formattedDateTime,
                                                'contents' => $processedContents,
                                                'cheering' => $post['cheering'],
                                                'likesRowCount' => $likeStatus
                                            ];
                                        }

                                        } catch (PDOException $e) {
                                            error_log("Failed to fetch initial posts: " . $e->getMessage());
                                        }
                                        ?>

                                    <div class="FeedPostListView_container">
                                        <div>
                                            <div class="FeedPostListView_list_wrap">
                                                <?php foreach ($posts as $post) : ?>
                                                <div id="PostListItemView_post_item<?php echo $post['id']; ?>" class="PostListItemView_post_item" data-id="<?php echo $post['id']; ?>">
                                                    <div class="PostHeaderView_header_wrap PostHeaderView_-header_type_feed">
                                                        <div class="PostHeaderView_group_wrap PostHeaderView_-profile_area">
                                                            <a class="PostHeaderView_thumbnail_wrap">
                                                                <div class="ProfileThumbnailView_thumbnail_area" style="width: 36px; height: 36px;">
                                                                    <div class="ProfileThumbnailView_thumbnail_wrap ProfileThumbnailView_-has_border">
                                                                        <div style="aspect-ratio: auto 36 / 36; content-visibility: auto; contain-intrinsic-size: 36px; width: 100%; height: 100%;">
                                                                            <img class="ProfileThumbnailView_thumbnail" src="<?= BASE_PATH ?>/res/image/icon_empty_profile.png" width="36" height="36" alt>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <div class="PostHeaderView_text_wrap">
                                                                <a href="">
                                                                    <div class="PostHeaderView_nickname_wrap">
                                                                        <strong id="PostHeaderView_nickname<?php echo $post['id']; ?>" class="PostHeaderView_nickname"><?php echo $post['writeUserNickname'];?></strong>
                                                                    </div>
                                                                </a>
                                                                <div class="PostHeaderView_info_wrap">
                                                                    <span id="PostHeaderView_date<?php echo $post['id']; ?>" class="PostHeaderView_date">
                                                                        <?php echo $post['dateTime']; ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div></div>
                                                    </div>
                                                    <div class="PostListItemView_content_wrap">
                                                        <div class="PostListItemView_content_item"></div>
                                                        <div class="PostListItemView_content_item PostListItemView_-text_preview">
                                                            <div id="PostPreviewTextView_text<?php echo $post['id']; ?>" class="PostPreviewTextView_text" onclick="location.href='<?= BASE_PATH ?>/front/view/feed/post_detail_page.php?board_number=<?php echo $post['id'];?>'">
                                                                <?php echo $post['contents']; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="PostListItemView_button_wrap">
                                                        <div class="PostListItemView_group_wrap">
                                                            <div class="PostListItemView_button_item">
                                                                <button id="EmotionButtonView_button_emotion<?php echo $post['id']; ?>" type="button" class="EmotionButtonView_button_emotion" aria-pressed="false" onclick="changeMaximumLikes(<?php echo $post['id']; ?>)">
                                                                    <?php
                                                                    if ($post['likesRowCount'] === 1) : ?>
                                                                        <svg id="like_btn<?php echo $post['id']; ?>" class="add_like liked" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            
                                                                        </svg>
                                                                        <span class="blind">cheering</span>
                                                                        <?php echo $post['cheering']; ?>
                                                                    <?php else : ?>
                                                                        <svg id="like_btn<?php echo $post['id']; ?>" class="add_like" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            
                                                                            </svg>
                                                                            <span class="blind">cheering</span>
                                                                            <?php 
                                                                            if ($post['cheering'] == 0) {
                                                                                NULL;
                                                                            }else {
                                                                                echo $post['cheering'];
                                                                            }
                                                                            ?>
                                                                    <?php endif ?>
                                                                </button>
                                                            </div>
                                                            <div class="PostListItemView_button_item">
                                                                <button type="button" class="CommentButtonView_button_comment">
                                                                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M22.7912 12.25C22.7912 6.98327 18.5168 2.7088 13.25 2.7088C7.98327 2.7088 3.7088 6.98327 3.7088 12.25C3.7088 16.2846 6.21678 19.7303 9.74976 21.1261C9.74976 21.1261 9.79338 21.1479 9.82609 21.1588C10.2295 21.3115 10.6439 21.4423 11.0692 21.5405C14.5258 22.4455 18.6258 22.2819 20.5995 21.9548C21.1338 21.8567 21.341 21.3878 21.0684 20.908C20.774 20.3846 20.3596 19.7522 20.2833 19.1851C20.0325 17.2769 22.7912 16.0229 22.7803 12.3591C22.7803 12.3264 22.7803 12.2936 22.7803 12.2609L22.7912 12.25Z" stroke="#444444" stroke-width="1.6" stroke-miterlimit="10"></path>
                                                                    </svg>
                                                                    <span class="blind">Leave a comment</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="PostListItemView_group_wrap">
                                                            <div class="PostListItemView_button_menu_wrap">
                                                                <div>
                                                                    <button type="button" id="MoreButtonView_button_menu<?php echo $post['id']; ?>" class="MoreButtonView_button_menu" data-id="<?php echo $post['id']; ?>" onclick="clickListBox(<?php echo $post['id']; ?>)">
                                                                        <span class="blind">Show More Content</span>
                                                                    </button>
                                                                    
                                                                    <?php
                                                                    if ($userNumber === $post['writeUserNumber']) : ?>
                                                                        <ul id="DropdownOptionListView<?php echo $post['id']; ?>" class="DropdownOptionListView_option_list DropdownOptionListView_dropdown-action" role="listbox" data-use-placement="true" data-placement="top" >
                                                                        <li class="DropdownOptionListView_option_item" role="presentation" >
    
                                                                        <button type="button" class="ContentMetaActionLayerView_button_item ContentMetaActionLayerView_-edit" onclick="openModifyPostModal(<?php echo $post['id']; ?>)" >
                                                                                수정하기
                                                                            </button>
                                                                        </li>
                                                                        <li class="DropdownOptionListView_option_item" role="presentation">
                                                                            <button class="ContentMetaActionLayerView_button_item ContentMetaActionLayerView_-delete" onclick="openDeletePostModal(<?php echo $post['id']; ?>)">
                                                                                삭제하기
                                                                            </button>
                                                                        </li>
                                                                    </ul>
                                                                    <?php else : ?>
                                                                        <ul id="DropdownOptionListView<?php echo $post['id']; ?>" class="DropdownOptionListView_option_list DropdownOptionListView_dropdown-action" role="listbox" data-use-placement="true" data-placement="top">
                                                                        <li class="DropdownOptionListView_option_item" role="presentation">
                                                                            <button class="ContentMetaActionLayerView_button_item ContentMetaActionLayerView_-report">
                                                                                신고하기
                                                                            </button>
                                                                        </li>
                                                                        <li class="DropdownOptionListView_option_item" role="presentation">
                                                                            <button class="ContentMetaActionLayerView_button_item ContentMetaActionLayerView_-block">
                                                                                작성자 차단
                                                                            </button>
                                                                        </li>
                                                                    </ul>
                                                                    <?php endif ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="loader">
                                                <span class="blind">Loading</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="FeedArtistLayoutView_aside">
                                    <div class="CommunityAsideView_container">
                                        <div class="CommunityAsideView_aside_item">
                                            <div class="CommunityAsideWelcomeView_container">
                                                <div class="CommunityAsideWelcomeView_thumbnail_wrap">
                                                    <div style="aspect-ratio: auto 353 / 370; content-visibility: auto; contain-intrinsic-size: 353px 370px;">
                                                        <img src="<?= BASE_PATH ?>/res/image/newjeans_thumbnail_fanboard.jpeg" class="CommunityAsideWelcomeView_thumbnail" width="353" height="370" alt>
                                                    </div>
                                                    <div class="CommunityAsideWelcomeView_gradation" style="background-image: linear-gradient(90deg, rgb(15, 15, 15) 0%, rgb(82, 82, 82) 100%);"></div>
                                                </div>
                                                <div class="CommunityAsideWelcomeView_info">
                                                    <div class="CommunityAsideWelcomeView_member">0 members</div>
                                                    <div class="CommunityAsideWelcomeView_community">
                                                        <div class="MarqueeView_container">
                                                            <span class="MarqueeView_content">NewJeans</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="CommunityAsideView_aside_item">
                                            <div style="aspect-ratio: auto 353 / 171; content-visibility: auto; contain-intrinsic-size: 353px 171px;">
                                                <a href="" class="CommunityAsideMyProfileView_community_profile">
                                                    <div class="CommunityAsideMyProfileView_profile_thumbnail">
                                                        <div class="ProfileThumbnailView_thumbnail_area" style="width: 64px; height: 64px;">
                                                            <div class="ProfileThumbnailView_thumbnail_wrap">
                                                                <div style="aspect-ratio: auto 64 / 64; content-visibility: auto; contain-intrinsic-size: 64px; width: 100%; height: 100%;">
                                                                    <img src="<?= BASE_PATH ?>/res/image/icon_empty_profile.png" class="ProfileThumbnailView_thumbnail" width="64" height="64" alt>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="CommunityAsideMyProfileView_profile_name">
                                                        <strong class="CommunityAsideMyProfileView_name_text"><?php echo $nickname;?></strong>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed_bottom_layer FixedBottomLayerView_fixed_wrap"></div>
    </div>

    <div class="Modal">
        <div id="post-create-modal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20003;">
            <div id="editorWriteModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="포스트 쓰기" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 767px; border-radius: 20px;">
                    <div class="EditorModalLayoutView_container">
                        
                        <div class="EditorModalLayoutView_title_area">
                            <strong class="title">포스트 쓰기</strong>
                            <em class="EditorWriteModalView_artist">NewJeans</em>
                        </div>
                        <div class="content EditorModalLayoutView_content">
                            <div class="WeverseEditor">
                                <div class="editor" id="editor-wevEditor" style="min-height: 348px;">
                                    <div id="post-editor-input" contenteditable="true" style="position: relative; outline: none;" class="cke_editable cke_editable_inline cke_contents_ltr placeholder" tabindex="0" spellcheck="false" role="textbox" aria-label="false">
                                        위버스에 남겨보세요...
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="EditorModalLayoutView_footer_area">
                            <div class="EditorWriteModalFooterView_container">
                                <div class="EditorWriteModalFooterView_button_area">
                                    <div class="EditorWriteModalFooterView_button_icon_wrap -photo">
                                        <label for="post-image-input" class="EditorWriteModalFooterView_button_icon">
                                            <!-- 2. 이미지 버튼을 누른다. -->
                                            <span class="blind">Attach photo</span>
                                            <!-- 3. 유저가 이미지 파일을 선택한다. -->
                                            <input class="blind" id="post-image-input" type="file" multiple accept="image/*">
                                        </label>
                                    </div>
                                    <!-- 1. 동영상 버튼 클릭시 동영상 선택을 할 수 있음 -->
                                    <div class="EditorWriteModalFooterView_button_icon_wrap EditorWriteModalFooterView_-video">
                                        <label for="post-video-input" class="EditorWriteModalFooterView_button_icon">
                                            <span class="blind">Attach photo</span>
                                            <input class="blind" id="post-video-input" type="file" multiple accept="video/mp4, video/*">
                                        </label>
                                    </div>
                                </div>
                                <div class="EditorWriteModalFooterView_button_area">
                                    <button type="button" onclick="savePost()" id="post-submit-button" class="EditorWriteModalFooterView_button_submit" disabled>등록</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="BaseModalView_close_button" onclick="closePostCreateModal()">
                    <span class="blind">close popup</span>
                </button>
            </div>
        </div>
    </div>

    <div class="Modal">
        <div id="ModifyModal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20003;">
            <div id="ModifyeditorWriteModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="포스트 수정" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 767px; border-radius: 20px;">
                    <div class="EditorModalLayoutView_container">
                        <div class="EditorModalLayoutView_title_area">
                            <strong class="title">포스트 수정</strong>
                            <em class="EditorWriteModalView_artist">NewJeans</em>
                        </div>
                        <div class="content EditorModalLayoutView_content">
                            <div class="WeverseEditor">
                                <div class="editor" id="Modifyeditor-wevEditor" style="min-height: 348px;">
                                    <div id="Modify_wevEditor" contenteditable="true" style="position: relative; outline: none;" class="cke_editable cke_editable_inline cke_contents_ltr" tabindex="0" spellcheck="false" role="textbox" aria-label="false">
                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="EditorModalLayoutView_footer_area">
                            <div class="EditorWriteModalFooterView_container">
                                <div class="EditorWriteModalFooterView_button_area">
                                    <div class="EditorWriteModalFooterView_button_icon_wrap -photo">
                                        <label for="modify-image-input" class="EditorWriteModalFooterView_button_icon">
                                            <span class="blind">Attach photo</span>
                                            <input class="blind" id="modify-image-input" type="file" multiple accept="image/*">
                                        </label>
                                    </div>
                                    <div class="EditorWriteModalFooterView_button_icon_wrap EditorWriteModalFooterView_-video">
                                        <label for="modify-video-input" class="EditorWriteModalFooterView_button_icon">
                                            <span class="blind">Attach photo</span>
                                            <input class="blind" id="modify-video-input" type="file" multiple accept="video/mp4, video/*">
                                        </label>
                                    </div>
                                </div>
                                <div class="EditorWriteModalFooterView_button_area">
                                    <button type="button" onclick="saveModifiedPost()" id="Modify_Modal_submit_btn" class="EditorWriteModalFooterView_button_submit" disabled>등록</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="BaseModalView_close_button" onclick="returnBoardNotModify()">
                    <span class="blind">close popup</span>
                </button>
            </div>
        </div>
    </div>
    <div class="Modal">
        <div id="modifyConfirmPostModal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20003;">
            <div id="modifyPopupModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="modal" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 428px; max-width: 428px; border-radius: 14px;">
                    <div class="CommonModalView_modal_inner">
                        <p class="CommonModalView_description">
                            수정을 취소하시겠습니까?
                        </p>
                        <div class="ModalButtonView_button_wrap">
                            <form>
                                <input id="Modify_board_number" type="hidden" name="Modify_board_number" value="<?php echo $board_number;?>">
                                <button aria-label="cancel modal" type="button" class="ModalButtonView_button ModalButtonView_-cancel" onclick="returnModifyModal()">취소</button>
                                <button aria-label="confirm modal" type="button" class="ModalButtonView_button ModalButtonView_-confirm" onclick="closeModifyConfirmModal()">확인</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="Modal">
        <div id="deletePostModal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20003;">
            <div id="deletePopupModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="modal" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 428px; max-width: 428px; border-radius: 14px;">
                    <div class="CommonModalView_modal_inner">
                        <p class="CommonModalView_description">
                            이 게시글을 삭제하시겠습니까?
                        </p>
                        <div class="ModalButtonView_button_wrap">
                            <button aria-label="cancel modal" type="button" class="ModalButtonView_button ModalButtonView_-cancel" onclick="closeDeletePostModal()">취소</button>
                            <button aria-label="confirm modal" type="button" class="ModalButtonView_button ModalButtonView_-confirm" onclick="completeDeletedPost()">확인</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 이미지 프리뷰를 보여주기 위한 사진 올리기 화면 -->
    <div class="Modal">
        <div id="previewPhotoModal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20005;">
        <!-- attachment 해당 요소가 첨부 파일을 나타낸다는 것 의미 -->
            <div id="editorAttachmentModal" class="ReactModal__Content ReactModal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="attachment" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 520px; border-radius: 20px;">
                    <div class="EditorModalLayoutView_container EditorModalLayoutView_-has_no_min_height">
                        <div class="content EditorModalLayoutView_content">
                            <div class="WeverseModal">
                                <div class="editor-modal-common editor-modal-attachment attachment-modal">
                                    <div class="editor-modal-content">
                                        <strong class="modal_title">사진 올리기</strong>
                                        <div class="wrap_preview">
                                            <div class="preview_content" data-editor-alias="wevEditor" id="image_content">
                                                <div class="preview_item" id="preview_item_btn">
                                                    <label for="preview-add-image-input" class="add_more">
                                                        <span class="blind">add more file</span>
                                                        <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.5 6C17.6716 6 17 6.67157 17 7.5V17H7.5C6.67157 17 6 17.6716 6 18.5C6 19.3284 6.67157 20 7.5 20H17V29.5C17 30.3284 17.6716 31 18.5 31C19.3284 31 20 30.3284 20 29.5V20H29.5C30.3284 20 31 19.3284 31 18.5C31 17.6716 30.3284 17 29.5 17H20V7.5C20 6.67157 19.3284 6 18.5 6Z" fill="#8E8E8E"></path>
                                                        </svg>
                                                    </label>
                                                    <input class="blind" id="preview-add-image-input" type="file" multiple accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal_action">
                                            <button type="button" class="cancel_button" onclick="closeAddImageModal()">취소</button>
                                            <button type="button" class="confirm_button" onclick="confirmAddImageModal()">확인</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="EditorModalLayoutView_footer_area"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 동영상 썸네일을 보여주기 위한 동영상 올리기 화면 -->
    <!-- 2. 동영상 선택 후 확인을 누르면 동영상 프리뷰 화면이 뜨면서 어떤 영상을 추가했는지 확인 할 수 있음. jpg의 썸네일과 영상의 길이가 함께 뜬다. -->
    <div class="Modal">
        <div id="previewVideoModal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20005;">
        <!-- attachment 해당 요소가 첨부 파일을 나타낸다는 것 의미 -->
            <div id="editorAttachmentModal" class="ReactModal__Content ReactModal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="attachment" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 520px; border-radius: 20px;">
                    <div class="EditorModalLayoutView_container EditorModalLayoutView_-has_no_min_height">
                        <div class="content EditorModalLayoutView_content">
                            <div class="WeverseModal">
                                <div class="editor-modal-common editor-modal-attachment attachment-modal">
                                    <div class="editor-modal-content">
                                        <strong class="modal_title">동영상 올리기</strong>
                                        <div class="wrap_preview">
                                            <div class="preview_content" data-editor-alias="wevEditor" id="thumbnail_content">
                                                <div class="preview_item" id="thumbnail_item_btn">
                                                    <label for="preview-add-video-input" class="add_more">
                                                        <span class="blind">add more file</span>
                                                        <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.5 6C17.6716 6 17 6.67157 17 7.5V17H7.5C6.67157 17 6 17.6716 6 18.5C6 19.3284 6.67157 20 7.5 20H17V29.5C17 30.3284 17.6716 31 18.5 31C19.3284 31 20 30.3284 20 29.5V20H29.5C30.3284 20 31 19.3284 31 18.5C31 17.6716 30.3284 17 29.5 17H20V7.5C20 6.67157 19.3284 6 18.5 6Z" fill="#8E8E8E"></path>
                                                        </svg>
                                                    </label>
                                                    <input class="blind" id="preview-add-video-input" type="file" multiple accept="video/mp4, video/*">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal_action">
                                            <button type="button" class="cancel_button" onclick="closeAddVideoModal()">취소</button>
                                            <button type="button" class="confirm_button" onclick="confirmAddVideoModal()">확인</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="EditorModalLayoutView_footer_area"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= BASE_PATH ?>/front/js/feed/feed_page.js"></script>
</body>
</html>