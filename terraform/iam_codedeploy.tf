#
# Manages the IAM policies and roles for CodeDeploy
#

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
