#!/usr/bin/env python3
import argparse
import os
import sys
from pathlib import Path
import cv2
import numpy as np

from core.face_analyzer import FaceAnalyzer
from core.motion_generator import MotionGenerator
from core.avatar_animator import AvatarAnimator

def main():
    parser = argparse.ArgumentParser(description='Generate AI Avatar Animation')
    parser.add_argument('--image', type=str, required=True, help='Input image path')
    parser.add_argument('--audio', type=str, required=True, help='Input audio path')
    parser.add_argument('--output', type=str, default='avatar_output.mp4', help='Output video path')
    parser.add_argument('--style', type=str, default='natural', 
                       choices=['minimal', 'natural', 'expressive', 'dynamic'],
                       help='Animation style')
    parser.add_argument('--intensity', type=str, default='medium',
                       choices=['low', 'medium', 'high'],
                       help='Animation intensity')
    parser.add_argument('--fps', type=int, default=25, help='Output FPS')
    parser.add_argument('--device', type=str, default='cpu',
                       choices=['cpu', 'cuda'], help='Device to use')
    
    args = parser.parse_args()
    
    if not os.path.exists(args.image):
        print(f"Error: Image file not found: {args.image}")
        sys.exit(1)
    
    if not os.path.exists(args.audio):
        print(f"Error: Audio file not found: {args.audio}")
        sys.exit(1)
    
    print("="*60)
    print("AI Avatar Studio - Avatar Generation")
    print("="*60)
    print(f"Image: {args.image}")
    print(f"Audio: {args.audio}")
    print(f"Style: {args.style}")
    print(f"Output: {args.output}")
    print("="*60)
    
    print("\n1. Analyzing face...")
    face_analyzer = FaceAnalyzer(device=args.device)
    
    image = cv2.imread(args.image)
    image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    
    try:
        landmarks_3d, bbox = face_analyzer.detect_face_3d(image_rgb)
        print(f"   ✓ Face detected at {bbox}")
        
        initial_pose = face_analyzer.estimate_head_pose(landmarks_3d)
        print(f"   ✓ Initial pose: pitch={initial_pose['pitch']:.1f}°, "
              f"yaw={initial_pose['yaw']:.1f}°, roll={initial_pose['roll']:.1f}°")
        
        face_crop, transform = face_analyzer.extract_face_region(image_rgb, bbox, (512, 512))
        print(f"   ✓ Face extracted: {face_crop.shape}")
        
    except Exception as e:
        print(f"   ✗ Error: {e}")
        sys.exit(1)
    
    print("\n2. Analyzing audio...")
    motion_gen = MotionGenerator()
    
    try:
        audio_features = motion_gen.analyze_audio(args.audio)
        print(f"   ✓ Audio duration: {audio_features['duration']:.2f}s")
        print(f"   ✓ Tempo: {audio_features['tempo']:.1f} BPM")
    except Exception as e:
        print(f"   ✗ Error: {e}")
        sys.exit(1)
    
    print("\n3. Generating motion sequence...")
    try:
        motion_sequence = motion_gen.generate_head_motion(
            audio_features, 
            fps=args.fps, 
            style=args.style
        )
        print(f"   ✓ Generated {len(motion_sequence)} motion frames")
        
        motion_sequence = motion_gen.smooth_motion(motion_sequence, window_size=5)
        print(f"   ✓ Motion smoothed")
        
    except Exception as e:
        print(f"   ✗ Error: {e}")
        sys.exit(1)
    
    print("\n4. Generating expression sequence...")
    try:
        expression_sequence = motion_gen.generate_expression_sequence(
            audio_features,
            fps=args.fps,
            style=args.style
        )
        print(f"   ✓ Generated {len(expression_sequence)} expression frames")
    except Exception as e:
        print(f"   ✗ Error: {e}")
        sys.exit(1)
    
    print("\n5. Animating avatar...")
    animator = AvatarAnimator(device=args.device)
    
    try:
        temp_video = animator.generate_avatar_video(
            face_crop,
            landmarks_3d,
            motion_sequence,
            expression_sequence,
            args.output,
            fps=args.fps
        )
        print(f"   ✓ Animation complete")
    except Exception as e:
        print(f"   ✗ Error: {e}")
        sys.exit(1)
    
    print("\n6. Adding audio...")
    try:
        command = f'ffmpeg -y -i {temp_video} -i {args.audio} -c:v libx264 -c:a aac -strict experimental -shortest {args.output}'
        os.system(command)
        
        if os.path.exists(temp_video):
            os.remove(temp_video)
        
        print(f"   ✓ Audio added")
    except Exception as e:
        print(f"   ✗ Error: {e}")
        sys.exit(1)
    
    print("\n" + "="*60)
    print("✓ Avatar generation complete!")
    print(f"Output: {args.output}")
    print("="*60)

if __name__ == "__main__":
    main()
