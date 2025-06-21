import axios, { AxiosInstance } from 'axios'
import { BACKEND_API_URL } from '../config/config'
import { RecordingStatus } from '../types/enums'
import { Recording } from '../types/interfaces'

export class RecordingApiHelper {
    private axios: AxiosInstance

    constructor() {
        this.axios = axios.create({
            baseURL: BACKEND_API_URL,
            headers: { 'Content-Type': 'application/json' }
        })
    }

    async setStatus(id: string, status: RecordingStatus): Promise<void> {
        await this.patchUpdate(id, { status })
    }

    async setStartedAt(id: string, startedAt: string): Promise<void> {
        await this.patchUpdate(id, { startedAt })
    }

    async setEndedAt(id: string, endedAt: string): Promise<void> {
        await this.patchUpdate(id, { endedAt })
    }

    async updateRecording(id: string, data: Recording): Promise<void> {
        await this.patchUpdate(id, data)
    }

    private async patchUpdate(id: string, data: Partial<Recording>): Promise<void> {
        await this.axios.patch(`/recordings/${id}/update`, data)
    }
}
