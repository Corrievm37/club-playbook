#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

# Ensure we're on main and have upstream set
branch=$(git rev-parse --abbrev-ref HEAD)
if [ "$branch" != "main" ]; then
  echo "Switching to main branch..."
  git checkout -B main
fi

# Pull latest (non-interactive); allow unrelated histories once
if ! git pull --no-rebase -X theirs --allow-unrelated-histories origin main >/dev/null 2>&1; then
  echo "Info: initial pull may require manual resolution later. Continuing watcher."
fi

# Commit and push helper
push_changes() {
  # Skip if nothing changed
  if git status --porcelain | grep -q .; then
    git add -A
    msg="Auto: $(date '+%Y-%m-%d %H:%M:%S')"
    git commit -m "$msg" || true
    git push -u origin main || true
    echo "Pushed at $(date '+%H:%M:%S')"
  fi
}

# Try fswatch if available for real-time watching; else poll every 5s
if command -v fswatch >/dev/null 2>&1; then
  echo "Watching with fswatch..."
  fswatch -or --event=Updated --event=Created --event=Removed --event=Renamed \
    --exclude=^\.git/ \
    --exclude=^node_modules/ \
    --exclude=^vendor/ \
    --exclude=^storage/ \
    --exclude=^public/build/ \
    . | while read -r _; do
      push_changes
    done
else
  echo "fswatch not found; polling every 5 seconds..."
  while true; do
    sleep 5
    push_changes
  done
fi
