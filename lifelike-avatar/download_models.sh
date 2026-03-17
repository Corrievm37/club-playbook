#!/bin/bash

echo "Downloading AI models for lifelike avatar generation..."

mkdir -p models/checkpoints
mkdir -p models/gfpgan
mkdir -p models/realesrgan

cd models/checkpoints

# Download Wav2Lip model
echo "Downloading Wav2Lip model..."
wget -O wav2lip_gan.pth "https://github.com/justinjohn0306/Wav2Lip/releases/download/models/wav2lip_gan.pth" || \
curl -L -o wav2lip_gan.pth "https://github.com/justinjohn0306/Wav2Lip/releases/download/models/wav2lip_gan.pth"

# Download face detection model
echo "Downloading face detection model..."
wget -O s3fd.pth "https://www.adrianbulat.com/downloads/python-fan/s3fd-619a316812.pth" || \
curl -L -o s3fd.pth "https://www.adrianbulat.com/downloads/python-fan/s3fd-619a316812.pth"

cd ../gfpgan

# Download GFPGAN model
echo "Downloading GFPGAN enhancement model..."
wget -O GFPGANv1.4.pth "https://github.com/TencentARC/GFPGAN/releases/download/v1.3.0/GFPGANv1.4.pth" || \
curl -L -o GFPGANv1.4.pth "https://github.com/TencentARC/GFPGAN/releases/download/v1.3.0/GFPGANv1.4.pth"

cd ../realesrgan

# Download Real-ESRGAN model
echo "Downloading Real-ESRGAN upscaling model..."
wget -O RealESRGAN_x4plus.pth "https://github.com/xinntao/Real-ESRGAN/releases/download/v0.1.0/RealESRGAN_x4plus.pth" || \
curl -L -o RealESRGAN_x4plus.pth "https://github.com/xinntao/Real-ESRGAN/releases/download/v0.1.0/RealESRGAN_x4plus.pth"

cd ../..

echo ""
echo "✓ All models downloaded successfully!"
echo ""
echo "Models location:"
echo "  - Wav2Lip: models/checkpoints/wav2lip_gan.pth"
echo "  - Face Detection: models/checkpoints/s3fd.pth"
echo "  - GFPGAN: models/gfpgan/GFPGANv1.4.pth"
echo "  - Real-ESRGAN: models/realesrgan/RealESRGAN_x4plus.pth"
echo ""
