# AI Avatar Studio

Transform static images into fully animated talking avatars with AI-powered facial expressions, head movements, and lip-sync.

## Features

### 🎭 Full Avatar Animation
- **3D Head Pose Control** - Natural head movements, tilts, and rotations
- **Facial Expressions** - Emotion-driven expressions synchronized with speech
- **Lip Synchronization** - Accurate mouth movements matching audio
- **Eye Animation** - Realistic blinking and eye movements
- **Body Gestures** - Upper body movements and gestures (optional)

### 🎨 Hybrid AI Pipeline
- **SadTalker** - 3D-aware face animation with head pose
- **Expression Transfer** - Emotion and expression mapping
- **Audio Analysis** - Speech-driven animation timing
- **Video Enhancement** - Upscaling and stabilization
- **Character Support** - Works with human faces, avatars, and stylized characters

### 🚀 Capabilities
- Upload any portrait image (photo, artwork, 3D render)
- Add audio (speech, dialogue, narration)
- Generate fully animated talking video
- Control animation intensity and style
- Export high-quality videos

## Installation

```bash
cd ai-avatar-studio
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
python download_models.py
```

## Usage

### Web Interface
```bash
python app.py
# Open http://localhost:5002
```

### Command Line
```bash
python generate_avatar.py \
  --image character.jpg \
  --audio dialogue.mp3 \
  --output animated_avatar.mp4 \
  --style natural \
  --intensity medium
```

## Animation Styles

- **Natural** - Subtle, realistic movements
- **Expressive** - Enhanced expressions and gestures
- **Dynamic** - Energetic movements and animations
- **Minimal** - Focus on lip-sync with minimal head movement

## Requirements

- Python 3.8+
- CUDA GPU recommended (8GB+ VRAM)
- 10GB disk space for models
- FFmpeg

## How It Works

1. **Face Analysis** - Detect facial landmarks and 3D structure
2. **Motion Generation** - Create head pose and expression sequences from audio
3. **Expression Mapping** - Transfer emotions and expressions to the face
4. **Lip-Sync** - Generate accurate mouth movements
5. **Video Synthesis** - Render animated frames with neural rendering
6. **Enhancement** - Stabilize and upscale final video

## Credits

Built with:
- SadTalker
- Face-vid2vid
- Wav2Lip
- GFPGAN (enhancement)
- Custom hybrid pipeline

## License

MIT License
