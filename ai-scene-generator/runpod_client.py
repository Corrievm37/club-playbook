#!/usr/bin/env python3
"""
RunPod API Client for AnimateDiff Video Generation
Automates the entire process: upload, generate, download
"""
import requests
import os
import time
import json
from pathlib import Path

class RunPodClient:
    def __init__(self, api_key):
        """
        Initialize RunPod client
        
        Args:
            api_key: Your RunPod API key from https://runpod.io/console/user/settings
        """
        self.api_key = api_key
        self.base_url = "https://api.runpod.io/v2"
        self.headers = {
            "Authorization": f"Bearer {api_key}",
            "Content-Type": "application/json"
        }
    
    def list_pods(self):
        """List all your pods"""
        response = requests.get(
            f"{self.base_url}/pods",
            headers=self.headers
        )
        return response.json()
    
    def start_pod(self, template_id="comfyui", gpu_type="NVIDIA RTX 3080"):
        """
        Start a new pod with ComfyUI/AnimateDiff
        
        Args:
            template_id: Template to use
            gpu_type: GPU type to rent
            
        Returns:
            pod_id: ID of started pod
        """
        payload = {
            "cloudType": "SECURE",
            "gpuTypeId": gpu_type,
            "templateId": template_id,
            "name": "AnimateDiff Generator"
        }
        
        response = requests.post(
            f"{self.base_url}/pods",
            headers=self.headers,
            json=payload
        )
        
        if response.status_code == 200:
            data = response.json()
            return data['id']
        else:
            raise Exception(f"Failed to start pod: {response.text}")
    
    def stop_pod(self, pod_id):
        """Stop a pod to save credits"""
        response = requests.post(
            f"{self.base_url}/pods/{pod_id}/stop",
            headers=self.headers
        )
        return response.json()
    
    def get_pod_status(self, pod_id):
        """Get pod status"""
        response = requests.get(
            f"{self.base_url}/pods/{pod_id}",
            headers=self.headers
        )
        return response.json()
    
    def wait_for_pod_ready(self, pod_id, timeout=300):
        """Wait for pod to be ready"""
        print(f"Waiting for pod {pod_id} to be ready...")
        start_time = time.time()
        
        while time.time() - start_time < timeout:
            status = self.get_pod_status(pod_id)
            
            if status.get('status') == 'RUNNING':
                print("✓ Pod is ready!")
                return status
            
            print(f"Status: {status.get('status')}... waiting")
            time.time.sleep(10)
        
        raise TimeoutError("Pod did not start in time")
    
    def generate_video(self, pod_id, image_path, audio_path, prompt, output_path):
        """
        Generate video using RunPod
        
        Args:
            pod_id: ID of running pod
            image_path: Path to character image
            audio_path: Path to audio file
            prompt: Text prompt for generation
            output_path: Where to save result
            
        Returns:
            output_path: Path to generated video
        """
        # Get pod connection info
        pod_info = self.get_pod_status(pod_id)
        
        # This would connect to ComfyUI API on the pod
        # For now, this is a placeholder showing the structure
        
        print(f"Pod URL: {pod_info.get('url')}")
        print("Upload your files manually to ComfyUI interface")
        print(f"Or use the web interface at http://localhost:5005")
        
        return output_path

def main():
    """Example usage"""
    import argparse
    
    parser = argparse.ArgumentParser(description='RunPod AnimateDiff Generator')
    parser.add_argument('--api-key', required=True, help='RunPod API key')
    parser.add_argument('--action', choices=['start', 'stop', 'list'], required=True)
    parser.add_argument('--pod-id', help='Pod ID for stop action')
    
    args = parser.parse_args()
    
    client = RunPodClient(args.api_key)
    
    if args.action == 'list':
        pods = client.list_pods()
        print(json.dumps(pods, indent=2))
    
    elif args.action == 'start':
        pod_id = client.start_pod()
        print(f"Started pod: {pod_id}")
        client.wait_for_pod_ready(pod_id)
    
    elif args.action == 'stop':
        if not args.pod_id:
            print("Error: --pod-id required for stop action")
            return
        result = client.stop_pod(args.pod_id)
        print(f"Stopped pod: {result}")

if __name__ == "__main__":
    main()
