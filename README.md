# Laravel 11 项目

本项目基于 Laravel 11 和 PHP 8.2 构建，包含多种中间件、功能模块和 API 接口，支持 Swoole 和 WebSocket 功能。

## Web
- [laravel-web](https://github.com/zhangqingxi/laravel-web)

## 功能模块

### 中间件

- **加密解密中间件**：用于对请求和响应的数据进行加密解密处理。
- **Null 转空字符串中间件**：将请求中的 Null 值转为空字符串。
- **日志中间件**：记录请求和响应的日志。
- **请求频率中间件**：限制请求频率，防止滥用。
- **CORS 中间件**：处理跨域请求。
- **请求重复检查中间件**：防止重复提交请求。
- **动态路由中间件**：支持动态路由配置。
- **权限中间件**：基于角色和权限的访问控制。

### 功能

- **Laravel Swoole 启动项目**：通过 Swoole 启动项目，提高性能。
- **WebSocket 异地登录监控**：实时监控用户异地登录情况。
- **观察者和事件**：记录操作日志。
- **Sanctum Token**：用于 API 认证。

### 基本接口

- **用户接口**
- **菜单接口**
- **角色接口**
- **日志接口**
- **回收站接口**
- **文件接口**
- **邮件接口**

## 安装步骤

1. 克隆仓库
    ```
    git clone https://https://github.com/zhangqingxi/Laravel11-Admin.git
    cd Laravel11-Admin
   
2. 安装依赖
    ```
    composer install
   ```
    注意：
    需要php开启安全函数 putenv、proc_open
3. 复制并修改环境配置文件
    ```
    cp .env.example .env
    php artisan key:generate
   ```

4. 配置数据库
    ```
    php artisan migrate:fresh --path=/database/migrations/admin
    php artisan db:seed --class=AdminSeeder
    php artisan db:seed --class=RoleSeeder
    php artisan db:seed --class=MenuSeeder
    php artisan db:seed --class=RoleAdminSeeder
    php artisan db:seed --class=RoleMenuSeeder
   ```

5. 启动服务
    ```
    php bin/laravels start
   ```

6. 补充
   1. 在storage目录下补充laravels、logs、keys目录
   2. 在storage/keys目录下 补充公钥、私钥文件 
   如：admin_private.pem admin_public.pem
   3. 如果运行失败，检查目录权限、日志

###  部署
#### Supervisord
通过Supervisord监管主进程，前提是命令不可以设置在后台运行
1. 启动项目
    ```
    [program:laravel-swoole]
    directory=[项目目录]
    command=/usr/local/bin/php bin/laravels start -i
    numprocs=1
    autostart=true
    autorestart=true
    redirect_stderr=true
    startretries=3
    startsecs=3
    user=www
    redirect_stderr=true
    stdout_logfile=/path/to/your/laravel-project/storage/logs/swoole.log
    ```

2. 队列
    ```
    [program:laravel-queue]
    directory=[项目目录]
    command=/usr/local/bin/php [项目目录]/artisan queue:work --tries=3 --queue=admin
    numprocs=1
    autostart=true
    autorestart=true
    redirect_stderr=true
    startretries=3
    startsecs=3
    user=www
    redirect_stderr=true
    stdout_logfile=/path/to/your/laravel-project/storage/logs/queue.log
    ```

3. 定时任务
    ```
    [program:laravel-schedule]
    directory=[项目目录]
    command=/usr/bin/php [项目目录]/artisan schedule:work
    numprocs=1
    autostart=true
    autorestart=true
    redirect_stderr=true
    startretries=3
    startsecs=3
    user=www
    redirect_stderr=true
    stdout_logfile=/path/to/your/laravel/application/storage/logs/schedule.log
    ```

#### Nginx
1. 反向代理
    ```
    map $http_upgrade $connection_upgrade {
        default upgrade;
        ''      close;
    }
    upstream swoole {
        # 通过 IP:Port 连接
        server 127.0.0.1:5200 weight=5 max_fails=3 fail_timeout=30s;
        # 通过 UnixSocket Stream 连接，小诀窍：将socket文件放在/dev/shm目录下，可获得更好的性能
        #server unix:/yourpath/laravel-s-test/storage/laravels.sock weight=5 max_fails=3 fail_timeout=30s;
        #server 192.168.1.1:5200 weight=3 max_fails=3 fail_timeout=30s;
        #server 192.168.1.2:5200 backup;
        keepalive 16;
    }
    server {
        listen 80;
        # 别忘了绑Host
        server_name laravels.com;
        root /yourpath/laravel-s-test/public;
        access_log /yourpath/log/nginx/$server_name.access.log  main;
        autoindex off;
        index index.html index.htm;
        # Nginx处理静态资源(建议开启gzip)，LaravelS处理动态资源。
        location / {
            try_files $uri @laravels;
        }
        # 当请求PHP文件时直接响应404，防止暴露public/*.php
        #location ~* \.php$ {
        #    return 404;
        #}
        # Http和WebSocket共存，Nginx通过location区分
        # !!! WebSocket连接时路径为/ws
        # Javascript: var ws = new WebSocket("ws://laravels.com/ws");
        location =/ws {
            # proxy_connect_timeout 60s;
            # proxy_send_timeout 60s;
            # proxy_read_timeout：如果60秒内被代理的服务器没有响应数据给Nginx，那么Nginx会关闭当前连接；同时，Swoole的心跳设置也会影响连接的关闭
            # proxy_read_timeout 60s;
            proxy_http_version 1.1;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Real-PORT $remote_port;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header Host $http_host;
            proxy_set_header Scheme $scheme;
            proxy_set_header Server-Protocol $server_protocol;
            proxy_set_header Server-Name $server_name;
            proxy_set_header Server-Addr $server_addr;
            proxy_set_header Server-Port $server_port;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection $connection_upgrade;
            proxy_pass http://swoole;
        }
        location @laravels {
            # proxy_connect_timeout 60s;
            # proxy_send_timeout 60s;
            # proxy_read_timeout 60s;
            proxy_http_version 1.1;
            proxy_set_header Connection "";
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Real-PORT $remote_port;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header Host $http_host;
            proxy_set_header Scheme $scheme;
            proxy_set_header Server-Protocol $server_protocol;
            proxy_set_header Server-Name $server_name;
            proxy_set_header Server-Addr $server_addr;
            proxy_set_header Server-Port $server_port;
            proxy_pass http://swoole;
        }
    }
    ```
