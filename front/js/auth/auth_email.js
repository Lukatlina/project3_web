function printEmail() {
    let email = document.getElementById("email");
    let email_check = document.getElementById("email-check-message");
    let button = document.getElementById("continue-login-button");

    console.log(email.value);

    console.log(isEmail(email.value));
    if(!isEmail(email.value)){
      email.style.borderColor = "#EF4444";
      email_check.textContent = "유효한 이메일을 입력해주세요.";
      email_check.style.visibility = "visible";
      button.disabled = true;
    }else{
      email.style.borderColor = "#9CA3AF";
      email_check.style.visibility = "hidden";
      button.disabled = false;
    }
    console.log(button.disabled);
}

function isEmail(asValue) {
  
    // 이메일 형식에 맞게 입력했는지 체크
  let regExp = /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;

    // 형식에 맞는 경우에만 true 리턴	
  return regExp.test(asValue);

}

function loadData() {
  console.log("loadData() 시작");
    var form = document.getElementById("email-check-form");
    let email_check = document.getElementById("email-check-message");

    form.addEventListener("submit", function(event) {
      console.log("submit 시작");
      event.preventDefault(); // 폼의 기본 동작인 페이지 새로고침을 막음
    
      var formData = new FormData(form);
      
      for (var pair of formData.entries()) {
        console.log(pair[0] + ": " + pair[1]);
    }
    
      var xhr = new XMLHttpRequest();
      xhr.open("POST", BASE_PATH + "/back/feature/auth/email_check_process.php", true);
    
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            console.log("POST 요청 성공");
            var response = xhr.responseText.trim();
            console.log("response: " + response);
            // 응답 결과에 따라 처리
            if (response === "1") {
              form.action = BASE_PATH + "/front/view/auth/login_page.php";
            } else if (response === "0") {
              form.action = BASE_PATH + "/front/view/auth/register_page.php";
            } else if (response === "2") {
              email_check.textContent = "탈퇴한지 90일이 지나지 않은 이메일입니다.";
              email_check.style.color = "rgb(253, 91, 21)";
              email_check.style.visibility = "visible";
              // return을 사용하게 되면 submit이 실행되지 않고 함수 호출 지점으로 돌아가게 됨.
              // 즉, 버튼을 누르기 전으로 돌아가게 됨.
              return;
            } else {
              console.log("response 오류");
              return;
            }
          } else {
            console.log("POST 요청 실패");
            return;
          }
          form.submit();
        }
      };
      xhr.send(formData);
    });
    console.log("loadData() 끝");
  }