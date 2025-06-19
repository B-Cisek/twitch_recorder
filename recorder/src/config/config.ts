import dotenv from 'dotenv'

dotenv.config()

export const DEVELOPMENT = process.env.APP_ENV === 'development' || process.env.APP_ENV === 'dev'
export const HOST = process.env.APP_HOST || 'localhost'
export const PORT = process.env.APP_PORT || 3000
export const BACKEND_API_URL = process.env.BACKEND_API_URL || 'http://localhost:8080/api'

export const SERVER = {
    HOST,
    PORT
}

export const RabbitMQ_HOST = process.env.RABBITMQ_HOST || 'localhost'
export const RabbitMQ_PORT = process.env.RABBITMQ_PORT || 5672
export const RabbitMQ_USER = process.env.RABBITMQ_USER || 'guest'
export const RabbitMQ_PASS = process.env.RABBITMQ_PASS || 'guest'
export const RabbitMQ_QUEUE_NAME = process.env.RABBITMQ_QUEUE_NAME || 'messages'

export const RabbitCredentials = {
    HOST: RabbitMQ_HOST,
    PORT: RabbitMQ_PORT,
    USER: RabbitMQ_USER,
    PASS: RabbitMQ_PASS,
    URL: `amqp://${RabbitMQ_USER}:${RabbitMQ_PASS}@${RabbitMQ_HOST}:${RabbitMQ_PORT}`
}
