const server_api_url = "/auth.php"

function signup(login, password, callback) {
    $.ajax({
        url: server_api_url,
        method: 'POST',
        data: {
            action: 'create',
            login: login,
            pass: password
        },
        dataType: 'json',
        success: () => {
            localStorage.setItem("login", login)
            callback(true);
        },
        error: (xhr) => {
            callback(xhr.responseJSON);
        }
    })
}

function signin(login, password, callback) {
    $.ajax({
        url: server_api_url,
        method: 'POST',
        data: {
            action: 'login',
            login: login,
            pass: password
        },
        dataType: 'json',
        success: () => {
            localStorage.setItem("login", login);
            callback(true);
        },
        error: (xhr) => {
            callback(xhr.responseJSON);
        }
    })
}

function get_login(callback) {
    $.ajax({
        url: server_api_url,
        method: 'POST',
        data: {
            action: 'get_login'
        },
        dataType: 'json',
        success: (resp) => {
            localStorage.setItem("login", resp.login);
            callback(resp.login);
        },
        error: (xhr) => {
            localStorage.removeItem("login");
            callback(xhr.responseJSON.error);
        }
    })
}

function check_session(callback) {
    $.ajax({
        url: server_api_url,
        method: 'POST',
        data: {
            action: 'get_login'
        },
        dataType: 'json',
        success: (resp) => {
            localStorage.setItem("login", resp.login);
            callback(true);
        },
        error: () => {
            localStorage.removeItem("login");
            callback(false);
        }
    })
}

function logout(callback) {
    $.ajax({
        url: server_api_url,
        method: 'POST',
        data: {
            action: 'logout'
        },
        dataType: 'json',
        success: () => {
            localStorage.removeItem("login");
            callback(true);
        },
        error: (xhr) => {
            callback(xhr.responseJSON);
        }
    })
}