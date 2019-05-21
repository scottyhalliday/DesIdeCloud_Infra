#
# Manages the IAM policies and roles for CodeDeploy
#

#
# In order for CodeDeploy to work properly a series of IAM credentials must exist.  
# An IAM user needs to be created to serve as the deployer.  This user must have 
# the permissions to interact with Autoscaling, CodeDeploy, EC2, AWS Lambda, ECS and
# IAM.  
#
# Next a service role needs to be created.  The service role is used to grant
# permissions to an AWS service so that it can access AWS resources.  The policies
# attached to the service role determine which AWS resources the service can access
# and what it can do with those services.  In our case we need to create the role
# such that it allows CodeDeploy to assume and gain the policies associated.
#
# Finally, an IAM instance profile for the EC2 instances is needed so that the EC2
# instance can pull the code from the S3 bucket during deployment.  This is configured
# in a seperate piece of terraform code, 'iam_ec2.tf'

# Create a CodeDeploy User
resource "aws_iam_user" "code_deploy_user" {
  name = "DesIde-Cloud-Code-Deployer"

  tags = {
    Description = "This is the user account for deploying code on EC2 instances via CodeDeploy -- DO NOT DELETE"
  }
}

# Get the access keys for CodeDeploy user
# TODO: Should provide PGP key so secret access key is not visible in state file
resource "aws_iam_access_key" "code_deploy_user_keys" {
  user = "${aws_iam_user.code_deploy_user.name}"
}

# Create an INLINE access policy so that the CodeDeployer user can make actual deployments.
# This will also include the ability to deploy to Lambda's and EC2
# NOTE:  There can be some lag before this shows up on the console
resource "aws_iam_user_policy" "code_deploy_user_policy" {
  name = "DC_CodeDeployUserPolicy"
  user = "${aws_iam_user.code_deploy_user.name}"

  policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement" : [
    {
      "Effect" : "Allow",
      "Action" : [
        "autoscaling:*",
        "codedeploy:*",
        "ec2:*",
        "lambda:*",
        "ecs:*",
        "elasticloadbalancing:*",
        "iam:AddRoleToInstanceProfile",
        "iam:CreateInstanceProfile",
        "iam:CreateRole",
        "iam:DeleteInstanceProfile",
        "iam:DeleteRole",
        "iam:DeleteRolePolicy",
        "iam:GetInstanceProfile",
        "iam:GetRole",
        "iam:GetRolePolicy",
        "iam:ListInstanceProfilesForRole",
        "iam:ListRolePolicies",
        "iam:ListRoles",
        "iam:PassRole",
        "iam:PutRolePolicy",
        "iam:RemoveRoleFromInstanceProfile", 
        "s3:*"
      ],
      "Resource" : "*"
    }    
  ]
}
EOF
}

# Create the role which allows the AWS service (CodeDeploy) to access AWS resources
resource "aws_iam_role" "code_deploy_role" {
  name        = "DC_CodeDeployRole"
  description = "Allow CodeDeploy to call AWS services needed to deploy code"

  assume_role_policy = <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "",
            "Effect": "Allow",
            "Principal": {
                "Service": [
                    "codedeploy.amazonaws.com"
                ]
            },
            "Action": "sts:AssumeRole"
        }
    ]
}
EOF
}

# Attach the AWS managed policy for CodeDeploy to the role
resource "aws_iam_role_policy_attachment" "code_deploy_policy" {
  role       = "${aws_iam_role.code_deploy_role.name}"
  policy_arn = "arn:aws:iam::aws:policy/service-role/AWSCodeDeployRole"
}
