# Pants ERP Deployment Guide

## 🚀 Production Deployment Checklist

### Pre-Deployment Requirements

#### System Requirements
- **Server**: 4 vCPUs, 8GB RAM, 100GB SSD minimum
- **OS**: Ubuntu 20.04 LTS or CentOS 8
- **Docker**: Version 20.10+
- **Docker Compose**: Version 2.0+
- **SSL Certificate**: Valid for your domain

#### Environment Setup
```bash
# 1. Clone repository
git clone <repository-url>
cd pants-erp

# 2. Copy environment file
cp .env.production .env

# 3. Generate application key
php artisan key:generate

# 4. Set environment variables
nano .env
```

#### Required Environment Variables
```env
APP_KEY=base64:your-generated-key
DB_PASSWORD=your-secure-db-password
DB_ROOT_PASSWORD=your-secure-root-password
```

### Docker Deployment

#### 1. Build and Deploy
```bash
# Build containers
docker-compose build

# Start services
docker-compose up -d

# Check status
docker-compose ps
```

#### 2. Database Setup
```bash
# Run migrations
docker-compose exec app php artisan migrate --force

# Seed initial data
docker-compose exec app php artisan db:seed --class=RoleAndPermissionSeeder
docker-compose exec app php artisan db:seed --class=DefaultStoreSeeder
docker-compose exec app php artisan db:seed --class=AdminUserSeeder
```

#### 3. Application Setup
```bash
# Clear and cache configurations
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### SSL Certificate Setup

#### Using Let's Encrypt (Certbot)
```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --standalone -d your-domain.com

# Copy certificates
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem ./ssl/cert.pem
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem ./ssl/key.pem

# Restart nginx
docker-compose restart nginx
```

### Monitoring Setup

#### Health Checks
```bash
# Test application health
curl -f http://localhost:8000/health

# Test database connection
docker-compose exec mysql mysqladmin ping

# Test redis connection
docker-compose exec redis redis-cli ping
```

#### Log Monitoring
```bash
# Application logs
docker-compose logs -f app

# Database logs
docker-compose logs -f mysql

# Nginx logs
docker-compose logs -f nginx
```

### Performance Optimization

#### Database Optimization
```sql
-- MySQL configuration (mysql.cnf)
[mysqld]
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
query_cache_size = 128M
max_connections = 200
```

#### PHP Optimization
```ini
; php.ini
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
opcache.memory_consumption = 256
opcache.max_accelerated_files = 10000
opcache.enable = 1
```

#### Nginx Optimization
- Enable gzip compression
- Configure static file caching
- Implement rate limiting
- Set appropriate worker processes

### Security Hardening

#### Application Security
```bash
# Set secure file permissions
chmod -R 755 storage/
chmod -R 644 .env

# Disable debug mode
APP_DEBUG=false

# Enable HTTPS redirect
# (configured in nginx.conf)
```

#### Database Security
```sql
-- Create dedicated database user
CREATE USER 'erp_user'@'%' IDENTIFIED BY 'strong-password';
GRANT SELECT, INSERT, UPDATE, DELETE ON pants_erp.* TO 'erp_user'@'%';
FLUSH PRIVILEGES;
```

#### Network Security
- Configure firewall (UFW/iptables)
- Limit SSH access
- Use VPN for admin access
- Enable fail2ban

### Backup Strategy

#### Database Backup
```bash
# Daily automated backup
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker-compose exec mysql mysqldump -u root -p$DB_ROOT_PASSWORD pants_erp > backup_$DATE.sql
aws s3 cp backup_$DATE.sql s3://your-backup-bucket/
```

#### File Backup
```bash
# Weekly file backup
tar -czf storage_backup_$DATE.tar.gz storage/
aws s3 cp storage_backup_$DATE.tar.gz s3://your-backup-bucket/
```

### Disaster Recovery

#### Backup Restoration
```bash
# Restore database
docker-compose exec -T mysql mysql -u root -p$DB_ROOT_PASSWORD pants_erp < backup_file.sql

# Restore files
tar -xzf storage_backup.tar.gz -C ./
```

#### High Availability Setup
- Load balancer configuration
- Database replication
- Multi-region deployment
- Auto-scaling groups

## 🔧 Post-Deployment Tasks

### User Setup
1. Create admin user accounts
2. Configure user roles and permissions
3. Set up store and warehouse data
4. Import initial product/material catalogs

### Integration Testing
1. Test all API endpoints
2. Verify mobile app functionality
3. Test offline sync capabilities
4. Validate reporting accuracy

### Training Materials
- User manuals for each role
- Video tutorials for mobile apps
- Troubleshooting guides
- FAQ documentation

### Go-Live Support
- 24/7 monitoring for first week
- Hotfix deployment process
- User support channels
- Performance monitoring

## 📊 Monitoring & Maintenance

### Application Monitoring
- Response time monitoring
- Error rate tracking
- Database performance
- Queue processing
- Storage usage

### Scheduled Maintenance
- Weekly security updates
- Monthly performance reviews
- Quarterly backup testing
- Semi-annual disaster recovery drills

### Scaling Considerations
- Database read replicas
- Redis clustering
- CDN for static assets
- Horizontal scaling for app servers

## 🆘 Troubleshooting

### Common Issues
1. **Database Connection Failed**
   - Check MySQL container status
   - Verify connection credentials
   - Check network connectivity

2. **High Memory Usage**
   - Monitor PHP processes
   - Check for memory leaks
   - Optimize database queries

3. **Slow Response Times**
   - Enable query logging
   - Check cache hit rates
   - Monitor server resources

4. **Offline Sync Issues**
   - Check network connectivity
   - Verify API endpoint status
   - Clear browser cache/storage

### Emergency Procedures
1. **Application Down**
   - Check container status
   - Restart affected services
   - Switch to backup server

2. **Database Corruption**
   - Stop application
   - Restore from latest backup
   - Verify data integrity

3. **Security Breach**
   - Isolate affected systems
   - Change all passwords
   - Review audit logs
   - Notify stakeholders

## 📞 Support Contacts
- Technical Support: support@your-company.com
- Emergency Hotline: +1-XXX-XXX-XXXX
- Documentation: docs.your-company.com