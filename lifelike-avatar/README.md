# Lifelike AI Avatar Generator

**Real AI-powered talking head generation** using neural networks and deep learning models.

## What This Actually Does

Unlike simple image warping, this system uses:
- **First Order Motion Model (FOMM)** - Neural network that transfers motion from driving video to source image
- **Wav2Lip** - State-of-the-art lip synchronization
- **GFPGAN** - Face enhancement and restoration
- **Real-ESRGAN** - Video upscaling for quality

## Features

✅ **Lifelike Animation** - Neural rendering, not image warping
✅ **Realistic Lip-Sync** - Accurate mouth movements
✅ **Natural Motion** - AI-generated facial movements
✅ **High Quality** - Enhanced and upscaled output
✅ **Works with Any Image** - Photos, artwork, avatars, characters

## Installation

```bash
cd lifelike-avatar
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
bash download_models.sh
```

## Usage

### Web Interface
```bash
python app.py
# Open http://localhost:5004
```

### Command Line
```bash
python generate.py --image character.jpg --audio speech.mp3 --output result.mp4
```

## How It Works

1. **Motion Generation** - Creates realistic facial motion from audio
2. **Neural Rendering** - Uses FOMM to animate the face naturally
3. **Lip Synchronization** - Wav2Lip ensures perfect audio-visual sync
4. **Enhancement** - GFPGAN improves face quality
5. **Upscaling** - Real-ESRGAN increases resolution

This produces **truly lifelike results**, not simple transformations.
