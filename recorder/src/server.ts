import express from 'express'
import http from 'http'
import {SERVER} from './config/config'
import { errorHandler } from './middlewares/errorHandler'
import {RecordingConsumer} from "./rabbitmq/consumers/recordingConsumer";

export const app = express()
export let httpServer: ReturnType<typeof http.createServer>

app.use(express.urlencoded({ extended: true }))
app.use(express.json())
app.use(errorHandler)

app.get('/', (req, res) => {
    res.status(200).json({ status: 'ok' })
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
