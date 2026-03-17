import torch
import numpy as np
import cv2
from PIL import Image
from tqdm import tqdm

class AvatarAnimator:
    """Main avatar animation pipeline combining all components"""
    
    def __init__(self, device='cpu'):
        self.device = device
        
    def apply_head_motion(self, image, landmarks, pose_delta):
        """
        Apply head pose transformation to image
        
        Args:
            image: Face image (H, W, 3)
            landmarks: Facial landmarks
            pose_delta: Change in pose (pitch, yaw, roll)
            
        Returns:
            warped_image: Image with applied head motion
        """
        h, w = image.shape[:2]
        
        pitch = pose_delta['pitch'] * np.pi / 180
        yaw = pose_delta['yaw'] * np.pi / 180
        roll = pose_delta['roll'] * np.pi / 180
        
        center_x, center_y = w / 2, h / 2
        
        rotation_matrix = cv2.getRotationMatrix2D((center_x, center_y), roll, 1.0)
        
        cos_pitch = np.cos(pitch)
        sin_pitch = np.sin(pitch)
        cos_yaw = np.cos(yaw)
        sin_yaw = np.sin(yaw)
        
        scale_x = cos_yaw
        scale_y = cos_pitch
        
        shear_x = sin_yaw * 0.5
        shear_y = sin_pitch * 0.5
        
        perspective_matrix = np.array([
            [scale_x, shear_x, center_x * (1 - scale_x)],
            [shear_y, scale_y, center_y * (1 - scale_y)],
            [0, 0, 1]
        ], dtype=np.float32)
        
        combined_matrix = perspective_matrix[:2, :] @ np.vstack([rotation_matrix, [0, 0, 1]])
        
        warped = cv2.warpAffine(image, combined_matrix, (w, h), 
                                flags=cv2.INTER_LINEAR,
                                borderMode=cv2.BORDER_REPLICATE)
        
        return warped
    
    def apply_expression(self, image, landmarks, expression_params):
        """
        Apply facial expression deformation
        
        Args:
            image: Face image
            landmarks: Facial landmarks
            expression_params: Expression parameters (mouth_open, smile, etc.)
            
        Returns:
            deformed_image: Image with applied expression
        """
        h, w = image.shape[:2]
        
        map_x = np.zeros((h, w), dtype=np.float32)
        map_y = np.zeros((h, w), dtype=np.float32)
        
        for y in range(h):
            for x in range(w):
                map_x[y, x] = x
                map_y[y, x] = y
        
        if landmarks is not None and len(landmarks) >= 68:
            mouth_center = np.mean(landmarks[48:68], axis=0)
            mouth_open = expression_params.get('mouth_open', 0)
            
            for y in range(h):
                for x in range(w):
                    dist = np.sqrt((x - mouth_center[0])**2 + (y - mouth_center[1])**2)
                    if dist < 50:
                        influence = (50 - dist) / 50
                        map_y[y, x] += mouth_open * 10 * influence
            
            smile = expression_params.get('smile', 0)
            if smile > 0:
                mouth_corners = [landmarks[48], landmarks[54]]
                for corner in mouth_corners:
                    for y in range(h):
                        for x in range(w):
                            dist = np.sqrt((x - corner[0])**2 + (y - corner[1])**2)
                            if dist < 30:
                                influence = (30 - dist) / 30
                                map_y[y, x] -= smile * 5 * influence
        
        deformed = cv2.remap(image, map_x, map_y, cv2.INTER_LINEAR)
        
        return deformed
    
    def blend_frames(self, frame1, frame2, alpha=0.5):
        """
        Blend two frames for smooth transitions
        
        Args:
            frame1: First frame
            frame2: Second frame
            alpha: Blend factor (0-1)
            
        Returns:
            blended: Blended frame
        """
        return cv2.addWeighted(frame1, 1 - alpha, frame2, alpha, 0)
    
    def enhance_frame(self, frame):
        """
        Enhance frame quality
        
        Args:
            frame: Input frame
            
        Returns:
            enhanced: Enhanced frame
        """
        lab = cv2.cvtColor(frame, cv2.COLOR_RGB2LAB)
        l, a, b = cv2.split(lab)
        
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
        l = clahe.apply(l)
        
        enhanced_lab = cv2.merge([l, a, b])
        enhanced = cv2.cvtColor(enhanced_lab, cv2.COLOR_LAB2RGB)
        
        kernel = np.array([[-1,-1,-1],
                          [-1, 9,-1],
                          [-1,-1,-1]])
        enhanced = cv2.filter2D(enhanced, -1, kernel * 0.1 + np.eye(3) * 0.9)
        
        return enhanced
    
    def stabilize_video(self, frames):
        """
        Apply video stabilization
        
        Args:
            frames: List of frames
            
        Returns:
            stabilized_frames: Stabilized frames
        """
        if len(frames) < 2:
            return frames
        
        stabilized = [frames[0]]
        
        prev_gray = cv2.cvtColor(frames[0], cv2.COLOR_RGB2GRAY)
        
        for i in range(1, len(frames)):
            curr_gray = cv2.cvtColor(frames[i], cv2.COLOR_RGB2GRAY)
            
            # Use estimateAffinePartial2D instead of deprecated estimateRigidTransform
            # Detect feature points
            prev_pts = cv2.goodFeaturesToTrack(prev_gray, maxCorners=200, qualityLevel=0.01, minDistance=30)
            
            if prev_pts is not None:
                curr_pts, status, _ = cv2.calcOpticalFlowPyrLK(prev_gray, curr_gray, prev_pts, None)
                
                # Filter only valid points
                idx = np.where(status == 1)[0]
                if len(idx) > 4:
                    prev_pts = prev_pts[idx]
                    curr_pts = curr_pts[idx]
                    
                    # Estimate affine transform
                    transform, _ = cv2.estimateAffinePartial2D(prev_pts, curr_pts)
                    
                    if transform is not None:
                        h, w = frames[i].shape[:2]
                        stabilized_frame = cv2.warpAffine(frames[i], transform, (w, h))
                        stabilized.append(stabilized_frame)
                    else:
                        stabilized.append(frames[i])
                else:
                    stabilized.append(frames[i])
            else:
                stabilized.append(frames[i])
            
            prev_gray = curr_gray
        
        return stabilized
    
    def generate_avatar_video(self, image, landmarks, motion_sequence, 
                            expression_sequence, output_path, fps=25):
        """
        Generate complete avatar video
        
        Args:
            image: Source face image
            landmarks: Facial landmarks
            motion_sequence: Head motion sequence
            expression_sequence: Expression sequence
            output_path: Output video path
            fps: Frames per second
        """
        num_frames = len(motion_sequence)
        h, w = image.shape[:2]
        
        fourcc = cv2.VideoWriter_fourcc(*'mp4v')
        out = cv2.VideoWriter(output_path.replace('.mp4', '_temp.mp4'), 
                             fourcc, fps, (w, h))
        
        frames = []
        
        print("Generating avatar frames...")
        for i in tqdm(range(num_frames)):
            motion = motion_sequence[i]
            expression = expression_sequence[i]
            
            frame = image.copy()
            
            frame = self.apply_head_motion(frame, landmarks, motion)
            
            frame = self.apply_expression(frame, landmarks, expression)
            
            frame = self.enhance_frame(frame)
            
            frames.append(frame)
        
        print("Stabilizing video...")
        frames = self.stabilize_video(frames)
        
        print("Writing video...")
        for frame in frames:
            frame_bgr = cv2.cvtColor(frame, cv2.COLOR_RGB2BGR)
            out.write(frame_bgr)
        
        out.release()
        
        return output_path.replace('.mp4', '_temp.mp4')
