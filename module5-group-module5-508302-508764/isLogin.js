function isLogin() {
    return fetch("isLogin.php")//asking this php file for login information and return json data
      .then(response => response.json())
      .then(data => {
        if (data.login == true) {
          return {
            success: true,
            userName: data.userName,
            userId: data.userId,
            token:data.token
          };
        } else {
          return { success: false };
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
  }
  