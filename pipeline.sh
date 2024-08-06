pipeline {
    agent any

    parameters {
        string(name: 'BRANCH_NAME', defaultValue: 'main', description: 'The branch to build.')
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
                    sh "rsync -av --delete --ignore-existing ${WORKSPACE}/ ${WEB_DIR}/"
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
