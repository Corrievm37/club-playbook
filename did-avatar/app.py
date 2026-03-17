#!/usr/bin/env python3
from flask import Flask, render_template, request, send_file, jsonify
from flask_cors import CORS
import os
from pathlib import Path
import uuid
import threading
from dotenv import load_dotenv

from did_api import DIDClient

load_dotenv()

app = Flask(__name__)
CORS(app)

UPLOAD_FOLDER = Path('uploads')
OUTPUT_FOLDER = Path('outputs')
UPLOAD_FOLDER.mkdir(exist_ok=True)
OUTPUT_FOLDER.mkdir(exist_ok=True)

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['OUTPUT_FOLDER'] = OUTPUT_FOLDER
app.config['MAX_CONTENT_LENGTH'] = 100 * 1024 * 1024

ALLOWED_IMAGE_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'}
ALLOWED_AUDIO_EXTENSIONS = {'mp3', 'wav', 'm4a', 'ogg', 'flac'}

job_status = {}

def allowed_file(filename, allowed_extensions):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in allowed_extensions

def process_avatar_async(job_id, image_path, audio_path, output_path, api_key):
    """Process avatar generation in background thread"""
    try:
        job_status[job_id] = {'status': 'processing', 'progress': 10, 'message': 'Initializing D-ID API...'}
        
        client = DIDClient(api_key)
        
        job_status[job_id] = {'status': 'processing', 'progress': 30, 'message': 'Compressing audio...'}
        compressed_audio = client.compress_audio(str(audio_path))
        
        job_status[job_id] = {'status': 'processing', 'progress': 50, 'message': 'Creating lifelike avatar...'}
        talk_id = client.create_talk_with_files(str(image_path), compressed_audio)
        
        if compressed_audio != str(audio_path) and os.path.exists(compressed_audio):
            os.remove(compressed_audio)
        
        job_status[job_id] = {'status': 'processing', 'progress': 70, 'message': f'AI generating video (ID: {talk_id})...'}
        
        result_url = client.wait_for_completion(talk_id)
        
        job_status[job_id] = {'status': 'processing', 'progress': 90, 'message': 'Downloading video...'}
        client.download_video(result_url, str(output_path))
        
        job_status[job_id] = {
            'status': 'completed',
            'progress': 100,
            'message': 'Lifelike avatar generated successfully!',
            'output_file': str(output_path)
        }
    except Exception as e:
        job_status[job_id] = {
            'status': 'failed',
            'progress': 0,
            'message': f'Error: {str(e)}'
        }

@app.route('/')
def index():
    return render_template('index.html')

def sanitize_filename(filename, max_length=50):
    """Sanitize filename to prevent issues with D-ID API"""
    name, ext = os.path.splitext(filename)
    name = name[:max_length]
    name = ''.join(c for c in name if c.isalnum() or c in ('-', '_'))
    return name + ext

@app.route('/upload', methods=['POST'])
def upload_files():
    api_key = os.getenv('DID_API_KEY')
    
    if not api_key:
        return jsonify({'error': 'D-ID API key not configured. Please add DID_API_KEY to .env file'}), 400
    
    if 'image' not in request.files or 'audio' not in request.files:
        return jsonify({'error': 'Missing image or audio file'}), 400
    
    image_file = request.files['image']
    audio_file = request.files['audio']
    
    if image_file.filename == '' or audio_file.filename == '':
        return jsonify({'error': 'No file selected'}), 400
    
    if not allowed_file(image_file.filename, ALLOWED_IMAGE_EXTENSIONS):
        return jsonify({'error': 'Invalid image format'}), 400
    
    if not allowed_file(audio_file.filename, ALLOWED_AUDIO_EXTENSIONS):
        return jsonify({'error': 'Invalid audio format'}), 400
    
    job_id = str(uuid.uuid4())[:8]
    
    image_ext = image_file.filename.rsplit('.', 1)[1].lower()
    audio_ext = audio_file.filename.rsplit('.', 1)[1].lower()
    
    image_path = app.config['UPLOAD_FOLDER'] / f"img_{job_id}.{image_ext}"
    audio_path = app.config['UPLOAD_FOLDER'] / f"aud_{job_id}.{audio_ext}"
    output_path = app.config['OUTPUT_FOLDER'] / f"avatar_{job_id}.mp4"
    
    image_file.save(str(image_path))
    audio_file.save(str(audio_path))
    
    job_status[job_id] = {'status': 'queued', 'progress': 0, 'message': 'Job queued'}
    
    thread = threading.Thread(
        target=process_avatar_async,
        args=(job_id, str(image_path), str(audio_path), str(output_path), api_key)
    )
    thread.start()
    
    return jsonify({
        'job_id': job_id,
        'message': 'Processing started with D-ID API'
    })

@app.route('/status/<job_id>')
def get_status(job_id):
    if job_id not in job_status:
        return jsonify({'error': 'Job not found'}), 404
    
    return jsonify(job_status[job_id])

@app.route('/download/<job_id>')
def download_video(job_id):
    if job_id not in job_status:
        return jsonify({'error': 'Job not found'}), 404
    
    status = job_status[job_id]
    
    if status['status'] != 'completed':
        return jsonify({'error': 'Video not ready'}), 400
    
    output_file = status['output_file']
    
    if not os.path.exists(output_file):
        return jsonify({'error': 'Output file not found'}), 404
    
    return send_file(output_file, as_attachment=True, download_name=f'lifelike_avatar_{job_id}.mp4')

@app.route('/health')
def health():
    api_key = os.getenv('DID_API_KEY')
    return jsonify({
        'status': 'healthy',
        'service': 'D-ID Avatar Generator',
        'api_configured': bool(api_key)
    })

if __name__ == '__main__':
    print("\n" + "="*60)
    print("D-ID AI Avatar Generator - Professional Quality")
    print("="*60)
    
    api_key = os.getenv('DID_API_KEY')
    if not api_key:
        print("\n⚠️  WARNING: D-ID API key not found!")
        print("Please create a .env file with:")
        print("DID_API_KEY=your_api_key_here")
        print("\nGet your API key at: https://studio.d-id.com/")
    else:
        print("\n✓ D-ID API key configured")
    
    print("\nStarting server on http://localhost:5004")
    print("\nPress Ctrl+C to stop\n")
    
    app.run(debug=True, host='0.0.0.0', port=5004)
