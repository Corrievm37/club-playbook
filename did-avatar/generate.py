#!/usr/bin/env python3
import argparse
import os
import sys
from dotenv import load_dotenv
from did_api import DIDClient

load_dotenv()

def main():
    parser = argparse.ArgumentParser(description='Generate lifelike AI avatar with D-ID')
    parser.add_argument('--image', type=str, required=True, help='Input image path')
    parser.add_argument('--audio', type=str, required=True, help='Input audio path')
    parser.add_argument('--output', type=str, default='avatar_output.mp4', help='Output video path')
    parser.add_argument('--api-key', type=str, help='D-ID API key (or set DID_API_KEY env var)')
    
    args = parser.parse_args()
    
    api_key = args.api_key or os.getenv('DID_API_KEY')
    
    if not api_key:
        print("Error: D-ID API key required")
        print("\nOptions:")
        print("  1. Set DID_API_KEY environment variable")
        print("  2. Create .env file with DID_API_KEY=your_key")
        print("  3. Use --api-key argument")
        print("\nGet your API key at: https://studio.d-id.com/")
        sys.exit(1)
    
    if not os.path.exists(args.image):
        print(f"Error: Image file not found: {args.image}")
        sys.exit(1)
    
    if not os.path.exists(args.audio):
        print(f"Error: Audio file not found: {args.audio}")
        sys.exit(1)
    
    print("="*60)
    print("D-ID AI Avatar Generator - Professional Quality")
    print("="*60)
    print(f"Image: {args.image}")
    print(f"Audio: {args.audio}")
    print(f"Output: {args.output}")
    print("="*60)
    print()
    
    try:
        client = DIDClient(api_key)
        client.generate_avatar(args.image, args.audio, args.output)
        
        print()
        print("="*60)
        print("✓ Lifelike avatar generated successfully!")
        print(f"Output: {args.output}")
        print("="*60)
        
    except Exception as e:
        print(f"\n✗ Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
