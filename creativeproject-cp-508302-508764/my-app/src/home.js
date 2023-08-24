import React, { useContext, useState } from 'react'
import { userContext } from "./App.js"


function LoginPage ({ onLogin, onBack }) {
  const [username, setUsername] = useState('')
  const [password, setPassword] = useState('')

  function handleUsernameChange (event) {
    setUsername(event.target.value)
  }

  function handlePasswordChange (event) {
    setPassword(event.target.value)
  }

  function handleSubmit (event) {
    event.preventDefault()
    onLogin(username, password)
  }

  return (
    <div className="login-page">
      <button onClick={onBack}>Back</button>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Username:</label>
          <input type="text" value={username} onChange={handleUsernameChange} />
        </div>
        <div>
          <label>Password:</label>
          <input type="password" value={password} onChange={handlePasswordChange} />
        </div>
        <button type="submit">Login</button>
      </form>
    </div>
  )
}

function SignupPage ({ onSignup, onBack }) {
  const [username, setUsername] = useState('')
  const [password, setPassword] = useState('')
  const [phone, setPhone] = useState('')

  function handleUsernameChange (event) {
    setUsername(event.target.value)
  }

  function handlePasswordChange (event) {
    setPassword(event.target.value)
  }

  function handlePhoneChange (event) {
    setPhone(event.target.value)
  }

  function handleSubmit (event) {
    event.preventDefault()
    onSignup(username, password, phone)
  }

  return (
    <div className="signup-page">
      <button onClick={onBack}>Back</button>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Username</label>
          <input type="text" value={username} onChange={handleUsernameChange} />
        </div>
        <div>
          <label>Password:</label>
          <input type="password" value={password} onChange={handlePasswordChange} />
        </div>
        <div>
          <label>Phone number:</label>
          <input type="text" value={phone} onChange={handlePhoneChange} />
        </div>
        <button type="submit">Sign up</button>
      </form>
    </div>
  )
}

function HomePage () {
  const [isPanelOpen, setIsPanelOpen] = useState(true)
  const [isLoginOpen, setIsLoginOpen] = useState(false)
  const [isSignupOpen, setIsSignupOpen] = useState(false)

  const userInfo = useContext(userContext)


  function handleLogin (username, password) {

    const data = {
      username: username,
      password: password,
    }
    fetch('http://localhost:3456/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data.success)
        if (data.success === "no") {
          alert("Incorrect Password!")
        }
        else {
          alert("Login Successfully!")
          setIsPanelOpen(true)//login 
          setIsLoginOpen(false)
          userInfo.setUsername(username)//need to modify
          userInfo.setIsLogin(true)
        }
      })
      .catch((error) => {
        console.error('Error:', error)
      })
  }

  function handleSignup (username, password, phone) {
    const data = {
      username: username,
      password: password,
      phonenumber: phone,
    }
    fetch('http://localhost:3456/add_user', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data.success)
        if (data.success === "no") {
          alert("Username already exist!")
        }
        else {
          alert("Created Successfully!")
          setIsPanelOpen(true)
          setIsSignupOpen(false)
        }
      })
      .catch((error) => {
        console.error('Error:', error)
      })
  }

  function handleOpenLogin () {
    setIsLoginOpen(true)
    setIsSignupOpen(false)
    setIsPanelOpen(false)
  }

  function handleCloseLogin () {
    setIsLoginOpen(false)
    setIsPanelOpen(true)
  }

  function handleOpenSignup () {
    setIsSignupOpen(true)
    setIsLoginOpen(false)
    setIsPanelOpen(false)
  }

  function handleCloseSignup () {
    setIsSignupOpen(false)
    setIsPanelOpen(true)
  }

  function handleOpenLogout () {
    setIsSignupOpen(false)
    setIsLoginOpen(false)
    setIsPanelOpen(true)
    userInfo.setIsLogin(false)
    userInfo.setUsername(null)
  }

  return (
    <div className='userPanel'>
      {isPanelOpen && (
        <div className='user'>
          {userInfo.isLogin ? (
            <div className='islogin'>
              <div> Welcome! {userInfo.username}</div>
              <button onClick={handleOpenLogout}>Logout</button>
            </div>

          ) : (
            <div className='notlogin'>
              <button onClick={handleOpenLogin}>Login</button>
              <button onClick={handleOpenSignup}>Signup</button>
            </div>
          )}
        </div>
      )
      }
      {isLoginOpen && <LoginPage onLogin={handleLogin} onBack={handleCloseLogin} />}
      {isSignupOpen && <SignupPage onSignup={handleSignup} onBack={handleCloseSignup} />}
    </div >
  )
}

export default HomePage
