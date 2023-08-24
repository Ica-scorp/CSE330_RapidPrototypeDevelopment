import React, { useState, useEffect, useContext } from 'react'
import { userContext } from "./App.js"


function Confirmform ({ Onsubmit, Onback, sender }) {

  const data = {
    username: sender
  }
  return (
    <div>
      <p>I confirm that {sender} already paid me.</p>
      <button onClick={Onback}>cancel</button>
      <button onClick={Onsubmit}>confirm</button>
    </div>
  )
}

function InboxPage ({ selectedPage }) {

  const [refresh, setRefresh] = useState(false)
  const [messages, setMessages] = useState("")
  const [isOpenForm, setIsOpenForm] = useState(false)
  const [sender, setSender] = useState("")
  const userInfo = useContext(userContext)
  const [targetAmount, setTargetAmount] = useState(0)
  const [messageId, setMessageId] = useState("")

  const [demands, setDemands] = useState("")

  const data = {
    username: userInfo.username
  }

  useEffect(() => {
    fetch('http://localhost:3456/search_message', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        setMessages(data)
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }, [refresh])

  useEffect(() => {
    fetch('http://localhost:3456/search_demand', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        setDemands(data)
      })
      .catch(error => {
        console.error('获取 groups 失败:', error)
      })
  }, [refresh])

  function handleSubmit () {
    const data = {
      payer: sender,
      username: userInfo.username,
      amount: targetAmount,
      messageid: messageId
    }
    fetch('http://localhost:3456/confirm', {
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
          alert("Confirm success!")
          setRefresh(!refresh)
          setIsOpenForm(false)
          setSender("")
          setTargetAmount(0)
        }
      })
      .catch(error => {
        console.error('error', error)
      })

  }

  function handleClose () {
    setIsOpenForm(false)
    setSender("")
    setRefresh(!refresh)
  }

  function handleOpenForm (sender, amount, messageid) {
    setIsOpenForm(true)
    setSender(sender)
    setTargetAmount(amount)
    setMessageId(messageid)
  }

  return (

    <div className='inbox'>
      {isOpenForm ? (
        <div className='confirm'>
          <Confirmform Onsubmit={handleSubmit} Onback={handleClose} sender={sender} />
        </div>
      ) : (
        <div classname="allMessage">
          <div className='Messages'>
            {(messages.length !== 0) && messages.map(message => (
              <div key={message._id}>
                <div>User {message.payer} already send you {(0 - message.amount)}$. Please confirm</div>
                <button onClick={() => handleOpenForm(message.payer, message.amount, message._id)}>confirm</button>
              </div>
            ))
            }
          </div>
          <div className='demands'>
            {(demands.length !== 0) && demands.map(demand => (
              <div key={demand._id}>
                <div>User {demand.creditor} ask you to send {(demand.amount)}$. </div>
              </div>
            ))
            }
          </div>
        </div>
      )
      }
    </div>
  )
}
export default InboxPage