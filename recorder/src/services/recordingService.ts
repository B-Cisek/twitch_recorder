import { spawn, ChildProcessWithoutNullStreams } from 'child_process'
import { logger } from '../helpers/logger'

function getKey(channel: string, platform: string): string {
    return `${platform}:${channel}`
}

export class RecordingService {
    private static processes: Map<string, ChildProcessWithoutNullStreams> = new Map()

    async start(
        channel: string,
        platform: string,
        outputPath: string,
        onExit: (code: number | null, signal: string | null) => Promise<void>,
        onError: (error: Error) => Promise<void>
    ): Promise<boolean> {
        const key = getKey(channel, platform)

        if (this.isRecording(channel, platform)) {
            return false
        }

        const url = this.getStreamUrl(channel, platform)
        const streamlink = spawn('streamlink', [url, 'best', '-o', outputPath, '--twitch-disable-ads'])

        RecordingService.processes.set(key, streamlink)

        streamlink.on('exit', async (code, signal) => {
            RecordingService.processes.delete(key)
            await onExit(code, signal)
        })

        streamlink.on('error', async (error) => {
            RecordingService.processes.delete(key)
            await onError(error)
        })

        return true
    }

    async stop(channel: string, platform: string): Promise<boolean> {
        const key = getKey(channel, platform)
        const proc = RecordingService.processes.get(key)
        if (!proc) return false
        proc.kill()
        RecordingService.processes.delete(key)
        return true
    }

    isRecording(channel: string, platform: string): boolean {
        const key = getKey(channel, platform)
        logger.info(`Channel [${channel}] is currently recording: ${RecordingService.processes.has(key)}`)
        return RecordingService.processes.has(key)
    }

    private getStreamUrl(channel: string, platform: string): string {
        switch (platform) {
            case 'twitch':
                return `https://twitch.tv/${channel}`
            default:
                throw new Error(`Unsupported platform: ${platform}`)
        }
    }
}
