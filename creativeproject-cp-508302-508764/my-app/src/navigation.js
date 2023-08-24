import React, { useState } from 'react'

function Navigation ({ onNavigationItemClick }) {
  const [selectedPage, setSelectedPage] = useState('main')

  function handlePageClick (pageName) {
    setSelectedPage(pageName)
    onNavigationItemClick(pageName)
  }

  return (
    <div className='nav'>
      <ul>
        <li><a href="#" onClick={() => handlePageClick('home')}>Home</a></li>
        <li><a href="#" onClick={() => handlePageClick('inbox')}>Inbox</a></li>
        <li><a href="#" onClick={() => handlePageClick('record')}>Record</a></li>
        <li><a href="#" onClick={() => handlePageClick('group')}>Group</a></li>
      </ul>
    </div>
  )
}

export default Navigation