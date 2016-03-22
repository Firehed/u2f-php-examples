// Actual site logic that you're probably interested in

function u2fRegister(ajaxResponse) {
    setFieldText('reg_request_to_sign',
                  JSON.stringify(ajaxResponse.request) + "\n" + JSON.stringify(ajaxResponse.signatures));
    showPress();

    // The u2f api takes an array of register requests so you can use multiple
    // protocols; the backend only supports one version and as such only
    // returns one request, so it's wrapped in an array here
    u2f.register([ajaxResponse.request], ajaxResponse.signatures, u2fPostRegisterData);
}

function u2fPostRegisterData(sig) {
    hidePress();
    setFieldText('reg_signature', JSON.stringify(sig));
    if (sig.errorCode) { showAuthError(sig.errorCode); return; }

    // Send data from U2F token to server over AJAX
    ajaxPost('/complete_registration.php',
             {"signature_str": JSON.stringify(sig)},
              displayResponse,
              displayResponse);
}

function u2fSign(ajaxResponse) {
    setFieldText('auth_request_to_sign', JSON.stringify(ajaxResponse));
    showPress();

    u2f.sign(ajaxResponse, u2fPostSignData);
}

function u2fPostSignData(sig) {
    hidePress();
    setFieldText('auth_signature', JSON.stringify(sig));
    if (sig.errorCode) { showAuthError(sig.errorCode); return; }

    // Do auth POST
    ajaxPost('/complete_auth.php',
             {"signature_str": JSON.stringify(sig)},
             displayResponse,
             displayResponse);
}

// Basic AJAX-on-button-press hooks

document.getElementById('register').addEventListener("submit", function(e) {
    e.preventDefault();
    var username = document.getElementById("reg_username").value;
    var password = document.getElementById("reg_password").value;
    ajaxPost('/register_user.php',
             {"username":username, "password":password},
             displayResponse,
             displayResponse);
});

document.getElementById('login').addEventListener("submit", function(e) {
    e.preventDefault();
    var username = document.getElementById("login_username").value;
    var password = document.getElementById("login_password").value;
    ajaxPost('/login_user.php',
             {"username":username, "password":password},
             displayResponse,
             displayResponse);
});

document.getElementById('register_token').addEventListener("submit", function(e) {
    e.preventDefault();
    ajaxPost('/u2f_register_data.php',
             {},
             u2fRegister,
             displayResponse);
});

document.getElementById('auth_form').addEventListener("submit", function(e) {
    e.preventDefault();
    ajaxPost('/u2f_auth_data.php',
             {},
             u2fSign,
             displayResponse);
});

// Helper functions

function urlencode(obj) {
  var str = [];
  for(var p in obj)
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  return str.join("&");
}
function ajaxPost(url, data, success, fail) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (XMLHttpRequest.DONE != xhr.readyState) { return; }
        var response = JSON.parse(xhr.responseText);
        if (xhr.status === 200) {
            if (success) { success(response); }
        }
        else {
            if (fail) { fail(response); }
        }
    }
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(urlencode(data));
}
function showPress() {
    document.getElementById('press').style.display = 'block';
}
function hidePress() {
    document.getElementById('press').style.display = 'none';
}

function setFieldText(field, text) {
    document.getElementById(field).value = text;
}

function displayResponse(resp) {
    document.getElementById('ajax-response').innerHTML = JSON.stringify(resp);
}

function showAuthError(code) {
    // https://developers.yubico.com/U2F/Libraries/Client_error_codes.html
    switch (code) {
    case 1:
        message = 'other error';
        break;
    case 2:
        message = 'bad request';
        break;
    case 3:
        message = 'unsupported client configuration';
        break;
    case 4:
        message = 'ineligible request';
        break;
    case 5:
        message = 'timeout';
        break;
    }
    alert(message);
};

// Manage the debug checkbox and fields

document.getElementById('debug').addEventListener('change', function(e) {
    box = document.getElementById('debug');
    if (box.checked) {
        document.body.setAttribute('class', 'debug');
    } else {
        document.body.setAttribute('class', '');
    }
});

