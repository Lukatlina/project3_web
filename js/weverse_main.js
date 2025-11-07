let modal = document.getElementById("noticeModal");

console.log("오픈 팝업 시작");

var cookieCheck = getCookie("cookieID");

console.log("쿠키체크" + cookieCheck);

if (cookieCheck != "N") {
    modal.style.display = "flex";
}

function getCookie(name) {

    var cookie = document.cookie;
    // 쿠키 값이 들어있는지 여부 확인 후 있다면 split으로 나눈 후 확인
        if (document.cookie != "") {
            var cookie_array = cookie.split("; ");
            for ( var index in cookie_array) {
                var cookie_name = cookie_array[index].split("=");
                if (cookie_name[0] == "cookieID") {
                    return cookie_name[1]; } }
        }
    // 없다면 반환값이 없음
    return undefined;
}



function closeNoticeModal() {
    modal.style.display = "none";
}

function setCookie(name, value, expiredays) {
    var date = new Date();
    date.setDate(date.getDate() + expiredays);
    document.cookie = escape(name) + "=" + escape(value) + "; expires=" + date.toUTCString();
}

function closePopup() {
    if (document.getElementById("NoticeCheckbox").value) {
    
        setCookie("cookieID", "N", 1);
        modal.style.display = "none";
    }
}