pipeline {
    agent { label 'loansworker' }

    stages {
        stage('Build') {
            environment {
                RUN_TESTS = true
            }
            steps {
                ansiColor('xterm') {
                    sshagent (credentials: ['bb68a6f9-817a-44d2-b3d6-2b04f304210e']) {
                        sh './ci/run.sh'
                    }
                }
            }
        }
    }

    post {
        cleanup {
            cleanWs()
        }
    }
}
