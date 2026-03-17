# AI Video Mimicking Tool

Transform static photos into singing videos using AI-powered lip-sync technology.

## Features

- Upload any photo with a visible face
- Attach an audio file (song, speech, etc.)
- Generate realistic lip-synced video where the photo "sings" the audio
- Powered by Wav2Lip deep learning model

## Requirements

- Python 3.8+
- CUDA-capable GPU (recommended for faster processing)
- ~2GB disk space for models
- FFmpeg installed on system

## Installation

### 1. Install System Dependencies

**macOS:**
```bash
brew install ffmpeg
```

**Linux:**
```bash
sudo apt-get install ffmpeg
```

### 2. Install Python Dependencies

```bash
cd ai-video-mimic
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
pip install -r requirements.txt
```

### 3. Download Wav2Lip Model

The model will be automatically downloaded on first run, or manually:

```bash
python download_models.py
```

## Usage

### Command Line Interface

```bash
python generate_video.py --image path/to/photo.jpg --audio path/to/song.mp3 --output result.mp4
```

### Web Interface

```bash
python app.py
```

Then open http://localhost:5000 in your browser.

## Options

- `--image`: Path to input image (JPG, PNG)
- `--audio`: Path to audio file (MP3, WAV, M4A)
- `--output`: Output video path (default: output.mp4)
- `--quality`: Video quality: 'low', 'medium', 'high' (default: medium)
- `--fps`: Frames per second (default: 25)

## How It Works

1. **Face Detection**: Detects face and facial landmarks in the input image
2. **Audio Processing**: Extracts mel-spectrogram features from audio
3. **Lip-Sync Generation**: Wav2Lip model generates mouth movements matching audio
4. **Video Synthesis**: Combines generated frames into final video

## Performance

- **CPU**: ~1-2 minutes per 10 seconds of audio
- **GPU (CUDA)**: ~10-20 seconds per 10 seconds of audio

## Limitations

- Works best with frontal face photos
- Audio quality affects lip-sync accuracy
- Very long audio files (>5 minutes) may require significant processing time

## Credits

Based on Wav2Lip: https://github.com/Rudrabha/Wav2Lip

## License

MIT License - See LICENSE file for details
