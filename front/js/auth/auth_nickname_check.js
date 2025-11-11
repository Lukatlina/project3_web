function checkNickname() {// JS 함수 이름을 사용하는 태그중 속성 ID 와 함수명이 겹치게 만들면 에러 발생

    let nickname = document.getElementById("nickname");
    let nickname_check = document.getElementById("nickname-check-message");
    let btn = document.getElementById("continue-join-button");

    console.log(isNickname(nickname.value));
    if(!isNickname(nickname.value)){
        nickname.style.borderColor = "#EF4444";
        nickname_check.style.visibility = "visible";
        btn.disabled = true;
    }else{
        nickname.style.borderColor = "#9CA3AF";
        nickname_check.style.visibility = "hidden";
        btn.disabled = false;
    }
}

function checkmodifyNickname() {// JS 함수 이름을 사용하는 태그중 속성 ID 와 함수명이 겹치게 만들면 에러 발생

  let nickname = document.getElementById("modify-nickname");
  let nicknameCheck = document.getElementById("modify-nickname-check");
  let btn = document.getElementById("nickname-change-complete");

  console.log(isNickname(nickname.value));
  if(!isNickname(nickname.value)){
      nickname.style.borderColor = "#EF4444";
      nicknameCheck.style.visibility = "visible";
      btn.disabled = true;
  }else{
      nickname.style.borderColor = "#9CA3AF";
      nicknameCheck.style.visibility = "hidden";
      btn.disabled = false;
  }
}

function isNickname(asValue) {
  
    // 이메일 형식에 맞게 입력했는지 체크
  let regExp = /^[ㄱ-ㅎ가-힣a-zA-Z0-9#?!@$%^&*-]{1,32}$/;

    // 형식에 맞는 경우에만 true 리턴	
  return regExp.test(asValue);

}

