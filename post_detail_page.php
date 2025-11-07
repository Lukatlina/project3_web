<?php
    // header('Cache-Control: no cache'); //no cache
    // session_cache_limiter('private_no_expire'); // works
    include_once 'auth/check_auto_login.php';
    include_once 'config/config.php';
?>

<!DOCTYPE html>
<html class="scrollbar-custom use-webfont">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color"content="#fff">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weverse</title>
    <link rel="stylesheet" type="text/css" href="css/feed_style.css">
</head>
<body>
    <?php
        $boardNumber = (int)($_GET['board_number'] ?? 0);
        $userNumber = (int)($_SESSION['user_number'] ?? 0);
        
        // (추가) 유효성 검사: 게시글 번호가 없으면 중단
        if ($boardNumber === 0) {
            echo "잘못된 접근입니다.";
            exit();
        }

        // 3. (추가) HTML 렌더링에 필요한 변수들 초기화
        $boardRow = null;
        $comments = [];
        $processedContents = ""; // ★★★ 비디오/이미지가 처리될 변수
        $numberOfComments = 0;
        $nickname = "";
        $cheering = 0;
        $dateTime = new DateTime(); // 기본값으로 현재 시간

        try {
            // 4. (변경) 게시글 정보 mysqli 쿼리를 PDO로 변경
            $sql = "SELECT board.board_number, board.user_number, board.contents, 
                        board.contents_save_time, board.cheering, user.nickname
                    FROM board
                    LEFT JOIN user ON board.user_number = user.user_number
                    WHERE board.board_number = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$boardNumber]);
            $boardRow = $stmt->fetch(); // (변경) fetch()로 1개 행만 가져오기

            if (!$boardRow) {
                echo "게시글이 존재하지 않습니다.";
                exit();
            }
            
            // 5. (변경) HTML에서 사용할 변수들에 값 할당
            $contents = $boardRow['contents'];
            $cheering = $boardRow['cheering'];
            $nickname = $boardRow['nickname'];
            $dateTime = new DateTime($boardRow['contents_save_time']);
            
            // 6. (★핵심 변경★) 'include find_video.php' -> 'injectMediaPaths()' 함수 호출
            // Step 3에서 만든 함수를 호출하여 $contents 안의 미디어 태그를 완성시킵니다.
            // 이 결과($processedContents)를 HTML 렌더링에 사용할 것입니다.
            $processedContents = injectMediaPaths($pdo, $boardNumber, $contents);

            // --- 댓글 로직 시작 ---
            
            // 7. (변경) 전체 댓글 개수 쿼리 (PDO)
            $sqlCommentCount = "SELECT COUNT(*) FROM comments WHERE board_number = ? AND parent_number = 0";
            $stmtCommentCount = $pdo->prepare($sqlCommentCount);
            $stmtCommentCount->execute([$boardNumber]);
            $numberOfComments = (int)$stmtCommentCount->fetchColumn(); // (int)로 변환
            
            // 8. (★핵심 변경★) '댓글 20개 로드 쿼리' -> 'getCommentsForPost()' 함수 호출
            // Step 11-3(통합)에서 만든 공용 함수를 호출하여 초기 댓글 목록을 가져옵니다.
            $comments = getCommentsForPost(
                $pdo,
                $boardNumber,
                $userNumber,
                0, // $lastItemNumber (처음 로드이므로 0)
                20
            );
            
            $mainPostLikeStatus = getLikeStatus(
                $pdo, 
                $userNumber, 
                $boardNumber, 
                null
            );

        } catch (PDOException $e) {
            error_log("Failed to fetch post details or comments: " . $e->getMessage());
            echo "페이지를 불러오는 데 실패했습니다.";
            exit();
        }

    ?>
    <div class="root">
        <div class="Toastify" id="WEV2-TOAST-CONTAINER-ID"></div>
        <div class="App" style>
            <div class="GlobalLayoutView_layout_container" data-is-responsive-mode="false">
                <div class="GlobalLayoutView_header">
                    <header class="header">
                        <div class="HeaderView_content">
                            <div class="HeaderView_service">
                                <img src="image/weverse.png" width="136px" height="20px">
                            </div>
                            <div class="HeaderView_action">
                                <button class="user_data_btn" onclick="location.href='user/profile_page.php'">
                                    <img src="image/userdata_btn_img.png" width="38px" height="38px">
                                </button>
                            </div>
                        </div>
                    </header>
                </div>
                <div class="body">
                    <div class="CommunityNavigationLayoutView_navigation">
                        <nav class="CommunityHeaderNavigationView_community_header_navigation"
                        style="background-image: linear-gradient(90deg, #07D8E2 54.07%, #35e99d 99.24%);">
                            <a href="newjeansofficial/feed.php" class="CommunityHeaderNavigationView_link" aria-current="false">Feed</a>
                            <a href="newjeansofficial/artist.php" class="CommunityHeaderNavigationView_link" aria-current="false">Artist</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="Modal" id="PostModal" data-id="<?php echo $board_number;?>">
        <div id="feed_Modal" class="Modal__Overlay Modal__Overlay--after-open PostModalView_modal_overlay BaseModalView_post_768" style="z-index: 20003;">
            <div id="feedReadModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="Reading feed post" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 1100px; border-radius: 20px;">
                    <div class="PostModalView_container" data-appearance="DEFAULT">
                        <div class="PostModalView_content">
                            <div class="PostModalView_post_wrap">
                                <div class="PostModalView_post_header">
                                    <div class="PostHeaderView_header_wrap PostHeaderView_-header_type_post">
                                        <div class="PostHeaderView_group_wrap PostHeaderView_-profile_area">
                                            <a class="PostHeaderView_thumbnail_wrap">
                                                <div class="ProfileThumbnailView_thumbnail_area" style="width: 46px; height: 46px;">
                                                    <div class="ProfileThumbnailView_thumbnail_wrap ProfileThumbnailView_-has_border">
                                                        <div style="aspect-ratio: auto 46 / 46; content-visibility: auto; contain-intrinsic-size: 46px; width: 100%; height: 100%;">
                                                            <img class="ProfileThumbnailView_thumbnail" src="image/icon_empty_profile.png" width="46" height="46" alt>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="PostHeaderView_text_wrap">
                                            <a href="">
                                                <div class="PostHeaderView_nickname_wrap">
                                                    <strong class="PostHeaderView_nickname"><?php echo $nickname;?></strong>
                                                </div>
                                            </a>
                                            <div class="PostHeaderView_info_wrap">
                                                <span class="PostHeaderView_date">
                                                    <?php
                                                        if ($dateTime->format('Y')===date('Y')) {
                                                            echo $dateTime->format('m. d. H:i');
                                                        }else{
                                                            echo $dateTime->format('Y. m. d. H:i');
                                                    }
                                                    ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="PostModalView_post_body">
                                    <div class="styles_scrollStartPointer undefined"></div>
                                    <div class="WeverseViewer">
                                        <?php echo $contents;?>
                                        <div id="ve"></div>
                                    </div>
                                    <div>
                                        <div class="styles_scrollEndPointer undefined"></div>
                                    </div>
                                    <div class="styles_scrollButtonWrap undefined" data-visible="false">
                                        <button type="button" class="ScrollUpDownButtonView_scroll_down -color-light ScrollUpDownButtonView_-direction-up">
                                            <span class="blind">
                                                move scroll to
                                                up
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="PostModalView_comment_wrap">
                                <div class="CommentView_container">
                                    <div id="commentViewer" data-comment-type="comment" class="CommentViewerView_container -comment_viewer_post" style="padding-bottom: 0px;">
                                        <div class="CommentViewerView_title">
                                            <div class="CommentTitleView_container -comment_client_post">
                                                <strong><?php echo $number_of_comments ."개의 댓글";?></strong>
                                                <button type="button" class="CommentTitleView_refresh_button">
                                                    <span class="CommentTitleView_refresh_icon">
                                                        <span class="blind">refresh</span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- 댓글이 0개 일 때 -->
                                        <?php if ($number_of_comments === 0) : ?>
                                        <div class="commentList CommentViewerView_scrollable_area">
                                            <div class="CommentViewerView_no_comment">
                                                <div class="EmptyCommentView_chat_error">
                                                    <p class="EmptyCommentView_error_text">등록된 댓글이 없습니다</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- 댓글이 0개 이상일 때 -->
                                        <?php else : ?>
                                        <div class="commentList CommentViewerView_scrollable_area">
                                            <div>
                                                <div class="styles_scrollStartPointer"></div>
                                            </div>
                                            <div class="wrap_comment_list">
                                                <div class="list">
                                                    <div class="CommentListView_list_content -comment_depth_depth1 -comment_client_post">
                                                        <div>
                                                            
                                                                
                                                                <!-- 대댓글의 갯수가 0개일 때 -->
                                                                <?php if ($comment['numberOfReply'] === 0) : ?>
                                                                <div class="comment_item CommentView_comment_item" data-comment-id="<?php echo $comment['id']?>" data-comment-anchored="false" data-comment-alias="COMMENT" data-comment-depth="depth1" data-comment-client="post" data-comment-use-background="false">
                                                                    <div class="CommentView_comment_content -comment_client_post">
                                                                        <div class="PostHeaderView_header_wrap PostHeaderView_-header_type_post PostHeaderView_-comment_depth1">
                                                                            <div class="PostHeaderView_group_wrap PostHeaderView_-profile_area">
                                                                                <a class="PostHeaderView_thumbnail_wrap">
                                                                                    <div class="ProfileThumbnailView_thumbnail_area" style="width: 32px; height: 32px;">
                                                                                        <div class="ProfileThumbnailView_thumbnail_wrap ProfileThumbnailView_-has_border">
                                                                                            <div style="aspect-ratio: auto 32 / 32; content-visibility: auto; contain-intrinsic-size: 32px; width: 100%; height: 100%;">
                                                                                                <img class="ProfileThumbnailView_thumbnail" src="image/icon_empty_profile.png" width="32" height="32" alt>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <div class="PostHeaderView_text_wrap">
                                                                                    <a href="">
                                                                                        <div class="PostHeaderView_nickname_wrap">
                                                                                            <strong class="PostHeaderView_nickname"><?php echo $comment['write_user_nickname'];?></strong>
                                                                                        </div>
                                                                                    </a>
                                                                                    <div class="PostHeaderView_info_wrap">
                                                                                        <span class="PostHeaderView_date">
                                                                                            <?php
                                                                                                if ($comment['dateTime']->format('Y')===date('Y')) {
                                                                                                    echo $comment['dateTime']->format('m. d. H:i');
                                                                                                }else{
                                                                                                    echo $comment['dateTime']->format('Y. m. d. H:i');
                                                                                            }
                                                                                            ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="PostHeaderView_group_wrap PostHeaderView_-button_area">
                                                                                <div type="button" class="TranslationButtonView_translation_button" aria-pressed="false"></div>
                                                                                <div class="PostHeaderView_button_item PostHeaderView_-menu">
                                                                                    <div>
                                                                                        <button type="button" id="MoreButtonView_button_menu<?php echo $comment['id']; ?>" class="MoreButtonView_button_menu MoreButtonView_-comment MoreButtonView_-post" data-id="<?php echo $comment['id']; ?>" onclick="clickCommentListBox(<?php echo $comment['id']; ?>)">
                                                                                            <span class="blind">Show More Content</span>
                                                                                        </button>
                                                                                        <?php
                                                                                        if ($user_number === $comment['write_user_number']) : ?>
                                                                                            <ul id="CommentDropdownOptionListView<?php echo $comment['id']; ?>" class="DropdownOptionListView_option_list DropdownOptionListView_dropdown-action" role="listbox" data-use-placement="true" data-placement="top" >
                                                                                                <li class="DropdownOptionListView_option_item" role="presentation">
                                                                                                    <button class="ContentMetaActionLayerView_button_item ContentMetaActionLayerView_-delete" onclick="openDeleteCommentModal(<?php echo $comment['id']?>)">
                                                                                                        삭제하기
                                                                                                    </button>
                                                                                                </li>   
                                                                                            </ul>
                                                                                        <?php else : ?>
                                                                                            <ul id="CommentDropdownOptionListView<?php echo $comment['id']; ?>" class="DropdownOptionListView_option_list DropdownOptionListView_dropdown-action" role="listbox" data-use-placement="true" data-placement="top">
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
                                                                        <div class="CommentView_comment_text"><?php echo $comment['comments_text']; ?></div>
                                                                        <!-- 댓글에 달린 좋아요 버튼 -->
                                                                        <div class="CommentView_comment_actions">
                                                                            <div class="CommentView_comment_action_item">
                                                                                <button id="EmotionButtonView_button_emotion<?php echo $comment['id']; ?>" type="button" class="EmotionButtonView_button_emotion EmotionButtonView_-comment -post" aria-pressed="false" onclick="updateLike(<?php echo $board_number; ?>,<?php echo $comment['id']; ?>)">
                                                                                    <?php
                                                                                    $comment_number = $comment['id'];

                                                                                    if ($comment['likesRowCount'] === 1) : ?>
                                                                                        <svg id="comment_like_btn<?php echo $comment['id']; ?>" class="add_comment_like liked" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg"></svg>
                                                                                        <span class="blind">cheering</span>
                                                                                        <?php echo $comment['comments_cheering']; ?>
                                                                                    <?php else : ?>
                                                                                        <svg id="comment_like_btn<?php echo $comment['id']; ?>" class="add_comment_like" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg"></svg>
                                                                                        <span class="blind">cheering</span>
                                                                                        <?php 
                                                                                            if ($comment['comments_cheering'] == 0) {
                                                                                                NULL;
                                                                                            }else {
                                                                                                echo $comment['comments_cheering'];
                                                                                            }
                                                                                        ?>
                                                                                    <?php endif ?>
                                                                                </button>
                                                                            </div>
                                                                            <div class="CommentView_comment_action_item">
                                                                                <button type="button" class="CommentButtonView_button_comment CommentButtonView_-comment -post" onclick="clickWriteReply(<?php echo $comment['id']; ?>)">
                                                                                    <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-id="<?php echo $comment['id']; ?>">
                                                                                        <path d="M16.9987 8.45003C16.9987 4.72476 13.9753 1.70135 10.25 1.70135C6.52475 1.70135 3.50134 4.72476 3.50134 8.45003C3.50134 11.3038 5.27528 13.741 7.77422 14.7282C7.77422 14.7282 7.80507 14.7436 7.8282 14.7514C8.11358 14.8593 8.40666 14.9519 8.70746 15.0213C11.1524 15.6615 14.0524 15.5458 15.4484 15.3144C15.8263 15.245 15.9729 14.9133 15.7801 14.574C15.5718 14.2038 15.2787 13.7564 15.2247 13.3553C15.0474 12.0056 16.9987 11.1186 16.991 8.52715C16.991 8.50402 16.991 8.48088 16.991 8.45774L16.9987 8.45003Z" stroke="#8E8E8E" stroke-width="1.2" stroke-miterlimit="10"></path>
                                                                                    </svg>
                                                                                    <span class="blind">Leave a comment</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <!-- 댓댓글 작성칸 -->
                                                                        <div class="ReplyViewerView_input_wrap" id="ReplyViewerView_input_wrap<?php echo $comment['id']; ?>" data-id="<?php echo $comment['id']; ?>" oninput="saveButtonCheck(<?php echo $comment['id']; ?>)">
                                                                            <div class="container -comment_client_post">
                                                                                <div class="CommentInputView_form">
                                                                                    <div class="CommentInputView_textarea_wrap">
                                                                                        <textarea id="ReplyInputView_textarea<?php echo $comment['id']; ?>" data-id="<?php echo $comment['id']; ?>" class="CommentInputView_textarea" spellcheck="false" placeholder="댓글을 입력하세요." style="height: 22px !important;"></textarea>
                                                                                    </div>
                                                                                    <button type="button" id="ReplyInputView_send_button<?php echo $comment['id']; ?>"class="CommentInputView_send_button" onclick="saveReplyValue(<?php echo $board_number; ?>,<?php echo $comment['id']; ?>)" disabled>
                                                                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M18 26C17.4477 26 17 25.5523 17 25L17 12C17 11.4477 17.4477 11 18 11C18.5523 11 19 11.4477 19 12L19 25C19 25.5523 18.5523 26 18 26Z" fill="currentColor"></path>
                                                                                            <path d="M12 17L18 11L24 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                                                        </svg>
                                                                                        <span class="blind">send</span>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- 대댓글이 있는 경우 -->
                                                                <?php else : ?>
                                                                <div class="comment_item CommentView_comment_item CommentView_-with_reply" data-comment-id="<?php echo $comment['id']?>" data-comment-anchored="false" data-comment-alias="COMMENT" data-comment-depth="depth1" data-comment-client="post" data-comment-use-background="false">
                                                                    <div class="CommentView_comment_content -comment_client_post">
                                                                        <div class="PostHeaderView_header_wrap PostHeaderView_-header_type_post PostHeaderView_-comment_depth1">
                                                                            <div class="PostHeaderView_group_wrap PostHeaderView_-profile_area">
                                                                                <a class="PostHeaderView_thumbnail_wrap">
                                                                                    <div class="ProfileThumbnailView_thumbnail_area" style="width: 32px; height: 32px;">
                                                                                        <div class="ProfileThumbnailView_thumbnail_wrap ProfileThumbnailView_-has_border">
                                                                                            <div style="aspect-ratio: auto 32 / 32; content-visibility: auto; contain-intrinsic-size: 32px; width: 100%; height: 100%;">
                                                                                                <img class="ProfileThumbnailView_thumbnail" src="image/icon_empty_profile.png" width="32" height="32" alt>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <div class="PostHeaderView_text_wrap">
                                                                                    <a href="">
                                                                                        <div class="PostHeaderView_nickname_wrap">
                                                                                            <strong class="PostHeaderView_nickname"><?php echo $comment['write_user_nickname'];?></strong>
                                                                                        </div>
                                                                                    </a>
                                                                                    <div class="PostHeaderView_info_wrap">
                                                                                        <span class="PostHeaderView_date">
                                                                                            <?php
                                                                                                if ($comment['dateTime']->format('Y')===date('Y')) {
                                                                                                    echo $comment['dateTime']->format('m. d. H:i');
                                                                                                }else{
                                                                                                    echo $comment['dateTime']->format('Y. m. d. H:i');
                                                                                            }
                                                                                            ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="PostHeaderView_group_wrap PostHeaderView_-button_area">
                                                                                <div type="button" class="TranslationButtonView_translation_button" aria-pressed="false"></div>
                                                                                <div class="PostHeaderView_button_item PostHeaderView_-menu">
                                                                                <div>
                                                                                        <button type="button" id="MoreButtonView_button_menu<?php echo $comment['id']; ?>" class="MoreButtonView_button_menu MoreButtonView_-comment MoreButtonView_-post" data-id="<?php echo $comment['id']; ?>" onclick="clickCommentListBox(<?php echo $comment['id']; ?>)">
                                                                                            <span class="blind">Show More Content</span>
                                                                                        </button>
                                                                                        <?php
                                                                                        if ($user_number === $comment['write_user_number']) : ?>
                                                                                            <ul id="CommentDropdownOptionListView<?php echo $comment['id']; ?>" class="DropdownOptionListView_option_list DropdownOptionListView_dropdown-action" role="listbox" data-use-placement="true" data-placement="top" >
                                                                                                <li class="DropdownOptionListView_option_item" role="presentation">
                                                                                                    <button class="ContentMetaActionLayerView_button_item ContentMetaActionLayerView_-delete" onclick="openDeleteCommentModal(<?php echo $comment['id']?>)">
                                                                                                        삭제하기
                                                                                                    </button>
                                                                                                </li>   
                                                                                            </ul>
                                                                                        <?php else : ?>
                                                                                            <ul id="CommentDropdownOptionListView<?php echo $comment['id']; ?>" class="DropdownOptionListView_option_list DropdownOptionListView_dropdown-action" role="listbox" data-use-placement="true" data-placement="top">
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
                                                                        <div class="CommentView_comment_text"><?php echo $comment['comments_text']; ?></div>
                                                                        <div class="CommentView_comment_actions">
                                                                            <div class="CommentView_comment_action_item">
                                                                                <button id="EmotionButtonView_button_emotion<?php echo $comment['id']; ?>" type="button" class="EmotionButtonView_button_emotion EmotionButtonView_-comment -post" aria-pressed="false" onclick="updateLike(<?php echo $board_number; ?>,<?php echo $comment['id']; ?>)">
                                                                                    <?php

                                                                                    $comment_number = $comment['id'];

                                                                        

                                                                                    if ($comment['likesRowCount'] === 1) : ?>
                                                                                        <svg id="comment_like_btn<?php echo $comment['id']; ?>" class="add_comment_like liked" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            
                                                                                        </svg>
                                                                                        <span class="blind">cheering</span>
                                                                                        <?php echo $comment['comments_cheering']; ?>
                                                                                    <?php else : ?>
                                                                                        <svg id="comment_like_btn<?php echo $comment['id']; ?>" class="add_comment_like" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            
                                                                                        </svg>
                                                                                        <span class="blind">cheering</span>
                                                                                        <?php 
                                                                                            if ($comment['comments_cheering'] == 0) {
                                                                                                NULL;
                                                                                            }else {
                                                                                                echo $comment['comments_cheering'];
                                                                                            }
                                                                                        ?>
                                                                                    <?php endif ?>
                                                                                </button>
                                                                            </div>
                                                                            <div class="CommentView_comment_action_item">
                                                                                <button type="button" class="CommentButtonView_button_comment CommentButtonView_-comment -post" onclick="clickWriteReply(<?php echo $comment['id']; ?>)">
                                                                                    <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-id="<?php echo $comment['id']; ?>">
                                                                                        <path d="M16.9987 8.45003C16.9987 4.72476 13.9753 1.70135 10.25 1.70135C6.52475 1.70135 3.50134 4.72476 3.50134 8.45003C3.50134 11.3038 5.27528 13.741 7.77422 14.7282C7.77422 14.7282 7.80507 14.7436 7.8282 14.7514C8.11358 14.8593 8.40666 14.9519 8.70746 15.0213C11.1524 15.6615 14.0524 15.5458 15.4484 15.3144C15.8263 15.245 15.9729 14.9133 15.7801 14.574C15.5718 14.2038 15.2787 13.7564 15.2247 13.3553C15.0474 12.0056 16.9987 11.1186 16.991 8.52715C16.991 8.50402 16.991 8.48088 16.991 8.45774L16.9987 8.45003Z" stroke="#8E8E8E" stroke-width="1.2" stroke-miterlimit="10"></path>
                                                                                    </svg>
                                                                                    <span class="blind">Leave a comment</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ReplyViewerView_input_wrap" id="ReplyViewerView_input_wrap<?php echo $comment['id']; ?>" data-id="<?php echo $comment['id']; ?>" oninput="saveButtonCheck(<?php echo $comment['id']; ?>)">
                                                                            <div class="container -comment_client_post">
                                                                                <div class="CommentInputView_form">
                                                                                    <div class="CommentInputView_textarea_wrap">
                                                                                        <textarea id="ReplyInputView_textarea<?php echo $comment['id']; ?>" data-id="<?php echo $comment['id']; ?>" class="CommentInputView_textarea" spellcheck="false" placeholder="댓글을 입력하세요." style="height: 22px !important;"></textarea>
                                                                                    </div>
                                                                                    <button type="button" id="ReplyInputView_send_button<?php echo $comment['id']; ?>"class="CommentInputView_send_button" onclick="saveReplyValue(<?php echo $board_number; ?>,<?php echo $comment['id']; ?>)" disabled>
                                                                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M18 26C17.4477 26 17 25.5523 17 25L17 12C17 11.4477 17.4477 11 18 11C18.5523 11 19 11.4477 19 12L19 25C19 25.5523 18.5523 26 18 26Z" fill="currentColor"></path>
                                                                                            <path d="M12 17L18 11L24 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                                                        </svg>
                                                                                        <span class="blind">send</span>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                                    <div class="CommentView_more_recent_comment_wrap" id="CommentView_more_recent_comment_wrap<?php echo $comment['id']; ?>" data-id="<?php echo $comment['id']; ?>" >
                                                                        <a href="#" class="MoreRecentCommentView_link_more -post" onclick="loadReply(<?php echo $comment['numberOfReply'] ?>, <?php echo $board_number ?>, <?php echo $comment['id']; ?>)">
                                                                            답글 <?php echo $comment['numberOfReply'] ?>개
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php endif ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="styles_scrollButtonWrap undefined"></div>
                                        </div>
                                        <?php endif ?>
                                        <div class="CommentViewerView_input_area" data-comment-emphasize="false" data-has-limit-description="false">
                                            <div class="CommentViewerView_input_wrap">
                                                <div class="container -comment_client_post">
                                                    <div class="CommentInputView_form">
                                                        <div class="CommentInputView_textarea_wrap">
                                                            <textarea id="CommentInputView_textarea" class="CommentInputView_textarea" spellcheck="false" placeholder="댓글을 입력하세요." style="height: 22px !important;"></textarea>
                                                        </div>
                                                        <button type="button" id="CommentInputView_send_button"class="CommentInputView_send_button" onclick="saveCommentValue(<?php echo $board_number;?>)" disabled>
                                                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M18 26C17.4477 26 17 25.5523 17 25L17 12C17 11.4477 17.4477 11 18 11C18.5523 11 19 11.4477 19 12L19 25C19 25.5523 18.5523 26 18 26Z" fill="currentColor"></path>
                                                                <path d="M12 17L18 11L24 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                            </svg>
                                                            <span class="blind">send</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="CommentAsideDrawerView_drawer CommentAsideDrawerView_-post">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="PostModalView_post_action">
                            <button id="EmotionButtonView_button_emotion" type="button" class="EmotionButtonView_button_emotion" aria-pressed="false" onclick="updateLike(<?php echo $board_number ?>)">
                            <?php

                            $comment_number = '';
                            
                          
                            
                            if ($mainPostLikeStatus === 1) : ?>
                                <svg id="like_btn<?php echo $board_number; ?>" class="add_like liked" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"></svg>
                                <span class="blind">cheering</span>
                                <?php echo $cheering; ?>
                            <?php else : ?>
                                <svg id="like_btn<?php echo $board_number; ?>" class="add_like" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"></svg>
                                    <span class="blind">cheering</span>
                                    <?php 
                                    if ($cheering == 0) {
                                        NULL;
                                    }else {
                                        echo $cheering;
                                    }
                                    ?>
                            <?php endif ?>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="BaseModalView_close_button" onclick="location.href='feed_page.php';">
                    <span class="blind">close popup</span>
                </button>
            </div>
        </div>
    </div>
    <div class="Modal">
        <div id="deleteCommentModal" class="Modal__Overlay Modal__Overlay--after-open BaseModalView_modal_overlay" style="z-index: 20003;">
            <div id="deleteCommentPopupModal" class="Modal__Content Modal__Content--after-open BaseModalView_modal" tabindex="-1" role="dialog" aria-label="modal" aria-modal="true">
                <div class="BaseModalViewContent BaseModalView_content" style="width: 428px; max-width: 428px; border-radius: 14px;">
                    <div class="CommonModalView_modal_inner">
                        <p class="CommonModalView_description">
                            이 댓글을 삭제하시겠습니까?
                        </p>
                        <div class="ModalButtonView_button_wrap">
                            <button aria-label="cancel modal" type="button" class="ModalButtonView_button ModalButtonView_-cancel" onclick="closeDeleteCommentModal()">취소</button>
                            <button aria-label="confirm modal" type="button" class="ModalButtonView_button ModalButtonView_-confirm" onclick="completeDeletedComment()">확인</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/post_detail_page.js"></script>
</body>
</html>