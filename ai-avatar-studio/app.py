#!/usr/bin/env python3
from flask import Flask, render_template, request, send_file, jsonify
from flask_cors import CORS
import os
import uuid
from pathlib import Path
import threading
import cv2
import numpy as np

from core.face_analyzer import FaceAnalyzer
from core.motion_generator import MotionGenerator
from core.avatar_animator import AvatarAnimator

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

def process_avatar_async(job_id, image_path, audio_path, output_path, style, fps, device):
    """Process avatar generation in background thread"""
    try:
        job_status[job_id] = {'status': 'processing', 'progress': 0, 'message': 'Analyzing face...'}
        
        face_analyzer = FaceAnalyzer(device=device)
        image = cv2.imread(str(image_path))
        image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        
        landmarks_3d, bbox = face_analyzer.detect_face_3d(image_rgb)
        face_crop, transform = face_analyzer.extract_face_region(image_rgb, bbox, (512, 512))
        
        job_status[job_id] = {'status': 'processing', 'progress': 20, 'message': 'Analyzing audio...'}
        
        motion_gen = MotionGenerator()
        audio_features = motion_gen.analyze_audio(str(audio_path))
        
        job_status[job_id] = {'status': 'processing', 'progress': 40, 'message': 'Generating motion...'}
        
        motion_sequence = motion_gen.generate_head_motion(audio_features, fps=fps, style=style)
        motion_sequence = motion_gen.smooth_motion(motion_sequence, window_size=5)
        
        job_status[job_id] = {'status': 'processing', 'progress': 60, 'message': 'Generating expressions...'}
        
        expression_sequence = motion_gen.generate_expression_sequence(audio_features, fps=fps, style=style)
        
        job_status[job_id] = {'status': 'processing', 'progress': 80, 'message': 'Animating avatar...'}
        
        animator = AvatarAnimator(device=device)
        temp_video = animator.generate_avatar_video(
            face_crop, landmarks_3d, motion_sequence, expression_sequence, str(output_path), fps=fps
        )
        
        job_status[job_id] = {'status': 'processing', 'progress': 90, 'message': 'Adding audio...'}
        
        command = f'ffmpeg -y -i {temp_video} -i {audio_path} -c:v libx264 -c:a aac -strict experimental -shortest {output_path}'
        os.system(command)
        
        if os.path.exists(temp_video):
            os.remove(temp_video)
        
        job_status[job_id] = {
            'status': 'completed',
            'progress': 100,
            'message': 'Avatar generation complete!',
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

@app.route('/upload', methods=['POST'])
def upload_files():
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
    
    job_id = str(uuid.uuid4())
    
    image_ext = image_file.filename.rsplit('.', 1)[1].lower()
    audio_ext = audio_file.filename.rsplit('.', 1)[1].lower()
    
    image_path = app.config['UPLOAD_FOLDER'] / f"{job_id}_image.{image_ext}"
    audio_path = app.config['UPLOAD_FOLDER'] / f"{job_id}_audio.{audio_ext}"
    output_path = app.config['OUTPUT_FOLDER'] / f"{job_id}_avatar.mp4"
    
    image_file.save(str(image_path))
    audio_file.save(str(audio_path))
    
    style = request.form.get('style', 'natural')
    fps = int(request.form.get('fps', 25))
    device = 'cpu'
    
    job_status[job_id] = {'status': 'queued', 'progress': 0, 'message': 'Job queued'}
    
    thread = threading.Thread(
        target=process_avatar_async,
        args=(job_id, str(image_path), str(audio_path), str(output_path), style, fps, device)
    )
    thread.start()
    
    return jsonify({
        'job_id': job_id,
        'message': 'Processing started'
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
    
    return send_file(output_file, as_attachment=True, download_name=f'avatar_{job_id}.mp4')

@app.route('/health')
def health():
    return jsonify({
        'status': 'healthy',
        'service': 'AI Avatar Studio'
    })

if __name__ == '__main__':
    print("\n" + "="*60)
    print("AI Avatar Studio - Web Interface")
    print("="*60)
    print("\nStarting server on http://localhost:5003")
    print("\nPress Ctrl+C to stop\n")
    
    app.run(debug=True, host='0.0.0.0', port=5003)
