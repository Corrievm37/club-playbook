# D-ID Avatar Generator - Setup Guide

## Quick Setup (5 minutes)

### Step 1: Get D-ID API Key

1. Visit https://studio.d-id.com/
2. Sign up for a free account
3. Click on your profile → **API Keys**
4. Click **Create API Key**
5. Copy the key (starts with `Basic ...`)

**Free Trial:** D-ID usually provides 20 free credits to test (~20 videos)

### Step 2: Configure API Key

Create a `.env` file in the `did-avatar` folder:

```bash
cd did-avatar
nano .env
```

Add your API key:
```
DID_API_KEY=Basic_your_api_key_here
```

Save and exit (Ctrl+X, Y, Enter)

### Step 3: Launch

```bash
source venv/bin/activate
python app.py
```

Open http://localhost:5004

## Usage

1. **Upload your skull character image**
2. **Upload your audio file**
3. **Click "Generate Lifelike Avatar"**
4. **Wait for D-ID to process** (usually 30-60 seconds)
5. **Download your professional result**

## What You Get

✅ **Real AI-generated video** - Not image warping
✅ **Lifelike facial animations** - Natural movements
✅ **Perfect lip-sync** - Audio-visual synchronization
✅ **Professional quality** - Industry-standard results
✅ **Works with any image** - Including non-human characters

## Pricing

- **Free Trial**: ~20 videos
- **Pay-as-you-go**: $0.05-0.20 per video
- **Monthly Plans**: Available for bulk usage

## Troubleshooting

### "API key not configured"
- Make sure `.env` file exists in `did-avatar` folder
- Check that `DID_API_KEY=` has your actual key
- Restart the server after adding the key

### "Authentication failed"
- Verify your API key is correct
- Make sure it starts with `Basic `
- Check your D-ID account is active

### "Insufficient credits"
- Add credits to your D-ID account
- Or use free trial credits if available

## Command Line Usage

```bash
python generate.py \
  --image /path/to/skull_character.jpg \
  --audio /path/to/dialogue.mp3 \
  --output result.mp4
```

## Why D-ID?

This is **professional AI video generation**, not amateur image manipulation:
- Used by major companies
- State-of-the-art neural rendering
- Truly lifelike results
- Reliable and fast
- Worth the small cost per video
