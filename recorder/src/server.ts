import express from 'express'
import http from 'http'
import fs from 'fs'
import path from 'path'
import { SERVER } from './config/config'
import { errorHandler } from './middlewares/errorHandler'
import { RecordingConsumer } from './rabbitmq/consumers/recordingConsumer'

export const app = express()
export let httpServer: ReturnType<typeof http.createServer>

app.use(express.urlencoded({ extended: true }))
app.use(express.json())
app.use(errorHandler)

app.get('/', (req, res) => {
    res.status(200).json({ status: 'ok' })
})

app.get('/video/:uuid', (req, res) => {
    const { uuid } = req.params
    const videoPath = path.join(__dirname, '../recordings', `${uuid}.mp4`)

    if (!fs.existsSync(videoPath)) {
        res.status(404).json({ error: 'Video not found' })
    }

    const stat = fs.statSync(videoPath)
    const fileSize = stat.size
    const range = req.headers.range

    if (range) {
        const parts = range.replace(/bytes=/, "").split("-")
        const start = parseInt(parts[0], 10)
        const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1
        const chunkSize = (end - start) + 1
        const file = fs.createReadStream(videoPath, { start, end })
        const head = {
            'Content-Range': `bytes ${start}-${end}/${fileSize}`,
            'Accept-Ranges': 'bytes',
            'Content-Length': chunkSize,
            'Content-Type': 'video/mp4',
        }
        res.writeHead(206, head)
        file.pipe(res)
    } else {
        const head = {
            'Content-Length': fileSize,
            'Content-Type': 'video/mp4',
        }
        res.writeHead(200, head)
        fs.createReadStream(videoPath).pipe(res)
    }
})

httpServer = http.createServer(app)

const startConsumer = async () => {
    try {
        const consumer = new RecordingConsumer()
        await consumer.start()
        console.log('RabbitMQ consumer started successfully')
    } catch (error) {
        console.error('Failed to start RabbitMQ consumer:', error)
        process.exit(1)
    }
}

httpServer.listen(SERVER.PORT, () => {
    console.log('-------------------------------')
    console.log(`Server started at: ${SERVER.HOST}:${SERVER.PORT}`)
    console.log('-------------------------------')

    // Start the consumer after the server is running
    startConsumer()
})
