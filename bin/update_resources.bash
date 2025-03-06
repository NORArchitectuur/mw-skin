#!/bin/bash
CURRENT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TARGET_DIR="$CURRENT_DIR/../resources"
CSS_DIR="$TARGET_DIR/css"

# Clone the nora-components repository
cd /tmp && git clone git@github.com:RGRMdesign/nora-components.git && cd nora-components/dist

# Copy the base CSS
cp mw-nora-components.css "$CSS_DIR"
cp mw-custom.css "$CSS_DIR"

# Navigate to the resources directory
cd skins/NORA/resources

# Find all directories and copy them to the target
for dir in */; do
    if [ -d "$dir" ]; then
        # Remove trailing slash from directory name
        dirname=${dir%/}

        # Create target directory if it doesn't exist
        mkdir -p "$TARGET_DIR/$dirname"

        # Copy all contents from the source directory
        cp -r "$dir"/* "$TARGET_DIR/$dirname/"

        echo "Copied $dirname resources to $TARGET_DIR/$dirname"
    fi
done


rm -rf /tmp/nora-components
