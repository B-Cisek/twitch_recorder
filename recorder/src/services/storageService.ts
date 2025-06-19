import fs from 'fs'
import path from 'path'

const BASE_RECORDINGS_DIR = path.resolve(__dirname, '../../recordings')

export class StorageService {
    getRecordingPath(uuid: string): string {
        return path.join(BASE_RECORDINGS_DIR, `${uuid}.mp4`)
    }

    prepareRecordingPath(uuid: string): string {
        if (!fs.existsSync(BASE_RECORDINGS_DIR)) {
            fs.mkdirSync(BASE_RECORDINGS_DIR, { recursive: true })
        }
        return this.getRecordingPath(uuid)
    }
}
