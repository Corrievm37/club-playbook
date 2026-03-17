#!/usr/bin/env python3
import cv2
import numpy as np
from video_generator import VideoGenerator

# Test with the uploaded image
image_path = 'uploads/39e22988-9d90-4394-87df-7cbe273502b3_image.jpeg'
audio_path = 'test_audio.wav'  # Use our test audio
output_path = 'test_output_debug.mp4'

print("Testing video generation...")
print(f"Image: {image_path}")
print(f"Audio: {audio_path}")

try:
    generator = VideoGenerator('models/checkpoints/wav2lip_gan.pth', device='cpu')
    
    # Test face detection
    print("\n1. Testing face detection...")
    face, original_image, bbox = generator.face_detector.prepare_face_for_model(image_path, target_size=(96, 96))
    print(f"   Face detected: shape {face.shape}")
    print(f"   Face value range: [{face.min()}, {face.max()}]")
    print(f"   BBox: {bbox}")
    
    # Save detected face for inspection
    cv2.imwrite('debug_face.jpg', cv2.cvtColor(face, cv2.COLOR_RGB2BGR))
    print("   Saved debug_face.jpg")
    
    # Test preprocessing
    print("\n2. Testing preprocessing...")
    preprocessed = generator.preprocess_image(face)
    print(f"   Preprocessed shape: {preprocessed.shape}")
    print(f"   Preprocessed range: [{preprocessed.min():.3f}, {preprocessed.max():.3f}]")
    
    # Test audio processing
    print("\n3. Testing audio processing...")
    mel_chunks, audio = generator.audio_processor.prepare_audio_windows(audio_path, fps=25)
    print(f"   Mel chunks: {len(mel_chunks)}")
    print(f"   First chunk shape: {mel_chunks[0].shape}")
    
    # Test single batch generation
    print("\n4. Testing model inference...")
    img_batch = np.array([preprocessed])
    mel_batch = np.array([mel_chunks[0].T])
    
    img_masked = img_batch.copy()
    img_masked[:, 48:] = 0
    
    img_batch_final = np.concatenate((img_masked, img_batch), axis=3) / 255.
    mel_batch_final = np.expand_dims(mel_batch, axis=1)
    
    print(f"   Image batch shape: {img_batch_final.shape}")
    print(f"   Mel batch shape: {mel_batch_final.shape}")
    
    frames = generator._generate_batch(img_batch_final, mel_batch_final)
    print(f"   Output frames shape: {frames.shape}")
    print(f"   Output range: [{frames.min():.3f}, {frames.max():.3f}]")
    
    # Save first frame
    if frames.shape[0] > 0:
        first_frame = frames[0]
        print(f"   First frame shape: {first_frame.shape}")
        print(f"   First frame range: [{first_frame.min():.3f}, {first_frame.max():.3f}]")
        
        # Clip and convert
        first_frame_uint8 = np.clip(first_frame, 0, 255).astype(np.uint8)
        cv2.imwrite('debug_output_frame.jpg', cv2.cvtColor(first_frame_uint8, cv2.COLOR_RGB2BGR))
        print("   Saved debug_output_frame.jpg")
        
        # Check if frame is all black
        if first_frame_uint8.max() == 0:
            print("   WARNING: Frame is completely black!")
        elif first_frame_uint8.max() < 10:
            print("   WARNING: Frame is very dark!")
    
    print("\n✓ Debug test complete")
    
except Exception as e:
    print(f"\n✗ Error: {e}")
    import traceback
    traceback.print_exc()
