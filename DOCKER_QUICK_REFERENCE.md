# Docker 快速参考

## 🚀 快速启动

### 最简单的方式（SQLite）

```bash
# 1. 准备环境
cp .env.docker.example .env
php artisan key:generate  # 或手动编辑.env设置APP_KEY

# 2. 启动
docker compose up -d

# 3. 访问
http://localhost:8000
```

### 推荐方式（PostgreSQL）

```bash
# 1. 准备环境
cp .env.docker.example .env
# 编辑.env，取消注释PostgreSQL部分

# 2. 启动
docker compose --profile postgres up -d

# 3. 访问
http://localhost:8000
```

## 📋 常用命令

### 启动服务

```bash
# SQLite模式（默认）
docker compose up -d

# PostgreSQL模式
docker compose --profile postgres up -d

# MySQL模式
docker compose --profile mysql up -d

# 完整模式（所有服务）
docker compose --profile full up -d

# 带pgAdmin管理工具
docker compose --profile postgres --profile pgadmin up -d
```

### 停止服务

```bash
# 停止默认服务
docker compose down

# 停止所有服务
docker compose --profile full down

# 停止并删除volumes（清除所有数据）
docker compose --profile full down -v
```

### 查看日志

```bash
# 查看所有日志
docker compose logs

# 实时查看日志
docker compose logs -f

# 查看特定服务
docker compose logs app
docker compose logs postgres

# 最近100行
docker compose logs --tail=100
```

### 查看状态

```bash
# 查看运行的容器
docker compose ps

# 查看所有profile的容器
docker compose --profile full ps
```

### 重启服务

```bash
# 重启应用
docker compose restart app

# 重启数据库
docker compose restart postgres

# 重启所有
docker compose restart
```

## 🗄️ 数据库选项

### Option 1: SQLite（默认）

```bash
# .env配置
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# 启动
docker compose up -d
```

**优点:**
- 无需额外容器
- 快速启动
- 低资源占用

**缺点:**
- 并发性能较低
- 功能有限

### Option 2: PostgreSQL（推荐）

```bash
# .env配置
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=lusis_route
DB_USERNAME=postgres
DB_PASSWORD=secret

# 启动
docker compose --profile postgres up -d
```

**优点:**
- 生产级数据库
- 优秀的并发性能
- 完整的SQL功能
- 数据持久化

**访问数据库:**
```bash
# 使用psql
docker compose exec postgres psql -U postgres -d lusis_route

# 使用pgAdmin (需要启动pgadmin profile)
http://localhost:5050
```

### Option 3: MySQL

```bash
# .env配置
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lusis_route
DB_USERNAME=laravel
DB_PASSWORD=secret

# 启动
docker compose --profile mysql up -d
```

**优点:**
- 广泛使用
- 成熟稳定
- 工具丰富

**访问数据库:**
```bash
docker compose exec mysql mysql -u root -p
```

## 🛠️ 维护命令

### 进入容器

```bash
# 进入应用容器
docker compose exec app bash

# 进入PostgreSQL容器
docker compose exec postgres bash

# 进入MySQL容器
docker compose exec mysql bash
```

### 运行Artisan命令

```bash
# 运行迁移
docker compose exec app php artisan migrate

# 回滚迁移
docker compose exec app php artisan migrate:rollback

# 清除缓存
docker compose exec app php artisan cache:clear

# 查看路由
docker compose exec app php artisan route:list

# 进入tinker
docker compose exec app php artisan tinker
```

### 数据库备份

```bash
# PostgreSQL备份
docker compose exec postgres pg_dump -U postgres lusis_route > backup.sql

# PostgreSQL恢复
cat backup.sql | docker compose exec -T postgres psql -U postgres lusis_route

# MySQL备份
docker compose exec mysql mysqldump -u root -p lusis_route > backup.sql

# MySQL恢复
cat backup.sql | docker compose exec -T mysql mysql -u root -p lusis_route
```

### 查看Docker volumes

```bash
# 列出所有volumes
docker volume ls | grep lusis

# 查看volume详情
docker volume inspect lusis-postgres-data
docker volume inspect lusis-mysql-data
docker volume inspect lusis-redis-data

# 清理未使用的volumes
docker volume prune
```

