import requests
import time
import os
from pathlib import Path
from pydub import AudioSegment

class DIDClient:
    """D-ID API client for generating lifelike talking avatars"""
    
    def __init__(self, api_key):
        self.api_key = api_key
        self.base_url = "https://api.d-id.com"
        self.headers = {
            "Authorization": f"Basic {api_key}",
            "Content-Type": "application/json"
        }
    
    def upload_image(self, image_path):
        """
        Upload image to D-ID
        
        Args:
            image_path: Path to image file
            
        Returns:
            image_url: URL of uploaded image
        """
        url = f"{self.base_url}/images"
        
        with open(image_path, 'rb') as f:
            files = {'image': f}
            response = requests.post(
                url,
                headers={"Authorization": f"Basic {self.api_key}"},
                files=files
            )
        
        if response.status_code == 201:
            return response.json()['url']
        else:
            raise Exception(f"Image upload failed: {response.text}")
    
    def compress_audio(self, audio_path, max_size_mb=5):
        """
        Compress audio file if it's too large
        
        Args:
            audio_path: Path to audio file
            max_size_mb: Maximum size in MB
            
        Returns:
            compressed_path: Path to compressed audio
        """
        file_size_mb = os.path.getsize(audio_path) / (1024 * 1024)
        
        if file_size_mb <= max_size_mb:
            return audio_path
        
        print(f"Compressing audio ({file_size_mb:.1f}MB -> target: {max_size_mb}MB)...")
        
        audio = AudioSegment.from_file(audio_path)
        
        bitrate = "64k"
        if file_size_mb > 10:
            bitrate = "32k"
        
        compressed_path = audio_path.replace('.mp3', '_compressed.mp3').replace('.wav', '_compressed.wav')
        if compressed_path == audio_path:
            compressed_path = audio_path + '_compressed.mp3'
        
        audio.export(
            compressed_path,
            format="mp3",
            bitrate=bitrate,
            parameters=["-ac", "1"]
        )
        
        new_size_mb = os.path.getsize(compressed_path) / (1024 * 1024)
        print(f"Audio compressed: {new_size_mb:.1f}MB")
        
        return compressed_path
    
    def upload_audio(self, audio_path):
        """
        Upload audio to D-ID (with automatic compression if needed)
        
        Args:
            audio_path: Path to audio file
            
        Returns:
            audio_url: URL of uploaded audio, duration in seconds
        """
        compressed_path = self.compress_audio(audio_path)
        
        url = f"{self.base_url}/audios"
        
        with open(compressed_path, 'rb') as f:
            files = {'audio': f}
            response = requests.post(
                url,
                headers={"Authorization": f"Basic {self.api_key}"},
                files=files
            )
        
        if compressed_path != audio_path and os.path.exists(compressed_path):
            os.remove(compressed_path)
        
        if response.status_code == 201:
            data = response.json()
            duration = data.get('duration', 0)
            
            # Warn about credits needed
            credits_needed = int(duration / 10) + 1
            print(f"⚠️  Audio duration: {duration:.1f}s (~{credits_needed} credits needed)")
            
            return data['url']
        else:
            raise Exception(f"Audio upload failed: {response.text}")
    
    def create_talk_with_files(self, image_path, audio_path, config=None):
        """
        Create talking video using proper D-ID upload workflow
        
        Args:
            image_path: Path to source image file
            audio_path: Path to audio file
            config: Optional configuration dict
            
        Returns:
            talk_id: ID of the talk job
        """
        # Upload image
        print("Uploading image to D-ID...")
        image_url = self.upload_image(image_path)
        
        # Upload audio
        print("Uploading audio to D-ID...")
        audio_url = self.upload_audio(audio_path)
        
        # Create talk
        print("Creating talk...")
        return self.create_talk(image_url, audio_url, config)
    
    def create_talk(self, image_url, audio_url, config=None):
        """
        Create talking video
        
        Args:
            image_url: URL of source image
            audio_url: URL of audio file
            config: Optional configuration dict
            
        Returns:
            talk_id: ID of the talk job
        """
        url = f"{self.base_url}/talks"
        
        payload = {
            "source_url": image_url,
            "script": {
                "type": "audio",
                "audio_url": audio_url
            }
        }
        
        if config:
            payload.update(config)
        
        response = requests.post(url, headers=self.headers, json=payload)
        
        if response.status_code == 201:
            return response.json()['id']
        else:
            raise Exception(f"Talk creation failed: {response.text}")
    
    def get_talk_status(self, talk_id):
        """
        Get status of talk generation
        
        Args:
            talk_id: ID of the talk job
            
        Returns:
            status_data: Dict with status and result_url if ready
        """
        url = f"{self.base_url}/talks/{talk_id}"
        response = requests.get(url, headers=self.headers)
        
        if response.status_code == 200:
            data = response.json()
            return {
                'status': data['status'],
                'result_url': data.get('result_url'),
                'error': data.get('error')
            }
        else:
            raise Exception(f"Status check failed: {response.text}")
    
    def wait_for_completion(self, talk_id, timeout=300, poll_interval=5):
        """
        Wait for talk generation to complete
        
        Args:
            talk_id: ID of the talk job
            timeout: Maximum wait time in seconds
            poll_interval: Seconds between status checks
            
        Returns:
            result_url: URL of generated video
        """
        start_time = time.time()
        
        while time.time() - start_time < timeout:
            status_data = self.get_talk_status(talk_id)
            
            if status_data['status'] == 'done':
                return status_data['result_url']
            elif status_data['status'] == 'error':
                raise Exception(f"Generation failed: {status_data.get('error')}")
            
            time.sleep(poll_interval)
        
        raise Exception("Generation timed out")
    
    def download_video(self, video_url, output_path):
        """
        Download generated video
        
        Args:
            video_url: URL of the video
            output_path: Path to save video
        """
        response = requests.get(video_url, stream=True)
        
        if response.status_code == 200:
            with open(output_path, 'wb') as f:
                for chunk in response.iter_content(chunk_size=8192):
                    f.write(chunk)
        else:
            raise Exception(f"Video download failed: {response.status_code}")
    
    def generate_avatar(self, image_path, audio_path, output_path, config=None):
        """
        Complete pipeline: upload, generate, download
        
        Args:
            image_path: Path to source image
            audio_path: Path to audio file
            output_path: Path to save result
            config: Optional configuration
            
        Returns:
            output_path: Path to generated video
        """
        print("Compressing audio if needed...")
        compressed_audio = self.compress_audio(audio_path)
        
        print("Creating talking avatar with D-ID...")
        talk_id = self.create_talk_with_files(image_path, compressed_audio, config)
        
        if compressed_audio != audio_path and os.path.exists(compressed_audio):
            os.remove(compressed_audio)
        
        print(f"Generation started (ID: {talk_id})")
        print("Waiting for completion...")
        
        result_url = self.wait_for_completion(talk_id)
        
        print("Downloading video...")
        self.download_video(result_url, output_path)
        
        print(f"✓ Avatar generated: {output_path}")
        return output_path
