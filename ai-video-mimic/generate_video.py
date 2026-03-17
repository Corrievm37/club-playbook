#!/usr/bin/env python3
import argparse
import os
import sys
from pathlib import Path
import torch

def check_dependencies():
    """Check if all required dependencies are installed"""
    try:
        import cv2
        import numpy
        import librosa
        import face_alignment
        print("✓ All dependencies installed")
        return True
    except ImportError as e:
        print(f"✗ Missing dependency: {e}")
        print("\nPlease install requirements:")
        print("  pip install -r requirements.txt")
        return False

def check_model():
    """Check if Wav2Lip model is downloaded"""
    model_path = Path("models/checkpoints/wav2lip_gan.pth")
    if not model_path.exists():
        print("✗ Wav2Lip model not found")
        print("\nPlease download the model:")
        print("  python download_models.py")
        return False
    print(f"✓ Model found at {model_path}")
    return True

def main():
    parser = argparse.ArgumentParser(description='Generate lip-synced video from image and audio')
    parser.add_argument('--image', type=str, required=True, help='Path to input image')
    parser.add_argument('--audio', type=str, required=True, help='Path to audio file')
    parser.add_argument('--output', type=str, default='output.mp4', help='Output video path')
    parser.add_argument('--quality', type=str, default='medium', 
                       choices=['low', 'medium', 'high'], help='Video quality')
    parser.add_argument('--fps', type=int, default=25, help='Frames per second')
    parser.add_argument('--device', type=str, default='cpu', 
                       choices=['cpu', 'cuda'], help='Device to use for inference')
    
    args = parser.parse_args()
    
    if not check_dependencies():
        sys.exit(1)
    
    if not check_model():
        sys.exit(1)
    
    if not os.path.exists(args.image):
        print(f"✗ Image file not found: {args.image}")
        sys.exit(1)
    
    if not os.path.exists(args.audio):
        print(f"✗ Audio file not found: {args.audio}")
        sys.exit(1)
    
    if args.device == 'cuda' and not torch.cuda.is_available():
        print("⚠ CUDA not available, falling back to CPU")
        args.device = 'cpu'
    
    from video_generator import VideoGenerator
    
    model_path = "models/checkpoints/wav2lip_gan.pth"
    generator = VideoGenerator(model_path, device=args.device)
    
    try:
        generator.create_video_file(
            args.image,
            args.audio,
            args.output,
            fps=args.fps,
            quality=args.quality
        )
    except Exception as e:
        print(f"\n✗ Error during video generation: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)

if __name__ == "__main__":
    main()
