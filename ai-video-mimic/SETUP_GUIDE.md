# AI Video Mimicking Tool - Setup Guide

Complete step-by-step guide to get the AI video mimicking tool running on your system.

## Prerequisites

### System Requirements

- **Operating System**: macOS, Linux, or Windows
- **Python**: 3.8 or higher
- **RAM**: Minimum 8GB (16GB recommended)
- **Storage**: ~3GB free space (for models and dependencies)
- **GPU**: Optional but highly recommended (NVIDIA CUDA-compatible GPU)

### Check Python Version

```bash
python3 --version
```

Should show Python 3.8 or higher.

## Installation Steps

### 1. Navigate to Project Directory

```bash
cd /Users/corneliusvanmollendorf/CascadeProjects/windsurf-project/ai-video-mimic
```

### 2. Install FFmpeg

FFmpeg is required for video processing.

**macOS (using Homebrew):**
```bash
brew install ffmpeg
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt-get update
sudo apt-get install ffmpeg
```

**Windows:**
Download from https://ffmpeg.org/download.html and add to PATH.

Verify installation:
```bash
ffmpeg -version
```

### 3. Create Virtual Environment

```bash
python3 -m venv venv
```

### 4. Activate Virtual Environment

**macOS/Linux:**
```bash
source venv/bin/activate
```

**Windows:**
```bash
venv\Scripts\activate
```

You should see `(venv)` in your terminal prompt.

### 5. Install Python Dependencies

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

This will take several minutes. The installation includes:
- PyTorch (deep learning framework)
- OpenCV (computer vision)
- Face alignment libraries
- Audio processing tools

### 6. Download Wav2Lip Model

```bash
python download_models.py
```

This downloads the pre-trained Wav2Lip model (~150MB). The model will be saved to `models/checkpoints/wav2lip_gan.pth`.

## Usage

### Option 1: Command Line Interface

Generate a video directly from the command line:

```bash
python generate_video.py \
  --image path/to/photo.jpg \
  --audio path/to/song.mp3 \
  --output result.mp4 \
  --quality medium \
  --fps 25
```

**Parameters:**
- `--image`: Path to your input image (required)
- `--audio`: Path to your audio file (required)
- `--output`: Output video filename (default: output.mp4)
- `--quality`: Video quality - low/medium/high (default: medium)
- `--fps`: Frames per second (default: 25)
- `--device`: cpu or cuda (auto-detected by default)

**Example:**
```bash
python generate_video.py \
  --image examples/person.jpg \
  --audio examples/song.mp3 \
  --output my_video.mp4
```

### Option 2: Web Interface

Start the web server:

```bash
python app.py
```

Then open your browser to:
```
http://localhost:5000
```

The web interface allows you to:
1. Upload an image (drag & drop or click)
2. Upload an audio file
3. Select quality and frame rate
4. Generate and download the video

## Testing Your Installation

### Quick Test

1. Create a test directory:
```bash
mkdir -p test_files
```

2. Add a test image and audio file to `test_files/`

3. Run generation:
```bash
python generate_video.py \
  --image test_files/test_image.jpg \
  --audio test_files/test_audio.mp3 \
  --output test_output.mp4
```

### Expected Processing Times

- **CPU Only**: ~1-2 minutes per 10 seconds of audio
- **With GPU (CUDA)**: ~10-20 seconds per 10 seconds of audio

## GPU Acceleration (Optional but Recommended)

### Check CUDA Availability

```bash
python -c "import torch; print('CUDA Available:', torch.cuda.is_available())"
```

If `False`, you're using CPU mode (slower but functional).

### Enable GPU (NVIDIA)

1. Install NVIDIA CUDA Toolkit: https://developer.nvidia.com/cuda-downloads
2. Install PyTorch with CUDA support:

```bash
pip uninstall torch torchvision
pip install torch torchvision --index-url https://download.pytorch.org/whl/cu118
```

Verify:
```bash
python -c "import torch; print('CUDA Available:', torch.cuda.is_available())"
```

## Troubleshooting

### Issue: "No module named 'cv2'"

**Solution:**
```bash
pip install opencv-python
```

### Issue: "No face detected in image"

**Solutions:**
- Ensure the image has a clear, frontal face
- Try a different image with better lighting
- Face should be at least 100x100 pixels

### Issue: "Model checkpoint not found"

**Solution:**
```bash
python download_models.py
```

### Issue: FFmpeg not found

**Solution:**
Install FFmpeg (see step 2 above) and ensure it's in your PATH:
```bash
which ffmpeg  # macOS/Linux
where ffmpeg  # Windows
```

### Issue: Out of memory error

**Solutions:**
- Use lower quality setting (`--quality low`)
- Process shorter audio clips
- Close other applications
- Use CPU mode if GPU memory is insufficient

### Issue: Slow processing on CPU

**Expected behavior** - CPU processing is significantly slower than GPU. Consider:
- Using shorter audio clips for testing
- Installing CUDA support for GPU acceleration
- Using `--quality low` for faster processing

## Advanced Configuration

### Batch Processing Multiple Files

Create a script to process multiple images with the same audio:

```bash
for img in images/*.jpg; do
  python generate_video.py \
    --image "$img" \
    --audio audio/song.mp3 \
    --output "outputs/$(basename "$img" .jpg).mp4"
done
```

### Custom Quality Settings

Edit `video_generator.py` to customize resolution and quality parameters.

### API Integration

The Flask app (`app.py`) provides REST API endpoints:

- `POST /upload` - Upload files and start processing
- `GET /status/<job_id>` - Check processing status
- `GET /download/<job_id>` - Download completed video
- `GET /health` - Check system health

## Best Practices

### Image Selection
- Use high-resolution images (at least 512x512)
- Frontal face view works best
- Good lighting and clear features
- Avoid heavy makeup or accessories covering the mouth

### Audio Selection
- Clear audio with minimal background noise
- Speech or singing works best
- Supported formats: MP3, WAV, M4A, OGG

### Performance Optimization
- Start with low quality for testing
- Use GPU acceleration when available
- Process shorter clips first to verify setup
- Close unnecessary applications during processing

## Getting Help

If you encounter issues:

1. Check the error message carefully
2. Verify all dependencies are installed
3. Ensure FFmpeg is accessible
4. Try with different input files
5. Check system resources (RAM, disk space)

## Next Steps

Once setup is complete:

1. Test with sample images and audio
2. Experiment with different quality settings
3. Try the web interface for easier usage
4. Integrate into your workflow or application

## Credits

This tool is built on:
- **Wav2Lip**: https://github.com/Rudrabha/Wav2Lip
- **PyTorch**: Deep learning framework
- **Face Alignment**: Face detection library
- **Librosa**: Audio processing

## License

MIT License - Free for personal and commercial use.
