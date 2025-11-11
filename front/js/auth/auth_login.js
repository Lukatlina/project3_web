// 비밀번호 입력 인풋창 확인하는 함수.
// 가입할 때 조건이 있었던 것과 달리 비어있지만 않으면 되는 것 같다.
function passwordCheck() {
  let password = document.getElementById("login-password");
  let password_check = document.getElementById("login-password-check-message");
  let button = document.getElementById("login-button");


  if(password.value == ""){
    password.style.borderColor = "#EF4444";
    password_check.style.visibility = "visible";
    button.disabled = true;
  }else{
    password.style.borderColor = "#9CA3AF";
    password_check.style.visibility = "hidden";
    button.disabled = false;
  }
  console.log(button.disabled);
}



function loginData() {
  var form = document.getElementById("login-form");
  // 자동 로그인 체크 박스를 확인하기 위해 변수 할당
  let autologin = document.getElementById("auto-login-checkbox");

  form.addEventListener("submit", function(event) {
    event.preventDefault(); // 폼의 기본 동작인 페이지 새로고침을 막음
  
    var formData = new FormData(form);
    formData.append("autologin", autologin.checked);

    // for (var pair of formData.entries()) {
    //   console.log(pair[0] + ": " + pair[1]);
    // }
  
    var xhr = new XMLHttpRequest();
    xhr.open("POST", BASE_PATH + "/back/feature/auth/login_process.php", true);
  
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          console.log("POST 요청 성공");
          var response = xhr.responseText;
          console.log("response: " + response);
          // 응답 결과에 따라 처리
          if (response === "1") {
            form.action = BASE_PATH + "/front/index.php";
            form.submit();
          } else if (response === "0") {
            // 팝업 띄우기
            alert("로그인에 실패했습니다.");
          } else {
            console.log("response 오류");
          }
        } else {
          console.log("POST 요청 실패");
        }
      }
    };
    xhr.send(formData);
  });
}