import cv2
import numpy as np
from PIL import Image
import face_alignment

class FaceDetector:
    def __init__(self, device='cpu'):
        """
        Initialize face detector
        
        Args:
            device: 'cpu' or 'cuda'
        """
        self.device = device
        self.fa = face_alignment.FaceAlignment(
            face_alignment.LandmarksType.TWO_D, 
            device=device,
            flip_input=False
        )
        
    def detect_face(self, image):
        """
        Detect face in image and return bounding box
        
        Args:
            image: numpy array (H, W, 3) in RGB
            
        Returns:
            bbox: [x1, y1, x2, y2] or None if no face detected
        """
        preds = self.fa.get_landmarks(image)
        
        if preds is None or len(preds) == 0:
            return None
            
        landmarks = preds[0]
        
        x_min = int(landmarks[:, 0].min())
        x_max = int(landmarks[:, 0].max())
        y_min = int(landmarks[:, 1].min())
        y_max = int(landmarks[:, 1].max())
        
        padding = 20
        x_min = max(0, x_min - padding)
        y_min = max(0, y_min - padding)
        x_max = min(image.shape[1], x_max + padding)
        y_max = min(image.shape[0], y_max + padding)
        
        return [x_min, y_min, x_max, y_max]
    
    def get_face_region(self, image, bbox, target_size=(96, 96)):
        """
        Extract and resize face region
        
        Args:
            image: numpy array (H, W, 3)
            bbox: [x1, y1, x2, y2]
            target_size: (width, height) for output
            
        Returns:
            face_crop: Resized face region
        """
        x1, y1, x2, y2 = bbox
        face = image[y1:y2, x1:x2]
        
        face_resized = cv2.resize(face, target_size)
        
        return face_resized
    
    def prepare_face_for_model(self, image_path, target_size=(96, 96)):
        """
        Load image, detect face, and prepare for model input
        
        Args:
            image_path: Path to image file
            target_size: Size for face crop
            
        Returns:
            face: Preprocessed face array
            original_image: Original image
            bbox: Face bounding box
        """
        image = cv2.imread(image_path)
        if image is None:
            raise ValueError(f"Could not load image: {image_path}")
            
        image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        
        bbox = self.detect_face(image_rgb)
        
        if bbox is None:
            raise ValueError("No face detected in image")
        
        face = self.get_face_region(image_rgb, bbox, target_size)
        
        return face, image_rgb, bbox
    
    def expand_bbox_for_context(self, bbox, image_shape, expansion_factor=1.5):
        """
        Expand bounding box to include more context around face
        
        Args:
            bbox: [x1, y1, x2, y2]
            image_shape: (height, width) of image
            expansion_factor: How much to expand (1.0 = no expansion)
            
        Returns:
            expanded_bbox: [x1, y1, x2, y2]
        """
        x1, y1, x2, y2 = bbox
        width = x2 - x1
        height = y2 - y1
        
        center_x = (x1 + x2) / 2
        center_y = (y1 + y2) / 2
        
        new_width = width * expansion_factor
        new_height = height * expansion_factor
        
        new_x1 = int(max(0, center_x - new_width / 2))
        new_y1 = int(max(0, center_y - new_height / 2))
        new_x2 = int(min(image_shape[1], center_x + new_width / 2))
        new_y2 = int(min(image_shape[0], center_y + new_height / 2))
        
        return [new_x1, new_y1, new_x2, new_y2]
