#!/usr/bin/env python3
"""Extract audio from video files"""
import argparse
import os
from moviepy.editor import VideoFileClip

def extract_audio(video_path, output_path=None, format='mp3'):
    """
    Extract audio from video file
    
    Args:
        video_path: Path to video file
        output_path: Path for output audio (optional)
        format: Audio format (mp3, wav, etc.)
    
    Returns:
        output_path: Path to extracted audio
    """
    if not os.path.exists(video_path):
        raise FileNotFoundError(f"Video file not found: {video_path}")
    
    if output_path is None:
        base = os.path.splitext(video_path)[0]
        output_path = f"{base}_audio.{format}"
    
    print(f"Extracting audio from: {video_path}")
    
    video = VideoFileClip(video_path)
    
    if video.audio is None:
        raise ValueError("Video has no audio track")
    
    video.audio.write_audiofile(output_path, logger=None)
    
    duration = video.duration
    video.close()
    
    print(f"✓ Audio extracted: {output_path}")
    print(f"  Duration: {duration:.1f}s")
    print(f"  Format: {format}")
    
    return output_path

def main():
    parser = argparse.ArgumentParser(description='Extract audio from video')
    parser.add_argument('--video', type=str, required=True, help='Input video file')
    parser.add_argument('--output', type=str, help='Output audio file')
    parser.add_argument('--format', type=str, default='mp3', choices=['mp3', 'wav', 'aac'],
                       help='Audio format')
    
    args = parser.parse_args()
    
    try:
        extract_audio(args.video, args.output, args.format)
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())
