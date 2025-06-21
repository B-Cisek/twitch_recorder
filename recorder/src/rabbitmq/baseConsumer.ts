import { Channel, ConsumeMessage } from 'amqplib'
import { RabbitMQ } from './rabbitMQ'
import {logger} from "../helpers/logger";

export abstract class BaseConsumer {
    protected constructor(
        protected readonly queueName: string,
        protected readonly prefetchCount: number = 1
    ) {}

    protected abstract handleMessage(message: ConsumeMessage | null): Promise<void>

    public async start(): Promise<void> {
        try {
            const channel = await RabbitMQ.getChannel()
            await this.setupChannel(channel)

            logger.info(`Starting consumer for queue: ${this.queueName}`)
            await channel.consume(this.queueName, async (message) => {
                try {
                    await this.handleMessage(message)
                } catch (error) {
                    logger.error(error, `Error processing message`)
                    if (message && channel) {
                        channel.nack(message, false, false)
                    }
                }
            })
        } catch (error) {
            logger.error(error, `Failed to start consumer for ${this.queueName}`)
            throw error
        }
    }

    protected async setupChannel(channel: Channel): Promise<void> {
        await channel.prefetch(this.prefetchCount)
        await channel.assertQueue(this.queueName, {
            durable: true
        })
    }

    protected async acknowledge(message: ConsumeMessage): Promise<void> {
        const channel = await RabbitMQ.getChannel()
        channel.ack(message)
    }

    protected async reject(message: ConsumeMessage, requeue: boolean = false): Promise<void> {
        const channel = await RabbitMQ.getChannel()
        channel.nack(message, false, requeue)
    }
}
