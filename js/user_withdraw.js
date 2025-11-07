function compareWithdrawText() {
    let compare_text = document.getElementById("withdraw_confirmtext");
    let button = document.getElementById("form_draw_btn");

    if(compare_text.value !="Weverse 탈퇴"){
      button.disabled = true;
    }else{
      button.disabled = false;
    }
    console.log(button.disabled);
}

function writeWithdrawText() {
    var form = document.getElementById("form_withdraw");
      var formData = new FormData(form);
  
      for (var pair of formData.entries()) {
        console.log(pair[0] + ": " + pair[1]);
      }
    
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "write_withdraw_time.php", true);
    
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            console.log("POST 요청 성공");
            var response = xhr.responseText;
            console.log("response: " + response);
            // 응답 결과에 따라 처리
            if (response === "1") {
              form.action = "weverse_main.php";
              console.log("response 성공");
            } else {
              console.log("response 오류");
            }
          } else {
            console.log("POST 요청 실패");
          }
          form.submit();
        }
      };
      xhr.send(formData);
    }