import logo from './logo.svg'
import './App.css'
import React, { createContext, useState } from 'react'
import Navigation from './navigation'
import Body from './body'
export const userContext = createContext({
  username: "no_user",
  isLogin: true,
  setUsername: () => { },
  setIsLogin: () => { }
})

function App () {
  const [selectedPage, setSelectedPage] = useState('main')
  const [username, setUsername] = useState(null)
  const [isLogin, setIsLogin] = useState(false)
  function handleNavigationItemClick (pageName) {
    setSelectedPage(pageName)
  }


  return (
    <userContext.Provider value={{ username, isLogin, setUsername, setIsLogin }}>
      <div className="App">
        <Navigation onNavigationItemClick={handleNavigationItemClick} />
        <Body selectedPage={selectedPage} />
      </div>
    </userContext.Provider >
  )
}

export default App
