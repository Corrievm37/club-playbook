# AI Scene Generator - Setup Guide

## 🆓 100% Free CapCut-Style Animation

Generate full animated scenes with character movement, emotions, and lip-sync using Google Colab's free GPU.

## Quick Start (5 minutes)

### Step 1: Launch Local Server

```bash
cd ai-scene-generator
source venv/bin/activate
python app.py
```

Open http://localhost:5005

### Step 2: Extract Audio (Optional)

If you have a video and want to extract its audio:

1. Upload video file
2. Click "Extract Audio"
3. Download the extracted audio

### Step 3: Open Google Colab

1. Click the "Open Google Colab Notebook" button
2. Or manually open: `AnimateDiff_Colab.ipynb`
3. Upload to Google Colab

### Step 4: Generate Scene

In Google Colab:

1. Click **Runtime → Run all**
2. Upload your character image (skull character)
3. Upload your audio file
4. Enter a prompt: *"Animated skeleton character speaking with emotions, dramatic lighting, full body"*
5. Wait 10-30 minutes
6. Download the result

## What You Get

✅ **Full-body animation** - Not just face
✅ **Character movement** - Gestures and poses
✅ **Emotional expressions** - Synchronized with audio
✅ **Professional quality** - Similar to CapCut
✅ **100% FREE** - Uses Google Colab GPU

## Features

### Audio Extraction
- Extract audio from any video
- Supports: MP4, AVI, MOV, MKV, WebM
- Output: MP3, WAV, AAC

### Scene Generation
- Text-to-video with AnimateDiff
- Character-based animation
- Audio synchronization
- Custom prompts for style control

## Requirements

### Local (Audio Extraction)
- Python 3.8+
- FFmpeg (auto-installed)
- 2GB RAM

### Google Colab (Scene Generation)
- Free Google account
- Internet connection
- Patience (10-30 min per video)

## Tips for Best Results

### Character Images
- Use clear, well-lit images
- Front-facing works best
- 512x512 or higher resolution
- Your skull character will work great!

### Prompts
Good prompts include:
- Character description
- Action/emotion
- Lighting/style
- Camera angle

Example:
```
"Animated skeleton character in royal robes speaking dramatically, 
cinematic lighting, full body shot, fantasy style"
```

### Audio
- Keep under 30 seconds for testing
- Longer audio = longer processing time
- Clear speech works best

## Troubleshooting

### "No GPU available" in Colab
- Wait a few hours and try again
- Google Colab has usage limits
- Try different times of day

### "Out of memory"
- Reduce num_frames in notebook
- Use shorter audio
- Restart Colab runtime

### Slow generation
- This is normal on free GPU
- 10-30 minutes is expected
- Be patient - it's worth it!

## Cost

**$0.00** - Completely free using Google Colab

Compare to paid services:
- D-ID: $0.05-0.20 per video
- Runway: $0.05 per second
- HeyGen: $0.50-1.00 per video

## Next Steps

Once you've generated your first scene:
- Experiment with different prompts
- Try various character images
- Adjust animation settings in notebook
- Generate longer videos (more processing time)

This is a real AI video generation system that creates CapCut-quality animated scenes for free!
