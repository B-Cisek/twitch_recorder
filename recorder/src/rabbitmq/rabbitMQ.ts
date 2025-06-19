import amqplib, { Channel, ChannelModel } from 'amqplib'
import { RabbitCredentials } from '../config/config'

export class RabbitMQ {
    private static connection: ChannelModel | null = null
    private static channel: Channel | null = null

    static async getConnection(): Promise<ChannelModel> {
        if (!this.connection) {
            this.connection = await amqplib.connect(RabbitCredentials.URL)

            this.connection.on('error', (err) => {
                console.error('RabbitMQ connection error:', err)
                this.connection = null
            })

            this.connection.on('close', () => {
                console.log('RabbitMQ connection closed')
                this.connection = null
            })
        }

        return this.connection
    }

    static async getChannel(): Promise<Channel> {
        if (!this.channel) {
            const connection = await this.getConnection()
            this.channel = await connection.createChannel()

            this.channel.on('error', (err) => {
                console.error('RabbitMQ channel error:', err)
                this.channel = null
            })

            this.channel.on('close', () => {
                console.log('RabbitMQ channel closed')
                this.channel = null
            })
        }

        return this.channel
    }

    static async closeConnection(): Promise<void> {
        if (this.channel) {
            await this.channel.close()
            this.channel = null
        }

        if (this.connection) {
            await this.connection.close()
            this.connection = null
        }
    }
}
