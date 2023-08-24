function getUserPanel(){

    isLogin().then(response => {
        if (response.success) {//if user are login, show user's id and set the logout buttion
            headBody = document.getElementById("hasuser");
            welcome = document.createElement("p");
            welcome.textContent = "Welcome " + response.userName + "!";
            headBody.appendChild(welcome);

            loginPanel= document.getElementById("hasuser");
            loginPanel.setAttribute("style", "")

        } else {
            //otherwise show the login pannel
            loginPanel= document.getElementById("nouser");
            loginPanel.setAttribute("style", "")
        }
      });

}

function login(){//create the login form
    logindiv = document.getElementById("login-div");

    loginform = document.createElement("form");
    loginform.setAttribute("id", "login-form");

    labeluser = document.createElement("label");
    labeluser.setAttribute("for", "username");
    labeluser.innerHTML = "Username:";
    loginform.appendChild(labeluser);



    logindiv.appendChild(loginform);
}