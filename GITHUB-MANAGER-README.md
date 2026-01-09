# GitHub Account Manager CLI Tool

A powerful command-line tool to manage multiple GitHub accounts with automatic SSH key generation, configuration, and easy switching between accounts.

## 🚀 Quick Start

### 1. Make the Tool Executable
```bash
chmod +x github-manager
```

### 2. Add Your GitHub Accounts
```bash
# Add your main account
./github-manager add kharis kharismcl@gmail.com

# Add your secondary account  
./github-manager add menchhub menchhub@gmail.com

# Set kharis as default
./github-manager default kharis
```

### 3. Add SSH Keys to GitHub
```bash
# Get public key for kharis (copies to clipboard)
./github-manager key kharis

# Get public key for menchhub (copies to clipboard)
./github-manager key menchhub
```

Then add each public key to the corresponding GitHub account at:
https://github.com/settings/keys

### 4. Test Connections
```bash
# Test all accounts
./github-manager test
```

## 📋 Available Commands

### Add a New Account
```bash
./github-manager add <account-name> <email>
# Example: ./github-manager add work work@company.com
```

### List All Accounts
```bash
./github-manager list
```

### Set Default Account
```bash
./github-manager default <account-name>
# Example: ./github-manager default kharis
```

### Switch Account for Current Repository
```bash
./github-manager switch <account-name>
# Example: ./github-manager switch menchhub
```

### Test SSH Connections
```bash
./github-manager test
```

### Show Public Key (Copies to Clipboard)
```bash
./github-manager key <account-name>
# Example: ./github-manager key kharis
```

### Remove an Account
```bash
./github-manager remove <account-name>
# Example: ./github-manager remove test
```

### Show Help
```bash
./github-manager help
```

## 🎯 Usage Examples

### Basic Workflow
```bash
# 1. Add accounts
./github-manager add personal personal@email.com
./github-manager add work work@company.com

# 2. Set default
./github-manager default work

# 3. Get keys and add to GitHub
./github-manager key work | pbcopy
# Then paste at: https://github.com/settings/keys

# 4. Test connection
./github-manager test
```

### Switching Between Projects
```bash
# For work projects
cd ~/projects/work-project
./github-manager switch work
git push origin main

# For personal projects  
cd ~/projects/personal-project
./github-manager switch personal
git push origin main
```

### Clone Repositories with Specific Accounts
```bash
# Clone using work account
git clone git@github.com-work:username/repo.git

# Clone using personal account
git clone git@github.com-personal:username/repo.git
```

## 🔧 Configuration Files

The tool manages these files automatically:

- `~/.github-manager/config.json` - Account configurations and settings
- `~/.ssh/config` - SSH host configurations for each account
- `~/.ssh/id_ed25519_*` - SSH key pairs for each account

## 🌟 Features

- ✅ **Automatic SSH Key Generation** - Creates secure ED25519 keys
- ✅ **SSH Config Management** - Automatically updates ~/.ssh/config
- ✅ **Git Configuration** - Sets local and global git user.email
- ✅ **Account Switching** - Easy switching between accounts per repository
- ✅ **Connection Testing** - Tests SSH connections to GitHub
- ✅ **Public Key Access** - Copies public keys to clipboard for easy GitHub setup
- ✅ **Safe Removal** - Proper account removal with safety precautions

## 🛠️ Installation Options

### Option 1: Use from Current Directory
```bash
# Run directly from project directory
./github-manager list
```

### Option 2: Install Globally (Recommended)
```bash
# Move to bin directory for global access
sudo mv github-manager /usr/local/bin/

# Now use from anywhere
github-manager list
```

### Option 3: Add to PATH
```bash
# Add current directory to PATH
export PATH=".:$PATH"

# Now use without ./
github-manager list
```

## 🔍 Troubleshooting

### SSH Connection Issues
```bash
# Test specific account
ssh -T git@github.com-kharis

# Check SSH config
cat ~/.ssh/config

# Verify keys exist
ls -la ~/.ssh/id_ed25519_*
```

### Permission Issues
```bash
# Ensure proper permissions
chmod 600 ~/.ssh/config
chmod 600 ~/.ssh/id_ed25519_*
chmod 644 ~/.ssh/id_ed25519_*.pub
```

### Tool Not Found
```bash
# Make executable
chmod +x github-manager

# Or install globally
sudo mv github-manager /usr/local/bin/
```

## 📝 Dependencies

- `jq` - JSON processing (install with `brew install jq`)
- `ssh-keygen` - SSH key generation (built into macOS)
- `git` - Version control system

## 🚨 Security Notes

- SSH keys are stored in `~/.ssh/` with proper permissions
- Private keys are never exposed or shared
- Public keys are safe to share with GitHub
- Configuration files are stored in user home directory

## 🔄 Updating

The tool is self-contained. To update, simply replace the `github-manager` file with a newer version.

## 🤝 Contributing

This is a standalone bash script. Feel free to modify and enhance as needed for your workflow.

## 📄 License

MIT License - Feel free to use and modify for your needs.

---

**Happy coding! 🎉**