function checkPassword() {
    let password = document.getElementById("password");
    let password_check_length = document.getElementById("password-rule-length");
    let password_check_english = document.getElementById("password-rule-english");
    let password_check_number = document.getElementById("password-rule-number");
    let password_check_sc = document.getElementById("password-rule-special");

    let check_password = document.getElementById("check-password");
    let password_check_text = document.getElementById("password-check-message");
    let pw_next_btn = document.getElementById("password-next-button");

    console.log(password.value);
    console.log(check_password.value);


    // 비밀번호 값이 비어있으면 글씨색을 만듦
    if (password.value != "") {
        pw_next_btn.disabled = true;
        console.log("value값이 비어있지않으면 + " + pw_next_btn.disabled);
        if (!passwordLengthCheck(password.value)) {
            password.style.borderColor = "#EF4444";
            password_check_length.style.color = "rgb(253, 91, 21)";

        } else {
            password.style.borderColor = "#9CA3AF";
            password_check_length.style.color = "rgb(0, 192, 181)";
        }

        if (!passwordEngCheck(password.value)) {
            password.style.borderColor = "#EF4444";
            password_check_english.style.color = "rgb(253, 91, 21)";
        } else {
            password.style.borderColor = "#9CA3AF";
            password_check_english.style.color = "rgb(0, 192, 181)";
        }

        if (!passwordNumCheck(password.value)) {
            password.style.borderColor = "#EF4444";
            password_check_number.style.color = "rgb(253, 91, 21)";
        } else {
            password.style.borderColor = "#9CA3AF";
            password_check_number.style.color = "rgb(0, 192, 181)";
        }

        if (!passwordScCheck(password.value)) {
            password.style.borderColor = "#EF4444";
            password_check_sc.style.color = "rgb(253, 91, 21)";

        } else {
            password.style.borderColor = "#9CA3AF";
            password_check_sc.style.color = "rgb(0, 192, 181)";
        }
        if (check_password.value != "") {
            if (check_password.value == password.value) {
                password_check_text.textContent = "비밀번호가 일치합니다.";
                password_check_text.style.color = "rgb(0, 192, 181)";
                password_check_text.style.visibility = "visible";
            } else {
                password_check_text.textContent = "비밀번호가 일치하지 않습니다. 다시 확인해주세요.";
                password_check_text.style.color = "rgb(253, 91, 21)";
                password_check_text.style.visibility = "visible";
            }
        } else {
            password_check_text.textContent = "";
        }

        if ((passwordLengthCheck(password.value) == true) && (passwordEngCheck(password.value) == true) &&
            (passwordNumCheck(password.value) == true) && (passwordScCheck(password.value) == true)) {
            if (check_password.value != "") {
                if (check_password.value == password.value) {
                    pw_next_btn.disabled = false;
                } else {
                    pw_next_btn.disabled = true;
                }
            }
        } else {
            pw_next_btn.disabled = true;
        }

    } else {
        password_check_length.style.color = "rgb(135, 142, 150)";
        password_check_english.style.color = "rgb(135, 142, 150)";
        password_check_number.style.color = "rgb(135, 142, 150)";
        password_check_sc.style.color = "rgb(135, 142, 150)";
        password_check_text.textContent = "";
        pw_next_btn.disabled = true;
        console.log("value값이 비어있으면 + " + pw_next_btn.disabled);
    }
    console.log(pw_next_btn.disabled);
}

