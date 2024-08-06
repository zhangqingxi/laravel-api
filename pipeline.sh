pipeline {
    agent any

    parameters {
        string(name: 'BRANCH_NAME', defaultValue: 'main', description: 'The branch to build.')
        string(name: 'API_URL', defaultValue: 'https://laravel-api.145238.xyz', description: 'The Web Api Url.')
        string(name: 'WS_URL', defaultValue: 'https://laravel-api.145238.xyz/ws', description: 'The WebSocket Url.')
        string(name: 'CORS_URLS', defaultValue: 'https://laravel-web.145238.xyz,http://localhost:5173', description: 'The Cors Urls.')
    }

    environment {
        WEB_DIR = '/www/wwwroot/jenkins/laravel-api'
        ADMIN_DB_CONNECTION = 'admin'
        ADMIN_DB_HOST = '127.0.0.1'
        ADMIN_DB_PORT = '3306'
        ADMIN_DB_DATABASE = 'laravel'
        ADMIN_DB_USERNAME = 'laravel'
        ADMIN_DB_PASSWORD = '123456'
        ADMIN_DB_PREFIX = 'qasim_'
        LOCK_FILE = "${WEB_DIR}/commands.lock"
        SECRET_VALUE = "jenkins145238"

        # 定义密钥内容（将实际的公钥和私钥内容替换到下面的字符串中）
        PRIVATE_KEY_CONTENT = '''-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDnNsyFr1kkbeJq
E0uug7FRkyBzeDIcByYFG2cGHarn3BtSeelIwNlsGskc8Ug9Y57DGkhvW0aUoZ48
PUMTvVGAlGMwpHtubGxjA+qh5/oV58SnrQgvP8tpljfeaJ7MR9z/mzSHe23rbBO7
Avb5uzY5HykEMLuAUplrUTn4mbGRaWTHP/wBX8n6ZLjG7ooddgioTJIDlQdz7uYf
SCVfC1TQx0/jTx0msZXJGlRgL5wQraXjzHHbLXklPQ9CsGrWLvZattoYfsa544+G
znm+dPZAI3dfIrR4WE16Nbp+IejuHVRhdtYgnkdGiP3ZtvcqLoync+wIq1SdTMxJ
v4nNLjYVAgMBAAECggEANMkr9DFOFqUHldQF6Tejmc4PEI2WUxPGuE/DO4A2bmyv
yCqQ+c0dVjLfSz5CkdFWg5BSjSm3eWNUbshFshH8s3AtHQPQMiuKqyhPvtrpG2so
WGeGgNL9q4NiCeK0rkqRx8E1QuEI/UBVWNKac/UrserNQGmnxOoWoIuQwpLEGUAZ
bRkLugXl/GD0nWeA4P7z8j8pM/Bd+WiRYnbRznFBwKF2GJiR6TPDYMDqK6EgGSfR
p9rVktX9SvTYxkVBflXnWHmAWejk+I4WUHSDfeQNK9uq/8V6VK2W7Ii9u/NjJ2cm
Ih0CBBxfPnl/UXkcINydcTMPKowJhkeAD1G5/i7cgQKBgQD0cWbRmvsINFXb18eT
7x1se9wdttQfnunN/dzblvRUMVBg5TGnTVzcQj84N64n3SGTaI620tXqMf2iu5nY
r2rdxJuERcsOtLGFdS82QThiY1wzk5BxIK5w2abUuBIcCNu+qXM64Ziks4Mdnq1l
dWiB6akgGZ7Tn9vYuWFCX4+gxQKBgQDyJUgyDAZgwVHQilaFyz9xNPmvnldoIHc9
Gs+mohZiFV2zcuWJQW8Qb9Ig587C+JZki+6fqYab1b9pU3vdwB3CCZW55Hmh460L
ojjGi4OiaFAy3api7MLzsHnqhLds5RR04VUVBB9p7PmGqQNh1TOTNskdE4G9jdUK
CFAobSj1EQKBgQDEeTGvX+ZN3eL05SchHJyFybhOmdk94TkDvPx784VG9qwodY/H
bUMqNoUh5n8ww2htVyj+gdA2gR/gYPFzXOuEfLaTRpXDqDDXmCzuatWrHznIqt76
Ts5nHkodyCWmKuiVAxX/SbyrBS5p7FRqrEfnw6uQhYdHicHnHHm0I6MKEQKBgFzw
Vk9vMht816wVDLR5mDYZQSRYv++lVl7LpZutZq3qyXYQyWzvAnPql1ot3rwSsZaR
5tq9NH8ngTop4sbv7/wDtcYNnWm3ezmV3/jEn5d8Nhvv3G/vs7vYplKO8eAH5Nwf
iIpydKQxYmx+/l9Ud2Evi+1vCUL55yTcn/eEPk4hAoGAdd1SQae5Iq3YGW+WSiCw
B+I0Xa5o8Lk71Ii/caZkfC2hcwHFBu+fQ56d6xeqhGAePpgJ3JD5pRmpwdZ6yfc0
llZxCSbKhWkfsszIl0WDaDSmtOHHJvg0vFHiJx2srEW3eadVCgCvPZ5J+yrffimy
wdZQz+gIkHWpZ4n4zq2XikU=
-----END PRIVATE KEY-----
        '''

        PUBLIC_KEY_CONTENT = '''-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5zbMha9ZJG3iahNLroOx
UZMgc3gyHAcmBRtnBh2q59wbUnnpSMDZbBrJHPFIPWOewxpIb1tGlKGePD1DE71R
gJRjMKR7bmxsYwPqoef6FefEp60ILz/LaZY33miezEfc/5s0h3tt62wTuwL2+bs2
OR8pBDC7gFKZa1E5+JmxkWlkxz/8AV/J+mS4xu6KHXYIqEySA5UHc+7mH0glXwtU
0MdP408dJrGVyRpUYC+cEK2l48xx2y15JT0PQrBq1i72WrbaGH7GueOPhs55vnT2
QCN3XyK0eFhNejW6fiHo7h1UYXbWIJ5HRoj92bb3Ki6Mp3PsCKtUnUzMSb+JzS42
FQIDAQAB
-----END PUBLIC KEY-----
'''
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    echo "Checking out repository from branch ${params.BRANCH_NAME}..."
                    git url: 'https://github.com/zhangqingxi/laravel-api.git', branch: params.BRANCH_NAME
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    echo "Deploying new code..."
                    sh """
                        rsync -avz --checksum --exclude='.git/' --exclude='commands.lock' --exclude='.user.ini' --exclude='storage/' --exclude='public/admin/' --exclude='public/chunks/' ${WORKSPACE}/ ${WEB_DIR}/
                    """
                    # -a 归档模式，保留文件属性，并递归复制目录
                    # -v 详细模式，显示进度信息
                    # -z 选项启用压缩，以减少传输的数据量
                    # --ignore-existing 忽略已存在的目标文件
                    # --checksum 通过比较文件的校验和（hash）来决定是否更新文件
                    # --exclude 排除目录
                    # --delete 删除目标目录中在源目录中不存在的文件
                }
            }
        }

        stage('Setup Directories') {
            steps {
                script {
                    echo "Creating directories..."

                    def storageDir = "${WEB_DIR}/storage"

                    // 创建目录
                    sh "mkdir -p ${storageDir}/laravels"
                    sh "mkdir -p ${storageDir}/logs"
                    sh "mkdir -p ${storageDir}/keys"
                }
            }
        }

        stage('Create Keys') {
            steps {
                script {
                    echo "Creating and writing keys..."

                    def keysDir = "${WEB_DIR}/storage/keys"

                    # 创建公钥和私钥文件，并写入内容
                    sh """
                        echo "${PRIVATE_KEY_CONTENT}" > "${keysDir}/admin_private.pem"
                        echo "${PUBLIC_KEY_CONTENT}" > "${keysDir}/admin_public.pem"
                    """
                }
            }
        }

        stage('Install Composer Dependencies') {
            steps {
                script {
                    def installCmd = "cd ${WEB_DIR} && composer install --no-dev --optimize-autoloader"
                    if (!isCommandExecuted(installCmd)) {
                        echo "Installing Composer dependencies..."
                        sh installCmd
                        recordCommand(installCmd)
                    } else {
                        echo "Composer dependencies already installed. Skipping..."
                    }
                }
            }
        }

        stage('Setup Environment') {
            steps {
                script {
                    def envFile = "${WEB_DIR}/.env"
                    def setupEnvCmd = "cp ${WEB_DIR}/.env.example ${envFile}"
                    def keyGenerateCmd = "php ${WEB_DIR}/artisan key:generate"

                    echo "Setting up environment..."

                    if (!isCommandExecuted(setupEnvCmd)) {
                        sh setupEnvCmd
                        recordCommand(setupEnvCmd)
                    } else {
                        echo ".env file already copied. Skipping..."
                    }

                    if (!isCommandExecuted(keyGenerateCmd)) {
                        sh keyGenerateCmd
                        recordCommand(keyGenerateCmd)
                    } else {
                        echo "Application key already generated. Skipping..."
                    }

                    // Replace contents of .env file
                    sh """
                        sed -i 's/^ADMIN_DB_CONNECTION=.*/ADMIN_DB_CONNECTION=${ADMIN_DB_CONNECTION}/' ${envFile}
                        sed -i 's/^ADMIN_DB_HOST=.*/ADMIN_DB_HOST=${ADMIN_DB_HOST}/' ${envFile}
                        sed -i 's/^ADMIN_DB_PORT=.*/ADMIN_DB_PORT=${ADMIN_DB_PORT}/' ${envFile}
                        sed -i 's/^ADMIN_DB_DATABASE=.*/ADMIN_DB_DATABASE=${ADMIN_DB_DATABASE}/' ${envFile}
                        sed -i 's/^ADMIN_DB_USERNAME=.*/ADMIN_DB_USERNAME=${ADMIN_DB_USERNAME}/' ${envFile}
                        sed -i 's/^ADMIN_DB_PASSWORD=.*/ADMIN_DB_PASSWORD=${ADMIN_DB_PASSWORD}/' ${envFile}
                        sed -i 's/^ADMIN_DB_PREFIX=.*/ADMIN_DB_PREFIX=${ADMIN_DB_PREFIX}/' ${envFile}

                        sed -i 's#^APP_URL=.*\$#APP_URL=${params.API_URL}#' ${envFile}
                        sed -i 's#^ADMIN_URL=.*\$#ADMIN_URL=${params.API_URL}#' ${envFile}
                        sed -i 's#^ADMIN_WEBSOCKET_URL=.*\$#ADMIN_WEBSOCKET_URL=${params.WS_URL}#' ${envFile}
                        sed -i 's#^ALLOWED_ORIGINS=.*\$#ALLOWED_ORIGINS=${params.CORS_URLS}#' ${envFile}
                    """
                }
            }
        }

        stage('Run Migrations') {
            steps {
                script {
                    def migrationCmd = "cd ${WEB_DIR} && php artisan migrate:fresh --path=/database/migrations/admin"
                    if (!isCommandExecuted(migrationCmd)) {
                        echo "Running database migrations..."
                        sh migrationCmd
                        recordCommand(migrationCmd)
                    } else {
                        echo "Database migrations already run. Skipping..."
                    }
                }
            }
        }

        stage('Seed Database') {
            steps {
                script {
                    def seedCmds = [
                        "cd ${WEB_DIR} && php artisan db:seed --class=AdminSeeder",
                        "cd ${WEB_DIR} && php artisan db:seed --class=RoleSeeder",
                        "cd ${WEB_DIR} && php artisan db:seed --class=MenuSeeder",
                        "cd ${WEB_DIR} && php artisan db:seed --class=RoleAdminSeeder",
                        "cd ${WEB_DIR} && php artisan db:seed --class=RoleMenuSeeder"
                    ]
                    def allSeeded = true
                    seedCmds.each { seedCmd ->
                        if (!isCommandExecuted(seedCmd)) {
                            echo "Seeding database..."
                            sh seedCmd
                            recordCommand(seedCmd)
                            allSeeded = false
                        }
                    }
                    if (allSeeded) {
                        echo "Database already seeded. Skipping..."
                    }
                }
            }
        }

        stage('Completed successfully') {
            steps {
                script {
                    echo "Notifying Supervisor to restart services..."
                    sh """
                        ${SUPERVISOR_CMD} restart all
                    """
                }
            }
        }
    }

    post {
        success {
            echo "Build and deployment completed successfully."
        }
        failure {
            echo "Build or deployment failed."
        }
        always {
            cleanWs() // Clean up the workspace
        }
    }

}

// Define the helper methods in a script block
def isCommandExecuted(cmd) {
    return sh(script: "grep -Fxq '${cmd}' ${LOCK_FILE}", returnStatus: true) == 0
}

def recordCommand(cmd) {
    sh(script: "echo '${cmd}' >> ${LOCK_FILE}")
}
