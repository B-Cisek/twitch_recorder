import { ConsumeMessage } from 'amqplib'
import {BaseConsumer} from "../baseConsumer";
import {RabbitMQ_QUEUE_NAME} from "../../config/config";

interface RecordingMessage {
    recordingId: string
    channel: string
    platform: string
    startAt?: string
    endAt?: string
}

export class RecordingConsumer extends BaseConsumer {
    constructor() {
        super(RabbitMQ_QUEUE_NAME)
    }

    protected async handleMessage(message: ConsumeMessage | null): Promise<void> {
        if (!message) {
            return
        }

        try {
            const content = message.content.toString()
            const data = JSON.parse(content) as RecordingMessage

            console.log('Received recording message:', {
                recordingId: data.recordingId,
                channel: data.channel,
                platform: data.platform,
                startAt: data.startAt,
                endAt: data.endAt
            })

            // Here you would implement your recording logic
            // For example:
            // await this.recordingService.startRecording(data)

            // Acknowledge the message after successful processing
            await this.acknowledge(message)
        } catch (error) {
            console.error('Error processing recording message:', error)
            // Reject the message if there's an error
            await this.reject(message, false)
        }
    }
}
