function openNicknameDialog() {
    
    var dialog = document.getElementById("dialog-nickname");
    dialog.style.display = "flex";
}

function openNameDialog() {
    
    var dialog = document.getElementById("dialog-name");
    dialog.style.display = "flex";
  }

  function checkmodifyName() {// JS 함수 이름을 사용하는 태그중 속성 ID 와 함수명이 겹치게 만들면 에러 발생

    let last_name = document.getElementById("lastname");
    let first_name = document.getElementById("firstname");

    let last_name_check_text = document.getElementById("modify-lastname-check");
    let first_name_check_text = document.getElementById("modify-firstname-check");
    let btn = document.getElementById("name-change-complete");
  
    console.log("value값 + " + btn.disabled);

if(last_name.value != "") {
  btn.disabled = true;

  if(!isName(last_name.value)) {
    last_name_check_text.textContent = "성 입력시 50자 이내로 입력해주세요.";
    last_name_check_text.style.color = "rgb(253, 91, 21)";
    last_name_check_text.style.visibility = "visible";
  }else{
    last_name_check_text.textContent = "";
  } 
} else {
  last_name_check_text.textContent = "필수입니다.";
  last_name_check_text.style.color = "rgb(253, 91, 21)";
  last_name_check_text.style.visibility = "visible";
  btn.disabled = true;
}

if(first_name.value != "") {
  btn.disabled = true;

  if(!isName(first_name.value)) {
    first_name_check_text.textContent = "성 입력시 50자 이내로 입력해주세요.";
    first_name_check_text.style.color = "rgb(253, 91, 21)";
    first_name_check_text.style.visibility = "visible";
  }else{
    first_name_check_text.textContent = "";
  } 
} else {
  first_name_check_text.textContent = "필수입니다.";
  first_name_check_text.style.color = "rgb(253, 91, 21)";
  first_name_check_text.style.visibility = "visible";
  btn.disabled = true;
}


  if(isName(last_name.value) && isName(first_name.value)) {
    btn.disabled = false;
    console.log("value값이 조건에 맞으면 + " + btn.disabled);
  }else{
    btn.disabled = true;
    console.log("value값이 조건에 맞지 않으면 + " + btn.disabled);
  }
    console.log("value값 + " + btn.disabled);
      
}
        

function isName(asValue) {
  
    // 이메일 형식에 맞게 입력했는지 체크
  let regExp = /^[ㄱ-ㅎ가-힣a-zA-Z0-9#?!@$%^&*-]{1,50}$/;

    // 형식에 맞는 경우에만 true 리턴	
  return regExp.test(asValue);

}

  function openPasswordDialog() {
    
    var dialog = document.getElementById("dialog-password");
    dialog.style.display = "flex";
  
  }

  function closeNicknameDialog() {
    
    var dialog = document.getElementById("dialog-nickname");
    dialog.style.display = "none";
   
  }

  function closeNameDialog() {
    
    var dialog = document.getElementById("dialog-name");
    dialog.style.display = "none";
   
  }

  function closePasswordDialog() {
    
    var dialog = document.getElementById("dialog-password");
    dialog.style.display = "none";
   
  }

function saveNicknameDialog() {
console.log(new FormData(document.getElementById("form-nickname")));
console.log(document.getElementById("dialog-nickname"));
  if (document.getElementById("form-nickname") != "") {
    var dialog = document.getElementById("dialog-nickname");
    var form = document.getElementById("form-nickname");
  }else if(document.getElementById("form-name")){
    var dialog = document.getElementById("dialog-name");
    var form = document.getElementById("form-name");
  }else{
    var dialog = document.getElementById("dialog-password");
    var form = document.getElementById("form-password");
  }


  form.addEventListener("submit", function(event) {
    event.preventDefault(); // 폼의 기본 동작인 페이지 새로고침을 막음
  
    var formData = new FormData(form);
    for (var pair of formData.entries()) {
      console.log(pair[0] + ": " + pair[1]);
  }
  
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "profile_update_process.php", true);
  
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        console.log("리퀘스트 성공");
        if (xhr.status === 200) {
          console.log("POST 요청 성공");
          var response = xhr.responseText;
          console.log("response: " + response);
          // 응답 결과에 따라 처리
          if (response === "1") {
            location.reload();
          }  else {
            console.log("response 오류");
          }
        } else {
          console.log("POST 요청 실패");
        }
        dialog.style.display = "none";
      }
    };
    xhr.send(formData);
    console.log("xhr.send");
  });
}

