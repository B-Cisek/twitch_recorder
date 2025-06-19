import { ConsumeMessage } from 'amqplib'
import { BaseConsumer } from '../baseConsumer'
import { RabbitMQ_QUEUE_NAME } from '../../config/config'
import { StorageService } from '../../services/storageService'
import { RecordingService } from '../../services/recordingService'
import { RecordingMessage } from '../../types/interfaces'
import { RecordingApiHelper } from '../../helpers/recordingApiHelper'
import { RecordingStatus } from '../../types/enums'

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
                platform: data.platform
            })

            const path = new StorageService().prepareRecordingPath(data.recordingId)
            const recorder = new RecordingService()
            await recorder.start(data.channel, data.platform, path)
            await new RecordingApiHelper().updateRecording(data.recordingId, {
                status: RecordingStatus.RECORDING,
                startedAt: new Date().toISOString()
            })

            await this.acknowledge(message)
        } catch (error) {
            console.error('Error processing recording message:', error)
            await this.reject(message, false)
        }
    }
}
