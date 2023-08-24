//const routes = require('./routes')

var express = require('express')
const ObjectId = require('bson').ObjectId
const bodyParser = require('body-parser')
const { User, Record, Group, Message } = require("./models")
var app = express()
//app.use('/', routes)



app.use(express.json())
const mongoose = require("mongoose")
mongoose.connect('mongodb+srv://user_washu_2026:7WJvpqncFCVXEpeK@cluster0.cvozzlu.mongodb.net/Bills',
   {
      useNewUrlParser: true
   }
)
const db = mongoose.connection
db.on("error", console.error.bind(console, "connection error: "))
db.once("open", function () {
   console.log("Connected successfully")
})



app.use(bodyParser.json())
var cors = require("cors")
app.use(cors())
app.use(bodyParser.urlencoded({ extended: false }))
app.get('/', function (req, res) {
   res.send('Hello World')
})

var server = app.listen(3456, function () {
   var host = server.address().address
   var port = server.address().port

   console.log("Example app listening at http://%s:%s", host, port)
})

app.post("/add_user", async (request, response) => {
   username = request.body.username
   password = request.body.password
   phonenumber = request.body.phonenumber
   let count = await db.collection('users').find({ "username": username }).toArray()
   if (count.length != 0) {
      var data = {
         success: "no",
      }
      response.send(data)

   } else {
      const user = new User({
         name: username,
         password: password,
         phone_number: phonenumber,
      })
      var data = {
         success: "yes",
      }
      try {
         await user.save()
         response.send(data)
      } catch (error) {
         response.status(500).send(error)
      }
   }
})
app.post("/login", async (request, response) => {
   username = request.body.username
   password = request.body.password

   let result = await db.collection('users').find({ name: username }).toArray()
   var login_success = "no"
   if (result.length != 0) {
      if (result[0].password == password) {
         login_success = "yes"
      }
   }
   var data = {
      success: login_success,
   }
   try {
      response.send(data)
   } catch (error) {
      response.status(500).send(error)
   }


})

app.post("/add_group", async (request, response) => {
   member = request.body.member
   admin = request.body.username
   groupname = request.body.groupname
   console.log(request.body)
   let result = await db.collection('groups').find({ group_name: groupname }).toArray()
   if (result.length != 0) {
      var data = {
         success: "no",
      }
      response.send(data)

   } else {
      true_member = []

      const promises = member.map(async (element) => {
         let tmp_result = await db.collection('users').find({ name: element }).toArray()
         if (tmp_result.length != 0) {
            true_member.push(element)
         }
      })
      Promise.all(promises)
         .then(async () => {
            const group = new Group({
               admin: admin,
               member: true_member,
               group_name: groupname,
            })
            return group.save()
         })
         .then((group) => {
            response.send(group)
         })
         .catch((error) => {
            response.status(500).send(error)
         })
   }
})

app.post("/add_record", async (request, response) => {
   payer = request.body.payer
   member = request.body.member
   amount = request.body.amount
   description = request.body.description
   group_name = request.body.groupname
   time = request.body.time
   const record = new Record({
      payer: payer,
      member: member,
      amount: amount,
      description: description,
      group_name: group_name,
      time: time
   })
   try {
      await record.save()
      var data = {
         success: "yes",
      }
      response.send(data)
   } catch (error) {
      response.status(500).send(error)
   }

})
app.post("/delete_record", async (request, response) => {
   record_id = request.body.recordid
   console.log("ssssssssssss")
   console.log(record_id)
   try {
      await db.collection('records').deleteOne({ _id: new ObjectId(record_id) }).then((test) => {
         var data = {
            success: "yes",
         }

         console.log(test)
         response.send(data)
      })


   } catch (error) {
      console.log(error)
      response.status(500).send(error)
   }

})
app.post("/search_group", async (request, response) => {
   let username = request.body.username
   array1 = await db.collection('groups').find({ member: username }).toArray()
   try {
      response.send(array1)
   } catch (error) {
      response.status(500).send(error)
   }
})

