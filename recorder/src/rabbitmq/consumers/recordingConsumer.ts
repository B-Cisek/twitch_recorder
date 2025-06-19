import {Channel, ConsumeMessage} from 'amqplib'
import {BaseConsumer} from "../baseConsumer";
import {RabbitMQ_QUEUE_NAME} from "../../config/config";
import {StorageService} from "../../services/storageService";
import {RecordingService} from "../../services/recordingService";

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

            const path = new StorageService().prepareRecordingPath(data.recordingId)
            const recorder = new RecordingService();
            await recorder.start(data.channel, data.platform, path)
            await this.acknowledge(message)
        } catch (error) {
            console.error('Error processing recording message:', error)
            await this.reject(message, false)
        }
    }
}