## 🔧 故障排查

### 容器无法启动

```bash
# 查看详细日志
docker compose logs app

# 检查容器状态
docker compose ps

# 重新构建
docker compose build --no-cache app
docker compose up -d
```

### 数据库连接失败

```bash
# 检查数据库容器状态
docker compose ps postgres

# 查看数据库日志
docker compose logs postgres

# 测试数据库连接
docker compose exec postgres pg_isready -U postgres

# 重启数据库
docker compose restart postgres
```

### 权限问题

```bash
# 修复storage权限
docker compose exec app chmod -R 775 storage bootstrap/cache

# 重新构建并启动
docker compose down
docker compose up -d --build
```

### 端口冲突

```bash
# 修改.env中的端口
APP_PORT=8001
DB_PORT=5433

# 重启服务
docker compose down
docker compose up -d
```

## 📊 监控和调试

### 实时监控

```bash
# 查看容器资源使用
docker stats

# 查看特定容器
docker stats lusis-route-app

# 查看容器内进程
docker compose top
```

### 健康检查

```bash
# 查看健康状态
docker compose ps

# 测试应用健康
curl http://localhost:8000

# 测试数据库健康
docker compose exec postgres pg_isready -U postgres
```

## 🎯 生产部署建议

### 最小化部署（单机）

```bash
# PostgreSQL + 应用
docker compose --profile postgres up -d

# 配置建议
APP_DEBUG=false
LOG_LEVEL=warning
```

### 完整部署（推荐）

```bash
# 所有服务
docker compose --profile full up -d

# 启用服务
# - 应用服务器
# - PostgreSQL数据库
# - Redis缓存
# - pgAdmin管理工具
```

### 安全建议

1. **修改默认密码**
```bash
DB_PASSWORD=<strong-random-password>
DB_ROOT_PASSWORD=<strong-random-password>
PGADMIN_PASSWORD=<strong-random-password>
```

2. **使用环境变量文件**
```bash
# 不要提交.env到git
echo ".env" >> .gitignore
```

3. **限制网络访问**
```yaml
# 仅暴露必要端口
ports:
  - "127.0.0.1:8000:8000"  # 仅本地访问
```

4. **定期备份**
```bash
# 自动备份脚本
#!/bin/bash
docker compose exec postgres pg_dump -U postgres lusis_route > backup_$(date +%Y%m%d).sql
```

## 📚 Profile组合使用

### 开发环境

```bash
# 完整开发栈
docker compose --profile full up -d
```

包含:
- Laravel应用
- PostgreSQL数据库
- Redis缓存
- pgAdmin管理工具

### 测试环境

```bash
# 简单测试栈
docker compose --profile postgres up -d
```

包含:
- Laravel应用
- PostgreSQL数据库

### 生产环境

```bash
# 生产栈（不含管理工具）
docker compose --profile postgres --profile redis up -d
```

包含:
- Laravel应用
- PostgreSQL数据库
- Redis缓存

## 🔗 相关文档

- **DOCKER_GUIDE.md** - 完整Docker部署指南
- **docker-compose.yml** - Docker Compose配置文件
- **Dockerfile** - Docker镜像定义
- **.env.docker.example** - Docker环境变量示例
- **POSTGRESQL_SETUP.md** - PostgreSQL详细配置
- **DEPLOYMENT.md** - 完整部署指南

## 💡 快速参考表

| 任务 | 命令 |
|------|------|
| 启动（SQLite） | `docker compose up -d` |
| 启动（PostgreSQL） | `docker compose --profile postgres up -d` |
| 启动（MySQL） | `docker compose --profile mysql up -d` |
| 启动（全部） | `docker compose --profile full up -d` |
| 停止 | `docker compose down` |
| 查看日志 | `docker compose logs -f` |
| 进入容器 | `docker compose exec app bash` |
| 运行迁移 | `docker compose exec app php artisan migrate` |
| 查看状态 | `docker compose ps` |
| 重启 | `docker compose restart` |
| 重建 | `docker compose up -d --build` |
| 清理 | `docker compose down -v` |

---

**提示:** 始终使用 `docker compose` 而不是 `docker-compose` (Docker Compose V2)
