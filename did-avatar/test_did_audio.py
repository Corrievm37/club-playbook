#!/usr/bin/env python3
"""Test D-ID API with audio upload"""
import requests
from pydub import AudioSegment
import os

api_key = "c2V1bkBpbmRvb3Jjcmlja2V0c2EuY28uemE:aKOc5GLjTTAzTY0hfdm39"

# Compress audio first
audio_path = 'uploads/aud_a0e5977f.mp3'
print(f"Original audio size: {os.path.getsize(audio_path) / (1024*1024):.1f}MB")

audio = AudioSegment.from_file(audio_path)
compressed_path = 'test_compressed.mp3'
audio.export(compressed_path, format="mp3", bitrate="64k", parameters=["-ac", "1"])
print(f"Compressed audio size: {os.path.getsize(compressed_path) / (1024*1024):.1f}MB\n")

# Upload image
print("1. Uploading image...")
with open('uploads/img_2cc0ff4a.jpeg', 'rb') as f:
    response = requests.post(
        "https://api.d-id.com/images",
        headers={"Authorization": f"Basic {api_key}"},
        files={'image': ('image.jpeg', f, 'image/jpeg')}
    )
print(f"Status: {response.status_code}")
if response.status_code == 201:
    image_url = response.json()['url']
    print(f"Image URL: {image_url}\n")
    
    # Upload audio
    print("2. Uploading audio...")
    with open(compressed_path, 'rb') as f:
        response = requests.post(
            "https://api.d-id.com/audios",
            headers={"Authorization": f"Basic {api_key}"},
            files={'audio': ('audio.mp3', f, 'audio/mpeg')}
        )
    print(f"Status: {response.status_code}")
    print(f"Response: {response.text}\n")
    
    if response.status_code == 201:
        audio_url = response.json()['url']
        print(f"Audio URL: {audio_url}\n")
        
        # Create talk with audio
        print("3. Creating talk with audio...")
        payload = {
            "source_url": image_url,
            "script": {
                "type": "audio",
                "audio_url": audio_url
            }
        }
        
        response = requests.post(
            "https://api.d-id.com/talks",
            headers={
                "Authorization": f"Basic {api_key}",
                "Content-Type": "application/json"
            },
            json=payload
        )
        print(f"Status: {response.status_code}")
        print(f"Response: {response.text}")

os.remove(compressed_path)
