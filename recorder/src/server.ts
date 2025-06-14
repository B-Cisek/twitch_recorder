import express from 'express'
import http from 'http'
import { SERVER } from './config/config'
import { errorHandler } from './middlewares/errorHandler'

export const app = express()
export let httpServer: ReturnType<typeof http.createServer>

app.use(express.urlencoded({ extended: true }))
app.use(express.json())
app.use(errorHandler)

app.get('/', (req, res) => {
    res.status(200).json({ status: 'ok' })
})

httpServer = http.createServer(app)

httpServer.listen(SERVER.PORT, () => {
    console.log('-------------------------------')
    console.log(`Serer start at: ${SERVER.HOST}:${SERVER.PORT}`)
    console.log('-------------------------------')
})
