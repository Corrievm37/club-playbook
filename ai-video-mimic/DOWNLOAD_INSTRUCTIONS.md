# Model Download Instructions

The Wav2Lip model is required to run the video generation. Here are multiple ways to download it.

## Method 1: Automatic Download (Recommended)

Try the updated Python script with multiple mirror URLs:

```bash
python download_models.py
```

## Method 2: Shell Script (macOS/Linux)

Use the bash script which uses `curl`:

```bash
chmod +x download_model_manual.sh
./download_model_manual.sh
```

## Method 3: Manual Download via Browser

1. **Visit the official repository:**
   - Go to: https://github.com/Rudrabha/Wav2Lip
   - Click on "Releases" or check the README for model links

2. **Alternative sources:**
   - OneDrive/SharePoint: [Click here](https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1)
   - Google Drive: Search for "Wav2Lip pretrained models"

3. **Place the file:**
   - Download `wav2lip_gan.pth` (~150MB)
   - Move it to: `models/checkpoints/wav2lip_gan.pth`

## Method 4: Command Line (wget)

```bash
mkdir -p models/checkpoints
wget -O models/checkpoints/wav2lip_gan.pth \
  'https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1'
```

## Method 5: Command Line (curl)

```bash
mkdir -p models/checkpoints
curl -L -o models/checkpoints/wav2lip_gan.pth \
  'https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1'
```

## Verify Download

After downloading, verify the file:

```bash
ls -lh models/checkpoints/wav2lip_gan.pth
```

The file should be approximately 150MB. If it's much smaller, the download may have failed.

## Troubleshooting

### File is too small (< 1MB)
The download likely failed or downloaded an HTML error page. Try a different method.

### Download keeps failing
- Check your internet connection
- Try using a VPN if the source is blocked
- Download from an alternative mirror
- Ask in the Wav2Lip GitHub issues for current download links

### Permission denied
```bash
chmod +x download_model_manual.sh
```

### Directory doesn't exist
```bash
mkdir -p models/checkpoints
```

## Alternative Models

If you can't download `wav2lip_gan.pth`, you can also try:
- `wav2lip.pth` (non-GAN version, slightly lower quality)

Place any alternative model in `models/checkpoints/` and update the model path in the code.

## Need Help?

If all methods fail, please:
1. Check the official Wav2Lip repository for updated links
2. Open an issue on the Wav2Lip GitHub
3. Search for "Wav2Lip model download" for community mirrors
