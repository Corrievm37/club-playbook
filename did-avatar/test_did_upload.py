#!/usr/bin/env python3
"""Test D-ID API with proper file upload"""
import requests

api_key = "c2V1bkBpbmRvb3Jjcmlja2V0c2EuY28uemE:aKOc5GLjTTAzTY0hfdm39"

# Test uploading image first
print("1. Uploading image...")
with open('uploads/img_2cc0ff4a.jpeg', 'rb') as f:
    response = requests.post(
        "https://api.d-id.com/images",
        headers={"Authorization": f"Basic {api_key}"},
        files={'image': ('image.jpeg', f, 'image/jpeg')}
    )
print(f"Status: {response.status_code}")
print(f"Response: {response.text}\n")

if response.status_code == 201:
    image_url = response.json()['url']
    print(f"Image URL: {image_url}\n")
    
    # Now create talk with text (not audio first)
    print("2. Creating talk with text...")
    payload = {
        "source_url": image_url,
        "script": {
            "type": "text",
            "input": "Hello, this is a test of the D-ID API."
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
