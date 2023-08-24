import React, { useState, useEffect, useContext } from 'react'
import CurrentGroupPage from './currentGroup'
import { userContext } from "./App.js"
import AddGroupForm from './addGroup'
function GroupPage ({ selectedPage }) {


  const userInfo = useContext(userContext)
  const [openGroup, setOpenGroup] = useState(false)
  const [isFormOpen, setIsFormOpen] = useState(false)
  const [currentGroup, setCurrentGroup] = useState()
  // const userData = [  //在这里fetch下来所有group的信息
  //   {
  //     id: 1,
  //     name: 'Dijkstra'
  //   },
  //   {
  //     id: 2,
  //     name: 'Ica'
  //   },
  //   {
  //     id: 3,
  //     name: 'test'
  //   }
  // ]
  // const groupData = [
  //   {
  //     id: 1,
  //     name: 'Group One',
  //     creator: 'Dijkstra',
  //     description: 'This is the first group',
  //     users: [userData[0], userData[1]]
  //   },
  //   {
  //     id: 2,
  //     name: 'Group Two',
  //     creator: 'User Two',
  //     description: 'This is the second group'
  //   },
  //   {
  //     id: 3,
  //     name: 'Group Three',
  //     creator: 'User Three',
  //     description: 'This is the third group'
  //   },
  //   {
  //     id: 4,
  //     name: 'Group Four',
  //     creator: 'User Four',
  //     description: 'This is the fourth group'
  //   },
  //   {
  //     id: 5,
  //     name: 'Group Five',
  //     creator: 'User Five',
  //     description: 'This is the fifth group'
  //   }
  // ]


  const [groups, setGroups] = useState([])

  const data = {
    username: userInfo.username
  }

  useEffect(() => {
    fetch('http://localhost:3456/search_group', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        setGroups(data)
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }, [])

  function handleOpenClick (group) {

    console.log(group)
    setCurrentGroup(group)
    console.log(currentGroup)
    setOpenGroup(true)
  }

  function handleLeaveGroup () {
    setOpenGroup(false)
  }

  function handleOpenForm () {
    setIsFormOpen(true)
  }

  function handleCloseForm () {
    setIsFormOpen(false)
  }

  function handleSubmitForm (group) {

    let data = group

    fetch('http://localhost:3456/add_group', {
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
          alert("Group existed!")
        }
        else {
          alert("Create Successfully!")
        }
      })
      .catch((error) => {
        console.error('Error:', error)
      })
    console.log(group)
  }

  return (
    <div>
      {openGroup ? (
        <div>
          <CurrentGroupPage OnBack={handleLeaveGroup} group={currentGroup} />
        </div>
      ) : (
        <div>
          <h1>Groups</h1>
          <button onClick={handleOpenForm}>Add Group</button>
          {isFormOpen &&
            <AddGroupForm onSubmit={handleSubmitForm} onBack={handleCloseForm} />
          }
          <div>
            {(groups.length != 0) && groups.map(group => (
              <div key={group._id}>
                <h2>{group.group_name}</h2>
                <button onClick={() => { handleOpenClick(group) }}>Open the group</button>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}

export default GroupPage
