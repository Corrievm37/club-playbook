import cv2
import numpy as np
import torch
from PIL import Image
import face_alignment

class FaceAnalyzer:
    """Analyzes face structure, landmarks, and 3D pose"""
    
    def __init__(self, device='cpu'):
        self.device = device
        self.fa = face_alignment.FaceAlignment(
            face_alignment.LandmarksType.THREE_D,
            device=device,
            flip_input=False
        )
        
    def detect_face_3d(self, image):
        """
        Detect face and extract 3D landmarks
        
        Args:
            image: numpy array (H, W, 3) RGB
            
        Returns:
            landmarks_3d: 3D facial landmarks (68, 3)
            bbox: Face bounding box [x1, y1, x2, y2]
        """
        preds = self.fa.get_landmarks_from_image(image)
        
        if preds is None or len(preds) == 0:
            raise ValueError("No face detected in image")
        
        landmarks_3d = preds[0]
        
        x_min = int(landmarks_3d[:, 0].min())
        x_max = int(landmarks_3d[:, 0].max())
        y_min = int(landmarks_3d[:, 1].min())
        y_max = int(landmarks_3d[:, 1].max())
        
        padding = 50
        x_min = max(0, x_min - padding)
        y_min = max(0, y_min - padding)
        x_max = min(image.shape[1], x_max + padding)
        y_max = min(image.shape[0], y_max + padding)
        
        bbox = [x_min, y_min, x_max, y_max]
        
        return landmarks_3d, bbox
    
    def estimate_head_pose(self, landmarks_3d):
        """
        Estimate head pose (pitch, yaw, roll) from 3D landmarks
        
        Args:
            landmarks_3d: 3D facial landmarks (68, 3)
            
        Returns:
            pose: dict with 'pitch', 'yaw', 'roll' in degrees
        """
        nose_tip = landmarks_3d[30]
        chin = landmarks_3d[8]
        left_eye = landmarks_3d[36]
        right_eye = landmarks_3d[45]
        
        eye_center = (left_eye + right_eye) / 2
        
        yaw = np.arctan2(nose_tip[0] - eye_center[0], nose_tip[2]) * 180 / np.pi
        pitch = np.arctan2(nose_tip[1] - chin[1], nose_tip[2] - chin[2]) * 180 / np.pi
        
        eye_vector = right_eye - left_eye
        roll = np.arctan2(eye_vector[1], eye_vector[0]) * 180 / np.pi
        
        return {
            'pitch': float(pitch),
            'yaw': float(yaw),
            'roll': float(roll)
        }
    
    def extract_face_region(self, image, bbox, target_size=(512, 512)):
        """
        Extract and resize face region
        
        Args:
            image: numpy array (H, W, 3)
            bbox: [x1, y1, x2, y2]
            target_size: (width, height)
            
        Returns:
            face_crop: Resized face region
            transform_matrix: Transformation matrix for mapping back
        """
        x1, y1, x2, y2 = bbox
        face = image[y1:y2, x1:x2]
        
        h, w = face.shape[:2]
        scale_x = target_size[0] / w
        scale_y = target_size[1] / h
        
        face_resized = cv2.resize(face, target_size)
        
        transform_matrix = np.array([
            [scale_x, 0, -x1 * scale_x],
            [0, scale_y, -y1 * scale_y],
            [0, 0, 1]
        ])
        
        return face_resized, transform_matrix
    
    def get_facial_regions(self, landmarks_3d):
        """
        Extract specific facial regions (eyes, mouth, etc.)
        
        Args:
            landmarks_3d: 3D facial landmarks (68, 3)
            
        Returns:
            regions: dict of landmark indices for each region
        """
        return {
            'left_eye': list(range(36, 42)),
            'right_eye': list(range(42, 48)),
            'mouth_outer': list(range(48, 60)),
            'mouth_inner': list(range(60, 68)),
            'nose': list(range(27, 36)),
            'left_eyebrow': list(range(17, 22)),
            'right_eyebrow': list(range(22, 27)),
            'jaw': list(range(0, 17))
        }
