const express = require('express')
const http = require('http')
const cors = require('cors')
const { Server } = require('socket.io')
const fs = require('fs')
const path = require('path')

const PORT = process.env.SOCKET_PORT ? parseInt(process.env.SOCKET_PORT, 10) : 3000
const app = express()
const server = http.createServer(app)
const io = new Server(server, {
  cors: { origin: '*', methods: ['GET', 'POST'] }
})

app.use(cors({ origin: '*', credentials: true }))
app.use(express.json())

const DATA_DIR = path.join(__dirname, 'api', 'storage', 'data')
const CHAT_STORE = path.join(DATA_DIR, 'chat.json')

function ensureStore() {
  try {
    if (!fs.existsSync(DATA_DIR)) fs.mkdirSync(DATA_DIR, { recursive: true })
    if (!fs.existsSync(CHAT_STORE)) {
      fs.writeFileSync(CHAT_STORE, JSON.stringify({ conversations: [], messages: [], next_msg_id: 1 }, null, 2))
    }
  } catch (e) {}
}

function readStore() {
  ensureStore()
  try { return JSON.parse(fs.readFileSync(CHAT_STORE, 'utf8') || '{}') } catch { return { conversations: [], messages: [], next_msg_id: 1 } }
}

function writeStore(data) {
  try { fs.writeFileSync(CHAT_STORE, JSON.stringify(data, null, 2)) } catch (e) {}
}

function listMessages(bookingId) {
  const store = readStore()
  return (store.messages || []).filter(m => Number(m.booking_id) === Number(bookingId)).sort((a, b) => (a.ts || 0) - (b.ts || 0))
}

function addMessage(bookingId, payload) {
  const store = readStore()
  const id = store.next_msg_id || 1
  const msg = {
    id,
    booking_id: Number(bookingId),
    text: String(payload.text || '').slice(0, 2000),
    sender: String(payload.sender || ''),
    sender_id: payload.sender_id || null,
    ts: Date.now()
  }
  store.messages = store.messages || []
  store.messages.push(msg)
  store.next_msg_id = id + 1
  writeStore(store)
  return msg
}

io.on('connection', socket => {
  socket.on('user:join', data => {
    const uid = data && (data.user_id || data.uid)
    if (uid) { socket.join(`user:${uid}`) }
  })

  socket.on('role:join', data => {
    const role = data && data.role
    if (role) { socket.join(`role:${role}`) }
  })

  socket.on('booking:join', data => {
    const bookingId = data && (data.booking_id || data.bookingId)
    if (!bookingId) return
    const room = `booking:${bookingId}`
    socket.join(room)
    const messages = listMessages(bookingId)
    socket.emit('booking:history', { booking_id: Number(bookingId), messages })
  })

  socket.on('booking:message', data => {
    const bookingId = data && (data.booking_id || data.bookingId)
    if (!bookingId) return
    const message = addMessage(bookingId, data || {})
    io.to(`booking:${bookingId}`).emit('booking:message:new', { booking_id: Number(bookingId), message })
  })
})

app.post('/notify', (req, res) => {
  const payload = req.body || {}
  try {
    const type = payload.type || payload.event || 'update'
    const bookingId = payload.booking_id || payload.bookingId
    const targetUserId = payload.user_id || payload.userId
    if (bookingId) io.to(`booking:${bookingId}`).emit('notify', { type, booking_id: Number(bookingId), data: payload })
    if (targetUserId) io.to(`user:${targetUserId}`).emit('notify', { type, data: payload })
    io.emit('notify', { type, data: payload })
  } catch (e) {}
  res.json({ success: true })
})

app.get('/', (_req, res) => { res.json({ ok: true }) })

server.listen(PORT, '0.0.0.0', () => {
  ensureStore()
  // eslint-disable-next-line no-console
  console.log(`Socket server listening on http://localhost:${PORT}`)
})

