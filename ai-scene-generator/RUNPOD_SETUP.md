# RunPod AnimateDiff Setup Guide

## 🎁 Free Trial: $10 Credits

Get started with RunPod's free credits - enough for ~30 hours of GPU time!

## Step 1: Create RunPod Account

1. Go to: https://runpod.io/
2. Click **Sign Up**
3. Use code: **WELCOME10** for $10 free credits
4. Verify your email

## Step 2: Deploy AnimateDiff Template

1. **Go to Templates:** https://runpod.io/console/explore
2. **Search:** "AnimateDiff" or "ComfyUI"
3. **Select:** "RunPod ComfyUI" template
4. **Click:** Deploy

### Recommended GPU:
- **RTX 4090** - $0.69/hour (fastest)
- **RTX 3090** - $0.44/hour (good balance)
- **RTX 3080** - $0.34/hour (budget)

**With $10 credits:**
- RTX 3080: ~29 hours
- RTX 3090: ~22 hours
- RTX 4090: ~14 hours

## Step 3: Access ComfyUI

1. Wait for pod to start (~2 minutes)
2. Click **Connect** → **HTTP Service [Port 3000]**
3. ComfyUI interface opens in browser

## Step 4: Generate Your Video

### Using ComfyUI:

1. **Load AnimateDiff Workflow:**
   - File → Load → Select "AnimateDiff" workflow

2. **Upload Your Files:**
   - Click "Upload Image" → Select skull character
   - Click "Upload Audio" → Select your audio file

3. **Set Parameters:**
   - Prompt: "animated skeleton character speaking with emotions, dramatic lighting, full body"
   - Frames: 48 (for 6 seconds at 8fps)
   - Steps: 20

4. **Generate:**
   - Click "Queue Prompt"
   - Wait 5-10 minutes
   - Download result

## Step 5: Stop Pod When Done

**IMPORTANT:** Stop your pod to save credits!
- Go to Pods → Click **Stop**
- You only pay for time used

## 💰 Cost Breakdown

**Per 30-second video:**
- Generation time: ~10 minutes
- GPU cost: ~$0.10 (RTX 3080)
- **Total: $0.10 per video**

**Your $10 credits = ~100 videos!**

## 🎯 Alternative: Use Our Local Interface

I can build you a simple web interface that:
1. Uploads your files to RunPod automatically
2. Triggers generation
3. Downloads result
4. Manages pod start/stop

This makes it as easy as the D-ID interface you used earlier.

**Want me to build this automated interface?**

## Troubleshooting

### Pod won't start
- Try different GPU type
- Check if region has availability

### Out of credits
- Add more credits (minimum $10)
- Or use Vast.ai (cheaper but more complex)

### Generation fails
- Reduce frame count
- Use simpler prompt
- Try different workflow

## 📚 Resources

- RunPod Docs: https://docs.runpod.io/
- ComfyUI Guide: https://github.com/comfyanonymous/ComfyUI
- AnimateDiff Models: https://huggingface.co/guoyww/animatediff

---

**This is the most cost-effective solution:** $0.10 per video vs $1.50 with Runway ML!
