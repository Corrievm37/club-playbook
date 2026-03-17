#!/usr/bin/env python3
"""Test D-ID API directly to debug the issue"""
import requests
import json

api_key = "c2V1bkBpbmRvb3Jjcmlja2V0c2EuY28uemE:aKOc5GLjTTAzTY0hfdm39"

# Test 1: Check credits
print("Testing D-ID API...")
print("\n1. Checking credits...")
response = requests.get(
    "https://api.d-id.com/credits",
    headers={"Authorization": f"Basic {api_key}"}
)
print(f"Status: {response.status_code}")
print(f"Response: {response.text}")

# Test 2: Try simple talk creation with external URLs
print("\n2. Testing with external URLs...")
payload = {
    "source_url": "https://create-images-results.d-id.com/DefaultPresenters/Noelle_f/image.jpeg",
    "script": {
        "type": "text",
        "input": "Hello, this is a test."
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
