pipeline {
    agent {
        node {
            label 'ALT_Linux' // Указываем метку агента, который будет использоваться для сборки
        }
    }
    environment {
        // Определяем переменные окружения для учетных данных и сервера
        DOCKERHUB_CREDENTIALS = credentials('docker-hub-credentials')
        SSH_KEY_PATH = credentials('ssh-key-server-test')
        GITHUB_CREDENTIALS = credentials('github-credentials-id')
        SERVER = credentials('ip-server-test')
    }
    stages {
        stage('Clone repository') {
            steps {
                // Клонируем репозиторий с GitHub для начала работы
                git(credentialsId: 'github-credentials-id', url: 'https://github.com/SuNRiZs/devops-test-repo.git', branch: 'main')
            }
        }
        stage('Build, Test and Push Image') {
            steps {
                script {
                    // Строим Docker образ и тегируем его номером сборки
                    def myImage = docker.build("sunraize/test-php:${BUILD_NUMBER}")
            
                    // Запускаем контейнер для тестирования образа и проверяем что такого контейнера не запущено, елси есть останавливаем и удаляем
                    sh "docker stop webtest || true"
                    sh "docker rm webtest || true"
                    sh "docker run -d -p \"8080:80\" --name webtest sunraize/test-php:${BUILD_NUMBER}"
            
                    // Проверяем работоспособность веб-сервера в контейнере
                    def testResult = sh script: 'curl -f http://localhost:8080/', returnStatus: true
            
                    // В случае ошибки останавливаем и удаляем контейнер, сообщаем об ошибке
                    if (testResult != 0) {
                        sh "docker stop webtest || true"
                        sh "docker rm webtest || true"
                        sh "docker rmi sunraize/test-php:${BUILD_NUMBER}"
                        error('Тестирование образа завершилось неудачей')
                    }
            
                    // При успешном тестировании пушим образ в Docker Hub
                    withCredentials([usernamePassword(credentialsId: 'docker-hub-credentials', usernameVariable: 'USERNAME', passwordVariable: 'PASSWORD')]) {
                        sh "docker login -u ${USERNAME} -p ${PASSWORD}"
                        myImage.push("latest")
                        myImage.push("${env.BUILD_NUMBER}")
                        sh "docker logout"
                    }
                    // Очищаем рабочее пространство, удаляя тестовый контейнер
                    sh "docker stop webtest || true"
                    sh "docker rm webtest || true"
                }
            }
        }
        stage('Deploy to server') {
            steps {
                // Разворачиваем образ на сервере с помощью SCP и SSH
                sh """
                    scp -i \${SSH_KEY_PATH} -P 40022 docker-compose.yml devops@\${SERVER}:/home/devops/
                    scp -i \${SSH_KEY_PATH} -P 40022 -r ./database/ devops@\${SERVER}:/home/devops/
                    ssh -i \${SSH_KEY_PATH} -p 40022 devops@\${SERVER} '
                    sed -i "s/image: sunraize\\/test-php:.*/image: sunraize\\/test-php:${BUILD_NUMBER}/" /home/devops/docker-compose.yml && mkdir -p /home/devops/data && docker-compose -f /home/devops/docker-compose.yml pull && docker-compose -f /home/devops/docker-compose.yml up -d --no-deps --build web
                    '
                """
            }
        }
        stage('Clean up Docker images') {
            steps {
                script {
                    sh "docker rmi sunraize/test-php:${BUILD_NUMBER}"
                    sh """
                        ssh -i ${SSH_KEY_PATH} -p 40022 devops@${SERVER} '
                            docker images | grep "sunraize/test-php" | sort -r | tail -n +4 | awk "{print \\\$1\\\":\\\"\\\$2}" | xargs -r docker rmi
                        '
                    """
                }
            }
        }
    }
    post {
        always {
            // Очищаем рабочее пространство после сборки
            cleanWs()
        }
    }
}
