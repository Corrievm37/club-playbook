#!/bin/bash

# Wav2Lip Model Download Script
# This script downloads the Wav2Lip GAN model using curl

set -e

MODEL_DIR="models/checkpoints"
MODEL_FILE="$MODEL_DIR/wav2lip_gan.pth"

echo "======================================================================"
echo "Wav2Lip Model Download Script"
echo "======================================================================"

# Create directory if it doesn't exist
mkdir -p "$MODEL_DIR"

# Check if model already exists
if [ -f "$MODEL_FILE" ]; then
    echo ""
    echo "✓ Model already exists at $MODEL_FILE"
    FILE_SIZE=$(du -h "$MODEL_FILE" | cut -f1)
    echo "  File size: $FILE_SIZE"
    echo ""
    echo "Delete the file if you want to re-download."
    exit 0
fi

echo ""
echo "Downloading Wav2Lip GAN model (~150MB)..."
echo "This may take several minutes depending on your connection."
echo ""

# Try primary URL (OneDrive/SharePoint)
echo "Attempting download from primary source..."
if curl -L -o "$MODEL_FILE" \
    'https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1' \
    --progress-bar; then
    
    # Verify download
    if [ -f "$MODEL_FILE" ]; then
        FILE_SIZE=$(stat -f%z "$MODEL_FILE" 2>/dev/null || stat -c%s "$MODEL_FILE" 2>/dev/null)
        
        if [ "$FILE_SIZE" -gt 1000000 ]; then
            echo ""
            echo "======================================================================"
            echo "✓ Model downloaded successfully!"
            echo "======================================================================"
            echo "Location: $MODEL_FILE"
            echo "Size: $(du -h "$MODEL_FILE" | cut -f1)"
            echo ""
            echo "You're ready to generate videos!"
            echo "Run: python generate_video.py --image your_image.jpg --audio your_audio.mp3"
            echo "======================================================================"
            exit 0
        else
            echo "⚠ Downloaded file is too small, trying alternative source..."
            rm -f "$MODEL_FILE"
        fi
    fi
fi

# Try alternative URL
echo ""
echo "Attempting download from alternative source..."
if curl -L -o "$MODEL_FILE" \
    'https://github.com/justinjohn0306/Wav2Lip/releases/download/models/wav2lip_gan.pth' \
    --progress-bar; then
    
    if [ -f "$MODEL_FILE" ]; then
        FILE_SIZE=$(stat -f%z "$MODEL_FILE" 2>/dev/null || stat -c%s "$MODEL_FILE" 2>/dev/null)
        
        if [ "$FILE_SIZE" -gt 1000000 ]; then
            echo ""
            echo "======================================================================"
            echo "✓ Model downloaded successfully!"
            echo "======================================================================"
            echo "Location: $MODEL_FILE"
            echo "Size: $(du -h "$MODEL_FILE" | cut -f1)"
            echo ""
            echo "You're ready to generate videos!"
            echo "======================================================================"
            exit 0
        fi
    fi
fi

# If all downloads failed
echo ""
echo "======================================================================"
echo "MANUAL DOWNLOAD REQUIRED"
echo "======================================================================"
echo ""
echo "Automatic download failed. Please try one of these options:"
echo ""
echo "Option 1: Download via browser"
echo "  1. Visit: https://github.com/Rudrabha/Wav2Lip"
echo "  2. Click on 'Releases'"
echo "  3. Download 'wav2lip_gan.pth'"
echo "  4. Move it to: $MODEL_FILE"
echo ""
echo "Option 2: Use wget"
echo "  wget -O '$MODEL_FILE' 'https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1'"
echo ""
echo "Option 3: Google Drive"
echo "  Search for 'Wav2Lip pretrained models' and download from Google Drive"
echo ""
echo "======================================================================"
exit 1
