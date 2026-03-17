# AI Scene Generator - CapCut Style

Local AI-powered video scene generation with character animation, emotions, and full-body movement.

## Features

✅ **Text-to-Video Generation** - Create animated scenes from text prompts
✅ **Audio-to-Video** - Generate scenes synchronized with audio/dialogue
✅ **Character Animation** - Full-body movement with emotions and gestures
✅ **Audio Extraction** - Extract audio from existing videos
✅ **Custom Characters** - Use your own character images as reference
✅ **FREE** - Runs locally, no API costs

## Technology Stack

- **AnimateDiff** - Motion module for character animation
- **Stable Diffusion** - Image generation and character rendering
- **ControlNet** - Precise character pose control
- **ComfyUI** - Workflow management
- **FFmpeg** - Audio extraction and video processing

## Requirements

- **GPU:** NVIDIA with 8GB+ VRAM (12GB recommended)
- **RAM:** 16GB+ system RAM
- **Storage:** 20GB for models
- **OS:** macOS, Linux, or Windows

## Installation

```bash
cd ai-scene-generator
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
bash download_models.sh
```

## Usage

### Extract Audio from Video
```bash
python extract_audio.py --video input.mp4 --output audio.mp3
```

### Generate Animated Scene
```bash
python generate_scene.py \
  --character skull_character.jpg \
  --audio dialogue.mp3 \
  --prompt "Animated skeleton character speaking with emotions" \
  --output scene.mp4
```

### Web Interface
```bash
python app.py
# Open http://localhost:5005
```

## What You Get

- **Full animated scenes** with character movement
- **Emotional expressions** synchronized with dialogue
- **Body language and gestures**
- **Professional quality** similar to CapCut
- **Complete control** over style and animation

This is real AI video generation, creating new animated content from scratch.