app.post("/search_user_record_in_group", async (request, response) => {
   let groupname = request.body.groupname
   let username = request.body.username
   array1 = await db.collection('Record').find({ group_name: groupname, payer: username }).toArray()
   array2 = await db.collection('Record').find({ group_name: groupname, members: username }).toArray()
   const combinedArray = array1.concat(array2)
   try {
      response.send(combinedArray)
   } catch (error) {
      response.status(500).send(error)
   }
})
app.post("/get_record", async (request, response) => {
   group_name = request.body.groupname
   admin_array = await db.collection('groups').find({ group_name: group_name }).toArray()
   admin_name = admin_array[0].admin
   records_list = await db.collection('records').find({ group_name: group_name }).toArray()
   const data = {
      records: records_list,
      admin: admin_name,
   }
   response.send(data)
})

app.post("/search_user_record_special", async (request, response) => {
   username = request.body.username
   const result = await db.collection('users').find().toArray()

   const data = []
   const promises = result.map(async (payer) => {
      if (payer.name !== username) {
         console.log("payer" + payer.phone_number)
         array1 = await db.collection('records').find({ $or: [{ payer: payer.name, member: { $elemMatch: { $eq: username } } }, { payer: username, member: { $elemMatch: { $eq: payer.name } } }] }).toArray()

         var paid_amount = 0
         var owed_amount = 0
         console.log("all array" + array1)
         array1.forEach((record_) => {
            console.log("record" + record_)
            if (record_.payer === username) {
               var arr_length = record_.member.length
               var delta = record_.amount / arr_length
               paid_amount = paid_amount + delta

            } else {
               var arr_length = record_.member.length
               var delta = record_.amount / arr_length
               owed_amount = owed_amount + delta
            }
         })

         balance_amount = paid_amount - owed_amount
         if (balance_amount != 0) {
            data.push({ username: payer.name, balance: balance_amount, phone_number: payer.phone_number })
         }
      }
   })
   Promise.all(promises).then(() => {
      console.log(data)
      try {
         response.send(data)
      } catch (error) {
         response.status(500).send(error)
      }
   })


})
app.post("/payback", async (request, response) => {
   let payer = request.body.payer
   let username = request.body.username
   let amount = request.body.amount
   const message = new Message({
      payer: username,
      creditor: payer,
      amount: amount,
      valid: "no"
   })
   try {
      await message.save()
      var data = {
         success: "yes",
      }
      response.send(data)
   } catch (error) {
      response.status(500).send(error)
   }

})

//!需要添加一个phonenumber的请求！
//添加一个confirm

app.post("/search_message", async (request, response) => {
   let username = request.body.username
   array1 = await db.collection('messages').find({ creditor: username, valid: "no" }).toArray()
   try {
      response.send(array1)

   } catch {
      response.status(500).send(error)
   }

})

app.post("/confirm", async (request, response) => {
   console.log(request.body)
   let payer = request.body.payer
   let username = request.body.username
   let amount = request.body.amount
   let message_id = request.body.messageid
   member_list = []
   member_list.push(payer)
   const record = new Record({
      payer: username,
      member: member_list,
      amount: amount,
      description: "PayBack",
      group_name: "PayBackGroup",
      time: new Date,
   })

   try {
      await record.save()
      await db.collection('messages').updateOne({ _id: new ObjectId(message_id) }, { $set: { valid: "yes" } })
      var data = {
         success: "yes",
      }
      response.send(data)
   } catch (error) {
      console.log(error)
   }

})

app.post("/payback_demand", async (request, response) => {
   let payer = request.body.receiver
   let username = request.body.asker
   let amount = request.body.amount
   console.log("demand")
   console.log(request.body)
   const message = new Message({
      payer: payer,
      creditor: username,
      amount: amount,
      valid: "demand"
   })
   try {
      await message.save()
      response.send(data)
   } catch (error) {
      response.status(500).send(error)
   }

})

app.post("/search_demand", async (request, response) => {//search demand for payback from other users
   let username = request.body.username
   array1 = await db.collection('messages').find({ payer: username, valid: "demand" }).toArray()
   try {
      console.log(array1)
      response.send(array1)
   } catch {
      response.status(500).send(error)
   }

})