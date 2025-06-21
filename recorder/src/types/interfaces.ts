import { RecordingStatus } from './enums'

export interface RecordingMessage {
    recordingId: string
    channel: string
    platform: string
}

export interface Recording {
    status?: RecordingStatus
    startedAt?: string
    endedAt?: string
}
