import { ConsumeMessage } from 'amqplib'
import { BaseConsumer } from '../baseConsumer'
import { RabbitMQ_QUEUE_NAME } from '../../config/config'
import { StorageService } from '../../services/storageService'
import { RecordingService } from '../../services/recordingService'
import { RecordingMessage } from '../../types/interfaces'
import { RecordingApiHelper } from '../../helpers/recordingApiHelper'
import { RecordingStatus } from '../../types/enums'
import { logger } from '../../helpers/logger'

export class RecordingConsumer extends BaseConsumer {
    private readonly storageService: StorageService
    private readonly recordingApiHelper: RecordingApiHelper

    constructor() {
        super(RabbitMQ_QUEUE_NAME)
        this.storageService = new StorageService()
        this.recordingApiHelper = new RecordingApiHelper()
    }

    protected async handleMessage(message: ConsumeMessage | null): Promise<void> {
        if (!message) {
            logger.warn('Received null message, skipping processing')
            return
        }

        let recordingData: RecordingMessage

        try {
            recordingData = this.parseMessage(message)
            this.logReceivedMessage(recordingData)
        } catch (error) {
            logger.warn('Failed to parse message')
            await this.reject(message, false)
            return
        }

        try {
            await this.processRecording(recordingData)
            await this.acknowledge(message)
        } catch (error) {
            logger.error(
                {
                    error: error instanceof Error ? error.message : error,
                    recordingId: recordingData.recordingId,
                    channel: recordingData.channel,
                    platform: recordingData.platform
                },
                'Error processing recording message'
            )
            await this.reject(message, true)
        }
    }

    private parseMessage(message: ConsumeMessage): RecordingMessage {
        const content = message.content.toString()
        const data = JSON.parse(content) as RecordingMessage

        this.validateRecordingMessage(data)
        return data
    }

    private validateRecordingMessage(data: RecordingMessage): void {
        if (!data.recordingId || !data.channel || !data.platform) {
            throw new Error('Invalid recording message: missing required fields')
        }
    }

    private logReceivedMessage(data: RecordingMessage): void {
        logger.info(
            {
                recordingId: data.recordingId,
                channel: data.channel,
                platform: data.platform
            },
            'Received recording message'
        )
    }

    private async processRecording(data: RecordingMessage): Promise<void> {
        const recorder = new RecordingService()

        if (await this.isAlreadyRecording(recorder, data)) {
            logger.warn(`Recording already in progress for ${data.channel} on ${data.platform}`)
            return
        }

        const recordingPath = this.storageService.prepareRecordingPath(data.recordingId)

        await this.startRecording(recorder, data, recordingPath)
        await this.updateRecordingStatus(data.recordingId)
    }

    private async isAlreadyRecording(recorder: RecordingService, data: RecordingMessage): Promise<boolean> {
        return recorder.isRecording(data.channel, data.platform)
    }

    private async startRecording(recorder: RecordingService, data: RecordingMessage, recordingPath: string): Promise<void> {
        await recorder.start(
            data.channel,
            data.platform,
            recordingPath,
            async (code, signal) => {
                logger.info(data, `Recording stoped with code: ${code}`)
                await this.recordingApiHelper.updateRecording(data.recordingId, {
                    status: RecordingStatus.SUCCESS,
                    endedAt: new Date().toISOString()
                })
            },
            async (error) => {
                logger.error(error, 'Recording failed with error')
                await this.recordingApiHelper.updateRecording(data.recordingId, {
                    status: RecordingStatus.FAILED,
                    endedAt: new Date().toISOString()
                })
            }
        )
    }

    private async updateRecordingStatus(recordingId: string): Promise<void> {
        await this.recordingApiHelper.updateRecording(recordingId, {
            status: RecordingStatus.RECORDING,
            startedAt: new Date().toISOString()
        })
    }
}
