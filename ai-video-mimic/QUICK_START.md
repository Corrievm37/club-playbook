# Quick Start Guide

Get up and running in 5 minutes!

## Installation

```bash
cd ai-video-mimic

# Create virtual environment
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Install FFmpeg (macOS)
brew install ffmpeg

# Download model
python download_models.py
```

## Usage

### Command Line

```bash
python generate_video.py \
  --image path/to/photo.jpg \
  --audio path/to/song.mp3 \
  --output result.mp4
```

### Web Interface

```bash
python app.py
```

Open http://localhost:5000 in your browser.

## What You Need

- **Photo**: Any image with a visible face (JPG, PNG)
- **Audio**: Song or speech file (MP3, WAV, M4A)

## Output

The tool generates an MP4 video where the person in the photo appears to sing or speak the audio.

## Tips

- Use frontal face photos for best results
- Start with shorter audio clips (10-30 seconds)
- GPU recommended for faster processing
- CPU mode works but is slower

For detailed instructions, see `SETUP_GUIDE.md`.
