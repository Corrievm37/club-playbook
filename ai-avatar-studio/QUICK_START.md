# AI Avatar Studio - Quick Start

Create fully animated talking avatars from static images in minutes!

## Installation

```bash
cd ai-avatar-studio

# Create virtual environment
python3 -m venv venv
source venv/bin/activate  # Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Install FFmpeg
brew install ffmpeg  # macOS
# sudo apt-get install ffmpeg  # Linux
```

## Usage

### Web Interface (Recommended)

```bash
python app.py
```

Open http://localhost:5002

### Command Line

```bash
python generate_avatar.py \
  --image character.jpg \
  --audio dialogue.mp3 \
  --output avatar.mp4 \
  --style natural
```

## Features

- **Full Face Animation** - 3D head movements, expressions, lip-sync
- **Multiple Styles** - Minimal, Natural, Expressive, Dynamic
- **Works with Any Image** - Photos, avatars, artwork, characters
- **Professional Quality** - Video enhancement and stabilization

## Animation Styles

- **Minimal** - Subtle movements, focus on lip-sync
- **Natural** - Realistic head motion and expressions
- **Expressive** - Enhanced emotions and gestures
- **Dynamic** - Energetic, animated movements

## Requirements

- Python 3.8+
- FFmpeg
- 4GB+ RAM
- GPU recommended but not required

## What You Get

✓ Natural head movements (pitch, yaw, roll)
✓ Facial expressions synchronized with speech
✓ Accurate lip synchronization
✓ Eye blinks and movements
✓ Video stabilization and enhancement
✓ High-quality MP4 output

Perfect for creating talking avatars, animated characters, virtual presenters, and AI-generated videos!
