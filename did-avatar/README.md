# D-ID AI Avatar Generator

Professional lifelike talking avatars using D-ID's AI video generation API.

## Features

✅ **Professional Quality** - Industry-leading lifelike results
✅ **Works with Any Image** - Photos, artwork, avatars, characters (including your skull character)
✅ **Natural Animations** - Realistic facial movements and expressions
✅ **Perfect Lip-Sync** - Audio-driven mouth movements
✅ **Fast Generation** - Cloud-based processing
✅ **Cost Effective** - Pay per video (~$0.05-0.20 each)

## Setup

### 1. Get D-ID API Key

1. Go to https://studio.d-id.com/
2. Sign up for an account
3. Navigate to API section
4. Copy your API key
5. Paste it in `.env` file (see below)

### 2. Install

```bash
cd did-avatar
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

### 3. Configure API Key

Create a `.env` file:
```
DID_API_KEY=your_api_key_here
```

## Usage

### Web Interface
```bash
python app.py
# Open http://localhost:5004
```

### Command Line
```bash
python generate.py \
  --image character.jpg \
  --audio speech.mp3 \
  --output result.mp4
```

## Pricing

D-ID charges per video generated:
- **Basic**: ~$0.05 per video (standard quality)
- **Premium**: ~$0.20 per video (HD quality)
- **Free Trial**: Usually 20 credits to test

## How It Works

1. Upload your image and audio
2. API sends to D-ID cloud
3. AI generates lifelike talking video
4. Download professional result

**This is real AI generation, not image warping.**
