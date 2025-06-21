import express from 'express'
import http from 'http'
import { SERVER } from './config/config'
import { errorHandler } from './middlewares/errorHandler'
import { RecordingConsumer } from './rabbitmq/consumers/recordingConsumer'
import pino from 'pino-http'
import { logger } from './helpers/logger'
import { HealthController, VideoController } from './controllers'
export const app = express()
export let httpServer: ReturnType<typeof http.createServer>

app.use(express.urlencoded({ extended: true }))
app.use(express.json())
app.use(pino())
app.use(errorHandler)
app.use('/', new HealthController().getHealth)
app.use('/video/:uuid', new VideoController().getVideo)

httpServer = http.createServer(app)

const startConsumer = async () => {
    try {
        const consumer = new RecordingConsumer()
        await consumer.start()
        logger.info('RabbitMQ consumer started successfully')
    } catch (error) {
        logger.error('Failed to start RabbitMQ consumer:', error)
        process.exit(1)
    }
}

httpServer.listen(SERVER.PORT, () => {
    logger.info(`Server started at: ${SERVER.HOST}:${SERVER.PORT}`)
    startConsumer()
})
