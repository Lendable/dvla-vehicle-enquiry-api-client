pipeline {
    agent { label 'loansworker' }

    parameters {
        booleanParam(name: 'RUN_TESTS', defaultValue: true, description: 'Set RUN_TESTS variable')
    }

    stages {
        stage('Build') {
            environment {
                GITHUB_TOKEN = credentials('jx-lendable-token')
                VAULT_ADDR = "https://vault.lendable.co.uk:8200/"
            }
            steps {
                ansiColor('xterm') {
                    sshagent (credentials: ['bb68a6f9-817a-44d2-b3d6-2b04f304210e']) {
                        getSecret("secret/loans/dvla_vehicle_enquiry_api_client/config_test.json", "file", "/app/config_test.json")
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

def String getVaultFile(String secretPath, String field, String targetDirectory) {
    sh(returnStatus: true, script: """
        vault login -method=aws region=eu-west-1 role=loans-role-iam >> /dev/null 2>&1
        vault kv get -format=table -field=${field} ${secretPath} > ${targetDirectory}
    """)
}
