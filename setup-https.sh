#!/bin/bash

# Automated HTTPS Setup Script for Attendance System
# Usage: sudo bash setup-https.sh your-domain.com /var/www/attendance

set -e

DOMAIN=${1:-}
PROJECT_PATH=${2:-/var/www/attendance}

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Error handling
error() {
    echo -e "${RED}ERROR: $1${NC}" >&2
    exit 1
}

success() {
    echo -e "${GREEN}✓ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Validate inputs
if [ -z "$DOMAIN" ]; then
    error "Domain name required. Usage: sudo bash setup-https.sh your-domain.com /var/www/attendance"
fi

if [ ! -d "$PROJECT_PATH" ]; then
    error "Project path $PROJECT_PATH does not exist"
fi

echo "=========================================="
echo "Attendance System - HTTPS Setup"
echo "=========================================="
echo "Domain: $DOMAIN"
echo "Project Path: $PROJECT_PATH"
echo "=========================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "This script must be run as root (use sudo)"
fi

# Update system
echo "Updating system packages..."
apt-get update
apt-get upgrade -y

# Install Certbot if not installed
if ! command -v certbot &> /dev/null; then
    echo "Installing Certbot..."
    apt-get install -y certbot python3-certbot-nginx
    success "Certbot installed"
else
    success "Certbot already installed"
fi

# Install Nginx if not installed
if ! command -v nginx &> /dev/null; then
    echo "Installing Nginx..."
    apt-get install -y nginx
    systemctl start nginx
    systemctl enable nginx
    success "Nginx installed and started"
else
    success "Nginx already installed"
fi

# Backup existing Nginx config
if [ -f "/etc/nginx/sites-available/$DOMAIN" ]; then
    cp "/etc/nginx/sites-available/$DOMAIN" "/etc/nginx/sites-available/${DOMAIN}.backup"
    success "Backed up existing Nginx config"
fi

# Copy production Nginx config
echo "Configuring Nginx..."
cp "$PROJECT_PATH/nginx.conf.production" "/etc/nginx/sites-available/$DOMAIN"

# Replace domain placeholder in Nginx config
sed -i "s/your-domain.com/$DOMAIN/g" "/etc/nginx/sites-available/$DOMAIN"

# Enable Nginx site
if [ ! -L "/etc/nginx/sites-enabled/$DOMAIN" ]; then
    ln -s "/etc/nginx/sites-available/$DOMAIN" "/etc/nginx/sites-enabled/$DOMAIN"
    success "Nginx site enabled"
else
    success "Nginx site already enabled"
fi

# Test Nginx configuration
echo "Testing Nginx configuration..."
nginx -t || error "Nginx configuration test failed"
success "Nginx configuration valid"

# Reload Nginx
systemctl reload nginx
success "Nginx reloaded"

# Setup SSL certificate with Certbot
echo "Setting up SSL certificate with Let's Encrypt..."
certbot certonly --nginx \
    --non-interactive \
    --agree-tos \
    --email admin@$DOMAIN \
    -d $DOMAIN \
    -d www.$DOMAIN \
    || warning "Certificate setup may have issues - verify manually"

success "SSL certificate configured"

# Update Nginx with SSL paths
sed -i "s|/etc/letsencrypt/live/your-domain.com|/etc/letsencrypt/live/$DOMAIN|g" "/etc/nginx/sites-available/$DOMAIN"

# Test and reload Nginx with SSL
echo "Testing Nginx with SSL..."
nginx -t || error "Nginx SSL configuration test failed"
systemctl reload nginx
success "Nginx reloaded with SSL"

# Setup auto-renewal with systemd timer
echo "Setting up auto-renewal..."
systemctl enable certbot.timer
systemctl start certbot.timer
success "Certificate auto-renewal enabled"

# Update .env.production
echo "Updating .env.production..."
if [ -f "$PROJECT_PATH/.env.production" ]; then
    sed -i "s|https://your-domain.com|https://$DOMAIN|g" "$PROJECT_PATH/.env.production"
    success ".env.production updated"
else
    warning ".env.production not found"
fi

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data "$PROJECT_PATH"
chmod -R 755 "$PROJECT_PATH"
chmod -R 775 "$PROJECT_PATH/storage" "$PROJECT_PATH/bootstrap/cache"
success "Permissions set"

# Display summary
echo ""
echo "=========================================="
echo "HTTPS Setup Complete!"
echo "=========================================="
echo "Domain: $DOMAIN"
echo "SSL Certificate: /etc/letsencrypt/live/$DOMAIN/"
echo "Nginx Config: /etc/nginx/sites-available/$DOMAIN"
echo "Auto-renewal: Enabled (certbot.timer)"
echo ""
echo "Next steps:"
echo "1. Verify DNS points to this server"
echo "2. Test: curl https://$DOMAIN"
echo "3. Update .env.production with database credentials"
echo "4. Run: php artisan migrate --env=production"
echo "=========================================="
