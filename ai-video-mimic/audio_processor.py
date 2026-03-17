import librosa
import numpy as np
import python_speech_features
from scipy.io import wavfile

class AudioProcessor:
    def __init__(self, sample_rate=16000):
        self.sample_rate = sample_rate
        self.mel_step_size = 16
        
    def load_audio(self, audio_path):
        """Load audio file and convert to 16kHz mono"""
        audio, sr = librosa.load(audio_path, sr=self.sample_rate, mono=True)
        return audio
    
    def get_mel_spectrogram(self, audio):
        """Extract mel spectrogram from audio - returns shape (80, T)"""
        mel = librosa.feature.melspectrogram(y=audio, sr=self.sample_rate, n_mels=80, hop_length=200, win_length=800, n_fft=800)
        mel = librosa.power_to_db(mel, ref=np.max)
        return mel
    
    def prepare_audio_windows(self, audio_path, fps=25):
        """
        Prepare audio windows synchronized with video frames
        
        Args:
            audio_path: Path to audio file
            fps: Frames per second of output video
            
        Returns:
            mel_chunks: List of mel spectrogram chunks, each shape (80, 16)
            audio: Original audio array
        """
        audio = self.load_audio(audio_path)
        
        # Generate mel spectrogram with proper parameters for Wav2Lip
        # hop_length=200 gives us 80 frames per second at 16kHz
        mel = librosa.feature.melspectrogram(
            y=audio, 
            sr=self.sample_rate, 
            n_mels=80, 
            hop_length=200,
            win_length=800,
            n_fft=800
        )
        mel = librosa.power_to_db(mel, ref=np.max)
        # mel shape: (80, time_steps) - do NOT transpose
        
        mel_chunks = []
        mel_idx_multiplier = 80. / fps
        
        i = 0
        while True:
            start_idx = int(i * mel_idx_multiplier)
            if start_idx + self.mel_step_size > mel.shape[1]:
                # Pad the last chunk if needed
                last_chunk = mel[:, max(0, mel.shape[1] - self.mel_step_size):]
                if last_chunk.shape[1] < self.mel_step_size:
                    padding = np.zeros((80, self.mel_step_size - last_chunk.shape[1]))
                    last_chunk = np.hstack([last_chunk, padding])
                mel_chunks.append(last_chunk.T)  # Transpose to (16, 80)
                break
            mel_chunks.append(mel[:, start_idx: start_idx + self.mel_step_size].T)  # Transpose to (16, 80)
            i += 1
            
        return mel_chunks, audio
    
    def get_audio_duration(self, audio_path):
        """Get duration of audio file in seconds"""
        audio = self.load_audio(audio_path)
        return len(audio) / self.sample_rate
    
    def save_audio(self, audio, output_path):
        """Save audio array to file"""
        wavfile.write(output_path, self.sample_rate, (audio * 32767).astype(np.int16))
