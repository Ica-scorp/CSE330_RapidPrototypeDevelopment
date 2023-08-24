import React, { useState, useEffect, useContext } from 'react'
import { userContext } from "./App.js"

function AddGroupForm ({ onSubmit, onBack }) {
  const [name, setName] = useState('')
  const [memberList, setMemberList] = useState('')
  const userInfo = useContext(userContext)
  const [creater, setCreater] = useState(userInfo.username)

  function handleSubmit (event) {
    if (!name) {
      alert('Please input all the information')
      return
    }
    event.preventDefault()

    const members = memberList.split(',').map(item => item.trim()).concat(creater)

    const group = {
      groupname: name,
      username: creater,
      member: members
    }
    onSubmit(group)
    setName('')
    setMemberList('')
  }

  return (
    <div>
      <button onClick={onBack}>Cancel</button>
      <form onSubmit={handleSubmit}>
        <label>
          Group Name:
          <input type="text" value={name} onChange={event => setName(event.target.value)} />
        </label>
        <br />
        <label>
          Members(seperate with ,):
          <input type="text" value={memberList} onChange={event => setMemberList(event.target.value)} />
        </label>
        <br />
        <button type="submit">Submit</button>
      </form>
    </div>
  )
}

export default AddGroupForm