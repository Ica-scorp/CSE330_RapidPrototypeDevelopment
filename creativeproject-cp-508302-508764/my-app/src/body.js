import React, { useState } from 'react'
import HomePage from './home'
import InboxPage from './inbox'
import RecordPage from './record'
import GroupPage from './group'

function Body ({ selectedPage }) {
  return (
    <div>
      {selectedPage === 'home' && <HomePage />}
      {selectedPage === 'inbox' && <InboxPage />}
      {selectedPage === 'record' && <RecordPage />}
      {selectedPage === 'group' && <GroupPage />}
    </div>
  )
}
export default Body