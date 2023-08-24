const mongoose = require("mongoose")

const UserSchema = new mongoose.Schema({
  name: {
    type: String,
    required: true,
  },
  password: {
    type: String,
    required: true,
  },
  phone_number: {
    type: String,
    required: true,
  },
})

const User = mongoose.model("User", UserSchema)

const RecordSchema = new mongoose.Schema({
  payer: {
    type: String,
    required: true,
  },
  member: {
    type: Array,
    required: true,
  },
  amount: {
    type: Number,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  group_name: {
    type: String,
    required: true,
  },
  time: {
    type: Date,
    required: true
  }
})

const Record = mongoose.model("Record", RecordSchema)

const GroupSchema = new mongoose.Schema({
  admin: {
    type: String,
    required: true,
  },
  member: {
    type: Array,
    required: true,
  },
  group_name: {
    type: String,
    required: true,
  },
})


const Group = mongoose.model("Group", GroupSchema)


const MessageSchema = new mongoose.Schema({
  payer: {
    type: String,
    required: true,
  },
  creditor: {
    type: String,
    required: true,
  },
  amount: {
    type: Number,
    required: true,
  },
  valid: {
    type: String,
    required: true,
  }
})

const Message = mongoose.model("Message", MessageSchema)


module.exports = { User, Record, Group, Message };