//
function saveNameDialog() {
  var dialog = document.getElementById("dialog-name");
  var form = document.getElementById("form-name");
  
  
    form.addEventListener("submit", function(event) {
      event.preventDefault(); // 폼의 기본 동작인 페이지 새로고침을 막음
    
      var formData = new FormData(form);
      for (var pair of formData.entries()) {
        console.log(pair[0] + ": " + pair[1]);
    }
    
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "profile_update_process.php", true);
    
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          console.log("리퀘스트 성공");
          if (xhr.status === 200) {
            console.log("POST 요청 성공");
            var response = xhr.responseText;
            console.log("response: " + response);
            // 응답 결과에 따라 처리
            if (response === "1") {
              location.reload();
            }  else {
              console.log("response 오류");
            }
          } else {
            console.log("POST 요청 실패");
          }
          dialog.style.display = "none";
        }
      };
      xhr.send(formData);
      console.log("xhr.send");
    });
  }

  function savePasswordDialog(formData) {
  console.log("savePasswordDialog 함수 시작")
    // var dialog = document.getElementById("dialog-password");
    // var form = document.getElementById("form-password");

    //     var formData = new FormData(form);
    //     for (var pair of formData.entries()) {
    //       console.log(pair[0] + ": " + pair[1]);
      
      
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "profile_update_process.php", true);
      
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        console.log("리퀘스트 성공");
        if (xhr.status === 200) {
          console.log("POST 요청 성공");
          var response = xhr.responseText.trim();
          console.log("response: " + response);
          // 응답 결과에 따라 처리
          if (response === "1") {
            // 패스워드는 띄울 필요 없음
            location.reload();
          }  else {
            console.log("response 오류");
          }
        } else {
          console.log("POST 요청 실패");
        }
        // dialog.style.display = "none";
      }
    };
    xhr.send(formData);
    console.log("xhr.send");
      // };
      console.log("savePasswordDialog 함수 끝")
  }

    function checkCurrentPassword() {

      var form = document.getElementById("form-password");
      var current_password_check_text = document.getElementById("current-password-check-message");
      var dialog = document.getElementById("dialog-password");
      
      
        form.addEventListener("submit", function(event) {
          event.preventDefault(); // 폼의 기본 동작인 페이지 새로고침을 막음
        
          var formData = new FormData(form);
          for (var pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }
        
          var xhr = new XMLHttpRequest();
          xhr.open("POST", "password_check_process.php", true);
        
          xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
              console.log("리퀘스트 성공");
              if (xhr.status === 200) {
                console.log("POST 요청 성공");
                var response = xhr.responseText;
                console.log("response: " + response);
                // 응답 결과에 따라 처리
                if (response === "1") {
                  console.log("response 1 if문 실행");
                  // 유저가 입력한 비밀번호가 유저의 비밀번호와 일치할 경우
                  current_password_check_text.style.visibility = "hidden";
                  savePasswordDialog(formData);
                  dialog.style.display = "none";
                } else {
                  current_password_check_text.style.visibility = "visible";
                  console.log("response 비밀번호 일치오류");
                }
              } else {
                console.log("POST 요청 실패");
              }
            
            }
          };
          xhr.send(formData);
          console.log("xhr.send");
        });
      }

function logoutUser() {

fetch("../auth/logout_process.php", {
  method: "POST",
})
  .then(function(response) {
    // 응답 처리
    console.log(response);
    if (response.status === 200) {
      window.location.href = "../weverse_main.php";
      alert("로그아웃되었습니다.");
    }else{
      alert("로그아웃 실패");
    }

  })
  .catch(function(error) {
    // 오류 처리
    console.log("error");
  });
}