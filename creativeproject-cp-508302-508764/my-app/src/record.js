import React, { useState, useEffect, useContext } from 'react'
import { userContext } from "./App.js"

function Payform ({ Onsubmit, Onback, target }) {

  const [targetPhoneNumber, setTargetPhoneNumber] = useState(3149349814)

  const data = {
    username: target
  }
  // useEffect(() => {
  //   fetch('http://localhost:3456/search_user_phone_number', {
  //     method: 'POST',
  //     headers: {
  //       'Content-Type': 'application/json'
  //     },
  //     body: JSON.stringify(data)
  //   })
  //     .then(response => response.json())
  //     .then(data => {
  //       console.log(data)
  //       setTargetPhoneNumber(data)
  //     })
  //     .catch(error => {
  //       console.error('获取 groups 失败:', error)
  //     })
  // }, [])

  return (
    <div>
      <p>{target.username}'s phone number is {target.phone_number}</p>
      <button onClick={Onback}>cancel</button>
      <button onClick={Onsubmit}>pay</button>
    </div>
  )
}


function RecordPage ({ selectedPage }) {

  const [refresh, setRefresh] = useState(false)
  const [records, setRecords] = useState("")
  const [isOpenForm, setIsOpenForm] = useState(false)
  const [targetUser, settargetUser] = useState("")
  const [targetAmount, setTargetAmount] = useState(0)
  const userInfo = useContext(userContext)
  const [totalBalance, setTotalBalance] = useState(0)

  const data = {
    username: userInfo.username
  }

  useEffect(() => {
    fetch('http://localhost:3456/search_user_record_special', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        setRecords(data)
        let sum = 0
        if (data.length > 0) {
          data.forEach((dat) => {
            sum = sum + dat.balance
          })
        }
        setTotalBalance(sum)
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }, [refresh])

  function handleSubmit () {
    const data = {
      payer: targetUser.username,
      username: userInfo.username,
      amount: targetAmount
    }
    fetch('http://localhost:3456/payback', {
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
          alert("Send success!")
          setIsOpenForm(false)
          settargetUser("")
          setTargetAmount(0)
          setRefresh(!refresh)
        }
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }

  function handleClose () {
    setIsOpenForm(false)
    settargetUser("")
  }

  function handleOpenForm (target, amount) {
    setIsOpenForm(true)
    settargetUser(target)
    setTargetAmount(amount)
  }

  function handleAsk (record) {
    const data = {
      asker: userInfo.username,
      receiver: record.username,
      amount: record.balance

    }
    fetch('http://localhost:3456/payback_demand', {
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
          alert("Asked Success")
          setRefresh(!refresh)
        }
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }

  return (

    <div>
      {isOpenForm ? (
        <div>
          <Payform Onsubmit={handleSubmit} Onback={handleClose} target={targetUser} />
        </div>
      ) : (
        <div className='Records'>
          <div clasName="TotalBalance">
            <h2>Your current balance is: {totalBalance}$</h2>
          </div>
          {(records.length !== 0) && records.map(record => (
            <div>
              {console.log(record.balance > 0)}
              {record.balance > 0 ? (
                <div>
                  <div> {record.username} should pay you {record.balance}$</div>
                  <button onClick={() => handleAsk(record)}>Ask</button>
                </div>
              ) : (
                <div>
                  <div> You should pay {record.username} {0 - record.balance}$</div>
                  <button onClick={() => handleOpenForm(record, (record.balance))}>Pay the bill</button>
                </div>
              )
              }

            </div>
          ))
          }
        </div>
      )
      }
    </div >
  )
}
export default RecordPage