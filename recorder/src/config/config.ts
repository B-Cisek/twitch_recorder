import dotenv from 'dotenv'

dotenv.config()

export const DEVELOPMENT = process.env.APP_ENV === 'development' || process.env.APP_ENV === 'dev'
export const HOST = process.env.APP_HOST || 'localhost'
export const PORT = process.env.APP_PORT ? Number(process.env.PORT) : 3000

export const SERVER = {
    HOST,
    PORT
}
