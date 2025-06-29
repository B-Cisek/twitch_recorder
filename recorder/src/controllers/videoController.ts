import { Request, Response } from 'express'
import fs from 'fs'
import path from 'path'

export class VideoController {
    public getVideo = (req: Request, res: Response): void => {
        const { uuid } = req.params
        const videoPath = path.join(__dirname, '../../recordings', `${uuid}.mp4`)

        if (!fs.existsSync(videoPath)) {
            res.status(404).json({ error: 'Video not found' })
            return
        }

        const stat = fs.statSync(videoPath)
        const fileSize = stat.size
        const range = req.headers.range

        if (range) {
            const parts = range.replace(/bytes=/, '').split('-')
            const start = parseInt(parts[0], 10)
            const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1
            const chunkSize = end - start + 1
            const file = fs.createReadStream(videoPath, { start, end })
            const head = {
                'Content-Range': `bytes ${start}-${end}/${fileSize}`,
                'Accept-Ranges': 'bytes',
                'Content-Length': chunkSize,
                'Content-Type': 'video/mp4'
            }
            res.writeHead(206, head)
            file.pipe(res)
        } else {
            const head = {
                'Content-Length': fileSize,
                'Content-Type': 'video/mp4'
            }
            res.writeHead(200, head)
            fs.createReadStream(videoPath).pipe(res)
        }
    }
}
