import React, { useState, useEffect, useContext } from 'react'
import { userContext } from "./App.js"

function AddBillForm ({ onSubmit, onBack, group }) {
  const [description, setDescription] = useState('')
  const [time, setTime] = useState('')
  const [amount, setAmount] = useState('')
  const [selectedUsers, setSelectedUsers] = useState([])
  const userInfo = useContext(userContext)

  function handleSubmit (event) {
    if (!description || !time || !amount || selectedUsers.length === 0) {
      alert('please input all the information')
      return
    }
    event.preventDefault()

    const bill =
    {
      payer: userInfo.username,
      description: description,
      amount: amount,
      member: selectedUsers,
      time: time,
      groupname: group.group_name
    }
    onSubmit(bill)
    setDescription('')
    setTime('')
    setAmount('')
    setSelectedUsers([])
  }

  function handleCheckboxChange (event) {
    const user = event.target.value
    if (selectedUsers.indexOf(user) === -1) {
      setSelectedUsers([...selectedUsers, user])
    } else {
      setSelectedUsers(selectedUsers.filter((u) => u !== user))
    }
  }

  return (
    <div>
      <button onClick={onBack}>Cancel</button>
      <form onSubmit={handleSubmit}>
        <label>
          Description:
          <input type="text" value={description} onChange={event => setDescription(event.target.value)} />
        </label>
        <br />
        <label>
          Date:
          <input type="date" value={time} onChange={event => setTime(event.target.value)} />
        </label>
        <br />
        <label>
          Amount:
          <input type="text" value={amount} onChange={event => setAmount(event.target.value)} />
        </label>
        <br />
        <label>Users:</label>
        <div>
          {group.member.map(user => (
            <div key={user}>
              <label>
                <input type="checkbox" value={user} checked={selectedUsers.indexOf(user) !== -1} onChange={handleCheckboxChange} />
                {user}
              </label>
            </div>
          ))}
        </div>
        <button type="submit">Submit</button>
      </form>
    </div>
  )
}




function BillofGroup ({ group }) {

  const [isFormOpen, setisFormOpen] = useState(false)
  const userInfo = useContext(userContext)
  const [reloadBills, setReloadBills] = useState(false)
  // const billData = [
  //   {
  //     id: 1,
  //     name: 'Bill One',
  //     payer: 'User One',
  //     description: 'KFC',
  //     amount: 100
  //   },
  //   {
  //     id: 2,
  //     name: 'Bill Two',
  //     payer: 'User One',
  //     description: 'Uber',
  //     amount: 30
  //   },
  //   {
  //     id: 3,
  //     name: 'Bill Three',
  //     payer: 'User Two',
  //     description: 'smallYueka',
  //     amount: 6
  //   }
  // ]

  const [bills, setbills] = useState([])
  const data = {
    groupname: group.group_name
  }
  useEffect(() => {
    fetch('http://localhost:3456/get_record', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        setbills(data.records)
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }, [reloadBills])


  function handleAddForm () {
    setisFormOpen(true)
  }

  function handleSubmit (bill) {
    console.log(bill)
    const data = bill
    fetch('http://localhost:3456/add_record', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        if (data.success === "yes") {
          alert("Upload bill success!")
          setisFormOpen(false)
          setReloadBills(!reloadBills)
        }
      })
      .catch((error) => {
        console.error('Error:', error)
      })
  }

  function handleCloseAddForm () {
    setisFormOpen(false)
  }

  function handleDeleteBill (id) {

    const data = {
      recordid: id,
    }
    console.log(data)
    fetch('http://localhost:3456/delete_record', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        if (data.success === "yes") {
          alert("Delete Success!")
          setReloadBills(!reloadBills)
        }
      })
      .catch((error) => {
        console.error('Error:', error)
      })
  }

  return (
    <div>
      <div className='addBill'>
        <button onClick={handleAddForm}>Add bill</button>
      </div>
      {isFormOpen && <AddBillForm onSubmit={handleSubmit} onBack={handleCloseAddForm} group={group} />}
      <div className='bills'>
        <h1>Bills</h1>
        {(bills.length != 0) && bills.map(bill => (
          <div key={bill._id} style={{
            color: bill.member.includes(userInfo.username) ? 'red' : 'black'
          }}>
            <p>{bill.description}</p>
            <p>Payer: {bill.payer}</p>
            <p>Amount: {bill.amount}</p>
            <p>Time: {bill.time}</p>
            <p>Members:</p>
            <div>
              {bill.member.map((element) => (
                <div key={element.toString()}>{element}</div>
              ))}
            </div>
            {userInfo.username === group.admin && (
              <button onClick={() => handleDeleteBill(bill._id)}>Delete</button>
            )}
          </div>

        ))}
      </div>
    </div>
  )
}

export default BillofGroup