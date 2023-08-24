import React, { useState, useEffect } from 'react'
import BillofGroup from './billofGroup'

function CurrentGroupPage ({ OnBack, group }) {
  return (
    <div>
      <button onClick={OnBack}>Back</button>
      <h1>Welcome to {group.group_name}</h1>
      <p>The group creater is {group.admin}</p>
      <BillofGroup group={group} />

    </div>
  )
}
export default CurrentGroupPage