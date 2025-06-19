import { spawn, ChildProcessWithoutNullStreams } from 'child_process';

function getKey(channel: string, platform: string): string {
  return `${platform}:${channel}`;
}

export class RecordingService {
  private static processes: Map<string, ChildProcessWithoutNullStreams> = new Map();

  async start(channel: string, platform: string, outputPath: string): Promise<boolean> {
    const key = getKey(channel, platform);

    if (this.isRecording(channel, platform)) {
      return false;
    }

    const url = this.getStreamUrl(channel, platform);
    const streamlink = spawn('streamlink', [url, 'best', '-o', outputPath, '--twitch-disable-ads']);

    RecordingService.processes.set(key, streamlink);

    streamlink.on('exit', () => {
      RecordingService.processes.delete(key);
    });

    return true;
  }

  async stop(channel: string, platform: string): Promise<boolean> {
    const key = getKey(channel, platform);
    const proc = RecordingService.processes.get(key);
    if (!proc) return false;
    proc.kill();
    RecordingService.processes.delete(key);
    return true;
  }

  isRecording(channel: string, platform: string): boolean {
    const key = getKey(channel, platform);
    console.log(RecordingService.processes.has(key))
    return RecordingService.processes.has(key);
  }

  private getStreamUrl(channel: string, platform: string): string {
    switch (platform) {
      case 'twitch':
        return `https://twitch.tv/${channel}`;
      default:
        throw new Error(`Unsupported platform: ${platform}`);
    }
  }
}