function checkmodifyPassword() {
    let current_password = document.getElementById("current-password");
    let modify_password = document.getElementById("modify-password");
    
    let modify_password_check_length = document.getElementById("modify-password-rule-text-length");
    let modify_password_check_english = document.getElementById("modify-password-rule-text-english");
    let modify_password_check_number = document.getElementById("modify-password-rule-text-number");
    let modify_password_check_sc = document.getElementById("modify-password-rule-text-sc");

    let modify_check_password = document.getElementById("modify-check-password");
    let modify_password_check_text = document.getElementById("modify-password-check-message");
    let pw_change_btn = document.getElementById("password-change-complete");

    console.log(modify_password.value);
    console.log(modify_check_password.value);


    // 비밀번호 값이 비어있으면 글씨색을 만듦
    if (modify_password.value != "") {
        pw_change_btn.disabled = true;
        console.log("value값이 비어있지않으면 + " + pw_change_btn.disabled);
        if (!passwordLengthCheck(modify_password.value)) {
            modify_password.style.borderColor = "#EF4444";
            modify_password_check_length.style.color = "rgb(253, 91, 21)";

        } else {
            modify_password.style.borderColor = "#9CA3AF";
            modify_password_check_length.style.color = "rgb(0, 192, 181)";
        }

        if (!passwordEngCheck(modify_password.value)) {
            modify_password.style.borderColor = "#EF4444";
            modify_password_check_english.style.color = "rgb(253, 91, 21)";
        } else {
            modify_password.style.borderColor = "#9CA3AF";
            modify_password_check_english.style.color = "rgb(0, 192, 181)";
        }

        if (!passwordNumCheck(modify_password.value)) {
            modify_password.style.borderColor = "#EF4444";
            modify_password_check_number.style.color = "rgb(253, 91, 21)";
        } else {
            modify_password.style.borderColor = "#9CA3AF";
            modify_password_check_number.style.color = "rgb(0, 192, 181)";
        }

        if (!passwordScCheck(modify_password.value)) {
            modify_password.style.borderColor = "#EF4444";
            modify_password_check_sc.style.color = "rgb(253, 91, 21)";

        } else {
            modify_password.style.borderColor = "#9CA3AF";
            modify_password_check_sc.style.color = "rgb(0, 192, 181)";
        }
        if (modify_check_password.value != "") {
            if (modify_check_password.value == modify_password.value) {
                modify_password_check_text.textContent = "비밀번호가 일치합니다.";
                modify_password_check_text.style.color = "rgb(0, 192, 181)";
                modify_password_check_text.style.visibility = "visible";
            } else {
                modify_password_check_text.textContent = "비밀번호가 일치하지 않습니다. 다시 확인해주세요.";
                modify_password_check_text.style.color = "rgb(253, 91, 21)";
                modify_password_check_text.style.visibility = "visible";
            }
        } else {
            modify_password_check_text.textContent = "";
        }

        if ((passwordLengthCheck(modify_password.value) == true) && (passwordEngCheck(modify_password.value) == true) &&
            (passwordNumCheck(modify_password.value) == true) && (passwordScCheck(modify_password.value) == true) && current_password.value != "") {
            if (modify_check_password.value != "") {
                if (modify_check_password.value == modify_password.value) {
                    pw_change_btn.disabled = false;
                } else {
                    pw_change_btn.disabled = true;
                }
            } else {
                pw_change_btn.disabled = true;
            }
        } else {
            pw_change_btn.disabled = true;
        }

    } else {
        modify_password_check_length.style.color = "rgb(135, 142, 150)";
        modify_password_check_english.style.color = "rgb(135, 142, 150)";
        modify_password_check_number.style.color = "rgb(135, 142, 150)";
        modify_password_check_sc.style.color = "rgb(135, 142, 150)";
        modify_password_check_text.textContent = "";
        pw_change_btn.disabled = true;
        console.log("value값이 비어있으면 + " + pw_change_btn.disabled);
    }
    console.log(pw_change_btn.disabled);
}


// 형식에 맞는 경우에만 true 리턴
// 비밀번호의 길이를 8-32인지 검사하는 함수
function passwordLengthCheck(modify_password) {
    let regExp = /^.{8,32}$/;
    return regExp.test(modify_password);
}

// 영어 대소문자 포함여부 확인 함수
function passwordEngCheck(modify_password) {
    let regExp = /(?=.*?[a-zA-Z])/;
    return regExp.test(modify_password);
}

// 숫자 포함 검사 함수
function passwordNumCheck(modify_password) {
    let regExp = /(?=.*?[0-9])/;
    return regExp.test(modify_password);
}

// 특수문자 포함 여부 검사 함수
function passwordScCheck(modify_password) {
    let regExp = /(?=.*?[#?!@$%^&*-])/;
    return regExp.test(modify_password);
}

// 현재 비밀번호와 입력한 비밀번호가 일치하는지 확인하는 함수
