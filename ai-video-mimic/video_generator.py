import torch
import cv2
import numpy as np
from tqdm import tqdm
import os
from models.wav2lip_model import Wav2Lip
from face_detector import FaceDetector
from audio_processor import AudioProcessor

class VideoGenerator:
    def __init__(self, checkpoint_path, device='cpu'):
        """
        Initialize video generator with Wav2Lip model
        
        Args:
            checkpoint_path: Path to Wav2Lip model checkpoint
            device: 'cpu' or 'cuda'
        """
        self.device = device
        self.model = self.load_model(checkpoint_path)
        self.face_detector = FaceDetector(device=device)
        self.audio_processor = AudioProcessor()
        
    def load_model(self, checkpoint_path):
        """Load Wav2Lip model from checkpoint"""
        model = Wav2Lip()
        
        if not os.path.exists(checkpoint_path):
            raise FileNotFoundError(f"Model checkpoint not found: {checkpoint_path}")
        
        checkpoint = torch.load(checkpoint_path, map_location=torch.device(self.device))
        
        if 'state_dict' in checkpoint:
            model.load_state_dict(checkpoint['state_dict'])
        else:
            model.load_state_dict(checkpoint)
            
        model = model.to(self.device)
        model.eval()
        
        return model
    
    def preprocess_image(self, image):
        """
        Preprocess image for model input
        
        Args:
            image: numpy array (H, W, 3) in RGB, values 0-255
            
        Returns:
            Normalized image tensor
        """
        image = image.astype(np.float32) / 255.0
        image = (image - 0.5) / 0.5
        return image
    
    def postprocess_image(self, image):
        """
        Convert model output back to displayable image
        
        Args:
            image: tensor or numpy array, normalized -1 to 1
            
        Returns:
            numpy array (H, W, 3) in RGB, values 0-255
        """
        if isinstance(image, torch.Tensor):
            image = image.cpu().numpy()
        
        image = (image * 0.5 + 0.5) * 255.0
        image = np.clip(image, 0, 255).astype(np.uint8)
        return image
    
    def generate_video(self, image_path, audio_path, output_path, fps=25, face_size=96):
        """
        Generate lip-synced video from image and audio
        
        Args:
            image_path: Path to input image
            audio_path: Path to audio file
            output_path: Path for output video
            fps: Frames per second
            face_size: Size of face region for model
        """
        print("Loading and processing image...")
        face, original_image, bbox = self.face_detector.prepare_face_for_model(
            image_path, 
            target_size=(face_size, face_size)
        )
        
        print("Processing audio...")
        mel_chunks, audio = self.audio_processor.prepare_audio_windows(audio_path, fps=fps)
        
        print(f"Generating {len(mel_chunks)} frames...")
        
        img_batch = []
        mel_batch = []
        frame_h, frame_w = face.shape[0], face.shape[1]
        
        # Normalize face to [0, 1] range
        face_normalized = face.astype(np.float32) / 255.0
        
        for mel_chunk in mel_chunks:
            img_batch.append(face_normalized)
            # mel_chunk is (16, 80), transpose to (80, 16) for model
            mel_batch.append(mel_chunk.T)
            
            if len(img_batch) >= 128:
                img_batch = np.asarray(img_batch)
                mel_batch = np.asarray(mel_batch)
                
                # Create masked version (lower half zeroed)
                img_masked = img_batch.copy()
                img_masked[:, face_size//2:] = 0
                
                # Concatenate masked and original along channel dimension
                img_batch_final = np.concatenate((img_masked, img_batch), axis=3)
                # mel_batch shape: (batch, 80, 16), add channel dimension -> (batch, 1, 80, 16)
                mel_batch = np.expand_dims(mel_batch, axis=1)
                
                yield self._generate_batch(img_batch_final, mel_batch)
                
                img_batch = []
                mel_batch = []
        
        if len(img_batch) > 0:
            img_batch = np.asarray(img_batch)
            mel_batch = np.asarray(mel_batch)
            
            # Create masked version (lower half zeroed)
            img_masked = img_batch.copy()
            img_masked[:, face_size//2:] = 0
            
            # Concatenate masked and original along channel dimension
            img_batch_final = np.concatenate((img_masked, img_batch), axis=3)
            # mel_batch shape: (batch, 80, 16), add channel dimension -> (batch, 1, 80, 16)
            mel_batch = np.expand_dims(mel_batch, axis=1)
            
            yield self._generate_batch(img_batch_final, mel_batch)
    
    def _generate_batch(self, img_batch, mel_batch):
        """Generate frames for a batch"""
        img_batch = torch.FloatTensor(np.transpose(img_batch, (0, 3, 1, 2))).to(self.device)
        # mel_batch is already (batch, 1, 80, 16) - no transpose needed
        mel_batch = torch.FloatTensor(mel_batch).to(self.device)
        
        with torch.no_grad():
            pred = self.model(mel_batch, img_batch)
        
        pred = pred.cpu().numpy().transpose(0, 2, 3, 1) * 255.
        
        return pred
    
    def create_video_file(self, image_path, audio_path, output_path, fps=25, quality='medium'):
        """
        Complete pipeline to create video file
        
        Args:
            image_path: Path to input image
            audio_path: Path to audio file
            output_path: Path for output video
            fps: Frames per second
            quality: 'low', 'medium', or 'high'
        """
        quality_settings = {
            'low': (480, 640, 96),
            'medium': (720, 1280, 96),
            'high': (1080, 1920, 96)
        }
        
        height, width, face_size = quality_settings.get(quality, quality_settings['medium'])
        
        print(f"\n{'='*60}")
        print(f"AI Video Mimicking - Starting Generation")
        print(f"{'='*60}")
        print(f"Image: {image_path}")
        print(f"Audio: {audio_path}")
        print(f"Output: {output_path}")
        print(f"Quality: {quality} ({width}x{height} @ {fps}fps)")
        print(f"{'='*60}\n")
        
        temp_video = output_path.replace('.mp4', '_temp.mp4')
        
        fourcc = cv2.VideoWriter_fourcc(*'mp4v')
        out = cv2.VideoWriter(temp_video, fourcc, fps, (face_size, face_size))
        
        frame_count = 0
        for frames in tqdm(self.generate_video(image_path, audio_path, output_path, fps, face_size), 
                          desc="Generating frames"):
            for frame in frames:
                frame_bgr = cv2.cvtColor(frame.astype(np.uint8), cv2.COLOR_RGB2BGR)
                out.write(frame_bgr)
                frame_count += 1
        
        out.release()
        
        print(f"\nGenerated {frame_count} frames")
        print("Adding audio to video...")
        
        command = f'ffmpeg -y -i {temp_video} -i {audio_path} -c:v libx264 -c:a aac -strict experimental -shortest {output_path}'
        os.system(command)
        
        os.remove(temp_video)
        
        print(f"\n{'='*60}")
        print(f"✓ Video generation complete!")
        print(f"Output saved to: {output_path}")
        print(f"{'='*60}\n")
