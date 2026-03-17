#!/usr/bin/env python3
from flask import Flask, render_template, request, send_file, jsonify
from flask_cors import CORS
import os
from pathlib import Path
import uuid
import subprocess

app = Flask(__name__)
CORS(app)

UPLOAD_FOLDER = Path('uploads')
OUTPUT_FOLDER = Path('outputs')
UPLOAD_FOLDER.mkdir(exist_ok=True)
OUTPUT_FOLDER.mkdir(exist_ok=True)

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['OUTPUT_FOLDER'] = OUTPUT_FOLDER
app.config['MAX_CONTENT_LENGTH'] = 500 * 1024 * 1024  # 500MB

ALLOWED_VIDEO_EXTENSIONS = {'mp4', 'avi', 'mov', 'mkv', 'webm'}
ALLOWED_IMAGE_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'}
ALLOWED_AUDIO_EXTENSIONS = {'mp3', 'wav', 'm4a', 'ogg', 'flac'}

def allowed_file(filename, allowed_extensions):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in allowed_extensions

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/extract-audio', methods=['POST'])
def extract_audio():
    """Extract audio from uploaded video"""
    if 'video' not in request.files:
        return jsonify({'error': 'No video file provided'}), 400
    
    video_file = request.files['video']
    
    if video_file.filename == '':
        return jsonify({'error': 'No file selected'}), 400
    
    if not allowed_file(video_file.filename, ALLOWED_VIDEO_EXTENSIONS):
        return jsonify({'error': 'Invalid video format'}), 400
    
    job_id = str(uuid.uuid4())[:8]
    video_ext = video_file.filename.rsplit('.', 1)[1].lower()
    
    video_path = app.config['UPLOAD_FOLDER'] / f"vid_{job_id}.{video_ext}"
    audio_path = app.config['OUTPUT_FOLDER'] / f"audio_{job_id}.mp3"
    
    video_file.save(str(video_path))
    
    try:
        # Use ffmpeg to extract audio
        cmd = [
            'ffmpeg', '-i', str(video_path),
            '-vn', '-acodec', 'libmp3lame',
            '-y', str(audio_path)
        ]
        
        result = subprocess.run(cmd, capture_output=True, text=True)
        
        if result.returncode != 0:
            os.remove(video_path)
            return jsonify({'error': 'Failed to extract audio from video'}), 400
        
        # Get duration
        duration_cmd = [
            'ffprobe', '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
            str(audio_path)
        ]
        duration_result = subprocess.run(duration_cmd, capture_output=True, text=True)
        duration = float(duration_result.stdout.strip()) if duration_result.returncode == 0 else 0
        
        os.remove(video_path)
        
        return jsonify({
            'success': True,
            'audio_file': f"audio_{job_id}.mp3",
            'duration': duration,
            'download_url': f'/download-audio/{job_id}'
        })
    
    except Exception as e:
        if os.path.exists(video_path):
            os.remove(video_path)
        return jsonify({'error': str(e)}), 500

@app.route('/download-audio/<job_id>')
def download_audio(job_id):
    """Download extracted audio"""
    audio_path = app.config['OUTPUT_FOLDER'] / f"audio_{job_id}.mp3"
    
    if not os.path.exists(audio_path):
        return jsonify({'error': 'Audio file not found'}), 404
    
    return send_file(audio_path, as_attachment=True, download_name=f'extracted_audio_{job_id}.mp3')

@app.route('/colab-instructions')
def colab_instructions():
    """Return Google Colab notebook URL and instructions"""
    return jsonify({
        'colab_url': 'https://colab.research.google.com/drive/YOUR_NOTEBOOK_ID',
        'instructions': [
            '1. Click the Colab link above',
            '2. Click "Runtime" → "Run all"',
            '3. Upload your character image when prompted',
            '4. Upload your audio file',
            '5. Wait for generation (10-30 minutes)',
            '6. Download the result video'
        ]
    })

@app.route('/health')
def health():
    return jsonify({
        'status': 'healthy',
        'service': 'AI Scene Generator - Free Edition'
    })

if __name__ == '__main__':
    print("\n" + "="*60)
    print("AI Scene Generator - Free Google Colab Edition")
    print("="*60)
    print("\nFeatures:")
    print("  ✓ Extract audio from videos (local)")
    print("  ✓ Google Colab integration (free GPU)")
    print("  ✓ AnimateDiff scene generation")
    print("\nStarting server on http://localhost:5005")
    print("\nPress Ctrl+C to stop\n")
    
    app.run(debug=True, host='0.0.0.0', port=5005)
