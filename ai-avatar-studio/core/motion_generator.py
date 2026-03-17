import numpy as np
import librosa
from scipy.interpolate import interp1d

class MotionGenerator:
    """Generates head motion and expression sequences from audio"""
    
    def __init__(self):
        self.sample_rate = 16000
        
    def analyze_audio(self, audio_path):
        """
        Analyze audio to extract features for animation
        
        Args:
            audio_path: Path to audio file
            
        Returns:
            features: dict with audio features
        """
        audio, sr = librosa.load(audio_path, sr=self.sample_rate)
        
        rms = librosa.feature.rms(y=audio, frame_length=2048, hop_length=512)[0]
        
        spectral_centroid = librosa.feature.spectral_centroid(y=audio, sr=sr, hop_length=512)[0]
        
        mfcc = librosa.feature.mfcc(y=audio, sr=sr, n_mfcc=13, hop_length=512)
        
        tempo, beats = librosa.beat.beat_track(y=audio, sr=sr)
        
        onset_env = librosa.onset.onset_strength(y=audio, sr=sr, hop_length=512)
        
        duration = len(audio) / sr
        
        return {
            'audio': audio,
            'rms': rms,
            'spectral_centroid': spectral_centroid,
            'mfcc': mfcc,
            'tempo': tempo,
            'beats': beats,
            'onset_strength': onset_env,
            'duration': duration,
            'sample_rate': sr
        }
    
    def generate_head_motion(self, audio_features, fps=25, style='natural'):
        """
        Generate head pose sequence from audio
        
        Args:
            audio_features: Audio analysis features
            fps: Frames per second
            style: 'natural', 'expressive', 'dynamic', 'minimal'
            
        Returns:
            motion_sequence: List of pose dicts (pitch, yaw, roll) per frame
        """
        duration = audio_features['duration']
        num_frames = int(duration * fps)
        
        rms = audio_features['rms']
        onset = audio_features['onset_strength']
        
        time_audio = np.linspace(0, duration, len(rms))
        time_video = np.linspace(0, duration, num_frames)
        
        rms_interp = interp1d(time_audio, rms, kind='cubic', fill_value='extrapolate')
        onset_interp = interp1d(time_audio, onset, kind='cubic', fill_value='extrapolate')
        
        rms_video = rms_interp(time_video)
        onset_video = onset_interp(time_video)
        
        intensity = {
            'minimal': 0.3,
            'natural': 0.6,
            'expressive': 1.0,
            'dynamic': 1.5
        }.get(style, 0.6)
        
        motion_sequence = []
        
        base_frequency = 0.5
        
        for i in range(num_frames):
            t = i / fps
            
            energy = rms_video[i]
            onset_strength = onset_video[i]
            
            yaw = intensity * 15 * np.sin(2 * np.pi * base_frequency * t) * (0.5 + energy)
            
            pitch = intensity * 8 * np.sin(2 * np.pi * base_frequency * 0.7 * t + np.pi/4) * (0.3 + energy * 0.7)
            
            roll = intensity * 5 * np.sin(2 * np.pi * base_frequency * 1.3 * t + np.pi/2) * energy
            
            if onset_strength > np.percentile(onset_video, 75):
                yaw += intensity * 5 * np.random.randn()
                pitch += intensity * 3 * np.random.randn()
            
            motion_sequence.append({
                'pitch': float(pitch),
                'yaw': float(yaw),
                'roll': float(roll),
                'timestamp': t
            })
        
        return motion_sequence
    
    def generate_expression_sequence(self, audio_features, fps=25, style='natural'):
        """
        Generate facial expression sequence
        
        Args:
            audio_features: Audio analysis features
            fps: Frames per second
            style: Animation style
            
        Returns:
            expression_sequence: List of expression parameters per frame
        """
        duration = audio_features['duration']
        num_frames = int(duration * fps)
        
        rms = audio_features['rms']
        spectral = audio_features['spectral_centroid']
        
        time_audio = np.linspace(0, duration, len(rms))
        time_video = np.linspace(0, duration, num_frames)
        
        rms_interp = interp1d(time_audio, rms, kind='cubic', fill_value='extrapolate')
        spectral_interp = interp1d(time_audio, spectral, kind='cubic', fill_value='extrapolate')
        
        rms_video = rms_interp(time_video)
        spectral_video = spectral_interp(time_video)
        
        spectral_norm = (spectral_video - spectral_video.min()) / (spectral_video.max() - spectral_video.min() + 1e-8)
        
        expression_sequence = []
        
        blink_interval = fps * 3
        last_blink = -blink_interval
        
        for i in range(num_frames):
            energy = rms_video[i]
            pitch_level = spectral_norm[i]
            
            mouth_open = min(1.0, energy * 1.5)
            
            smile = pitch_level * 0.3 + energy * 0.2
            
            if i - last_blink > blink_interval + np.random.randint(-10, 10):
                eye_open = 0.0
                last_blink = i
            else:
                eye_open = 1.0
            
            eyebrow_raise = pitch_level * 0.4
            
            expression_sequence.append({
                'mouth_open': float(mouth_open),
                'smile': float(smile),
                'eye_open': float(eye_open),
                'eyebrow_raise': float(eyebrow_raise),
                'energy': float(energy)
            })
        
        return expression_sequence
    
    def smooth_motion(self, motion_sequence, window_size=5):
        """
        Apply smoothing to motion sequence
        
        Args:
            motion_sequence: List of motion dicts
            window_size: Smoothing window size
            
        Returns:
            smoothed_sequence: Smoothed motion sequence
        """
        if len(motion_sequence) < window_size:
            return motion_sequence
        
        keys = ['pitch', 'yaw', 'roll']
        smoothed = []
        
        for i in range(len(motion_sequence)):
            start = max(0, i - window_size // 2)
            end = min(len(motion_sequence), i + window_size // 2 + 1)
            
            smoothed_frame = motion_sequence[i].copy()
            
            for key in keys:
                values = [motion_sequence[j][key] for j in range(start, end)]
                smoothed_frame[key] = float(np.mean(values))
            
            smoothed.append(smoothed_frame)
        
        return smoothed
