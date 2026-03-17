import os
import urllib.request
import sys
from pathlib import Path

def download_file(url, destination):
    """Download file with progress bar"""
    print(f"Downloading from: {url}")
    print(f"Saving to: {destination}\n")
    
    def reporthook(count, block_size, total_size):
        if total_size > 0:
            percent = int(count * block_size * 100 / total_size)
            sys.stdout.write(f"\rProgress: {percent}%")
            sys.stdout.flush()
    
    urllib.request.urlretrieve(url, destination, reporthook)
    print("\nDownload complete!")

def main():
    models_dir = Path("models")
    models_dir.mkdir(exist_ok=True)
    
    checkpoints_dir = models_dir / "checkpoints"
    checkpoints_dir.mkdir(exist_ok=True)
    
    print("=" * 60)
    print("Wav2Lip Model Downloader")
    print("=" * 60)
    
    wav2lip_model = checkpoints_dir / "wav2lip_gan.pth"
    
    if wav2lip_model.exists():
        print(f"\n✓ Model already exists at {wav2lip_model}")
        print("Skipping download.")
        return
    
    print("\nDownloading Wav2Lip GAN model (~150MB)...")
    print("This may take several minutes depending on your connection.\n")
    
    # Multiple mirror URLs to try
    model_urls = [
        "https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1",
        "https://github.com/justinjohn0306/Wav2Lip/releases/download/models/wav2lip_gan.pth",
        "https://www.adrianbulat.com/downloads/python-fan/s3fd-619a316812.pth"
    ]
    
    success = False
    for i, model_url in enumerate(model_urls):
        try:
            print(f"Attempt {i+1}/{len(model_urls)}...")
            download_file(model_url, str(wav2lip_model))
            
            # Verify file was downloaded and has reasonable size
            if wav2lip_model.exists() and wav2lip_model.stat().st_size > 1000000:
                print(f"\n✓ Model downloaded successfully to {wav2lip_model}")
                print(f"File size: {wav2lip_model.stat().st_size / (1024*1024):.1f} MB")
                print("\nYou're ready to generate videos!")
                success = True
                break
            else:
                print("\n⚠ Downloaded file seems invalid, trying next URL...")
                if wav2lip_model.exists():
                    wav2lip_model.unlink()
        except Exception as e:
            print(f"\n✗ Error: {e}")
            if i < len(model_urls) - 1:
                print("Trying alternative download source...\n")
            continue
    
    if not success:
        print("\n" + "=" * 60)
        print("MANUAL DOWNLOAD REQUIRED")
        print("=" * 60)
        print("\nAutomatic download failed. Please download manually:")
        print("\nOption 1: Direct download")
        print("  Visit: https://github.com/Rudrabha/Wav2Lip")
        print("  Go to 'Releases' and download wav2lip_gan.pth")
        print(f"\nOption 2: Use wget/curl")
        print(f"  wget -O {wav2lip_model} 'https://iiitaphyd-my.sharepoint.com/:u:/g/personal/radrabha_m_research_iiit_ac_in/Eb3LEzbfuKlJiR600lQWRxgBIY27JZg80f7V9jtMfbNDaQ?download=1'")
        print(f"\nOption 3: Google Drive")
        print("  Search for 'Wav2Lip pretrained models' on Google Drive")
        print(f"\nPlace the downloaded file at:")
        print(f"  {wav2lip_model}")
        print("\n" + "=" * 60)
        sys.exit(1)

if __name__ == "__main__":
    main()
