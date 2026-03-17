#!/usr/bin/env python3
import numpy as np
from scipy.io import wavfile
import sys

# Create a simple test audio file (3 seconds of sine wave)
sample_rate = 16000
duration = 3
t = np.linspace(0, duration, int(sample_rate * duration))
frequency = 440  # A4 note
audio = np.sin(2 * np.pi * frequency * t) * 0.3

# Save as WAV
wavfile.write('test_audio.wav', sample_rate, (audio * 32767).astype(np.int16))
print("Created test_audio.wav")

# Now test the audio processor
from audio_processor import AudioProcessor

processor = AudioProcessor()
mel_chunks, audio_data = processor.prepare_audio_windows('test_audio.wav', fps=25)

print(f"\nAudio processing results:")
print(f"Number of chunks: {len(mel_chunks)}")
print(f"First chunk shape: {mel_chunks[0].shape}")
print(f"Expected shape: (16, 80)")

# Check all chunks
for i, chunk in enumerate(mel_chunks):
    print(f"Chunk {i}: shape {chunk.shape}")
    if chunk.shape != (16, 80):
        print(f"  ERROR: Wrong shape!")
