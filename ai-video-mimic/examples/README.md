# Example Files

Place your test images and audio files in this directory.

## Recommended Test Files

### Images
- Portrait photos with clear, frontal faces
- Resolution: 512x512 or higher
- Formats: JPG, PNG

### Audio
- Speech or singing recordings
- Duration: 10-30 seconds for initial testing
- Formats: MP3, WAV, M4A

## Quick Test

1. Add a test image: `examples/test_image.jpg`
2. Add a test audio: `examples/test_audio.mp3`
3. Run:

```bash
python generate_video.py \
  --image examples/test_image.jpg \
  --audio examples/test_audio.mp3 \
  --output examples/output.mp4
```

## Sample Sources

You can find free test resources at:
- **Images**: https://unsplash.com (search for "portrait")
- **Audio**: https://freesound.org or record your own voice

## Tips

- Start with shorter audio clips (10-15 seconds)
- Use clear, high-quality images
- Ensure faces are well-lit and unobstructed
