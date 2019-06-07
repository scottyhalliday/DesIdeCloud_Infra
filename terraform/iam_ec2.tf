#
# Manages the IAM policies and roles for EC2 Instances
#

# Create a policy which allows an EC2 instance to get/list from S3 buckets.  This policy
# originates around the need for the EC2 instance to accept code deployment from CodeDeploy
# TODO: Narrow this policy down to what it is actually needed for.
#{
#  "Version": "2012-10-17",
#  "Statement": [
#    {
#      "Effect": "Allow",
#      "Action": [
#        "s3:Get*",
#        "s3:List*"
#      ],
#      "Resource": [
#        "arn:aws:s3:::replace-with-your-s3-bucket-name/*",
#        "arn:aws:s3:::aws-codedeploy-us-east-2/*",
#        "arn:aws:s3:::aws-codedeploy-us-east-1/*",
#        "arn:aws:s3:::aws-codedeploy-us-west-1/*",
#        "arn:aws:s3:::aws-codedeploy-us-west-2/*",
#        "arn:aws:s3:::aws-codedeploy-ca-central-1/*",
#        "arn:aws:s3:::aws-codedeploy-eu-west-1/*",
#        "arn:aws:s3:::aws-codedeploy-eu-west-2/*",
#        "arn:aws:s3:::aws-codedeploy-eu-west-3/*",
#        "arn:aws:s3:::aws-codedeploy-eu-central-1/*",
#        "arn:aws:s3:::aws-codedeploy-ap-east-1/*",
#        "arn:aws:s3:::aws-codedeploy-ap-northeast-1/*",
#        "arn:aws:s3:::aws-codedeploy-ap-northeast-2/*",
#        "arn:aws:s3:::aws-codedeploy-ap-southeast-1/*",        
#        "arn:aws:s3:::aws-codedeploy-ap-southeast-2/*",
#        "arn:aws:s3:::aws-codedeploy-ap-south-1/*",
#        "arn:aws:s3:::aws-codedeploy-sa-east-1/*"
#      ]
#    }
#  ]
#}
#resource "aws_iam_policy" "ec2_codedeploy_policy" {
#  name        = "DC_ec2_s3_bucket_get"
#  description = "Allow the ec2 instance to access s3 buckets needed for CodeDeploy"
#
#  policy = <<EOF
#{
#    "Version": "2012-10-17",
#    "Statement": [
#        {
#            "Action": [
#                "s3:Get*",
#                "s3:List*"
#            ],
#            "Effect": "Allow",
#            "Resource": "*"
#        }
#    ]
#}
#EOF
#}

# TODO: Not just codedeploy needs policies.  Lump it all together with other things
resource "aws_iam_policy" "ec2_codedeploy_policy" {
  name        = "DC_ec2_s3_bucket_get"
  description = "Allow the ec2 instance to access s3 buckets needed for CodeDeploy"

  policy = <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Action": [
                "s3:*",
                "ec2:*",
                "rds:*",
                "autoscaling:*"
            ],
            "Effect": "Allow",
            "Resource": "*"
        }
    ]
}
EOF
}

# Create a role which can use this policy.  Allow an EC2 instance to assume this role
# by adding EC2 service to the trust entities
resource "aws_iam_role" "ec2_codedeploy_role" {
  name = "DC_ec2_codedeploy_role"

  assume_role_policy = <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "",
            "Effect": "Allow",
            "Principal": {
                "Service": "ec2.amazonaws.com"
            },
            "Action": "sts:AssumeRole"
        }
    ]
}
EOF
}

# Attach the DC_ec2_s3_bucket_get policy to this role
resource "aws_iam_role_policy_attachment" "ec2_codedeploy_role_policy" {
  role       = "${aws_iam_role.ec2_codedeploy_role.name}"
  policy_arn = "${aws_iam_policy.ec2_codedeploy_policy.arn}"
}

# Create an instance profile.  An instance profile is a container for an IAM role that
# we can use to pass role information to an EC2 instance when the instance starts
resource "aws_iam_instance_profile" "ec2_iam_profile" {
  name = "ec2_iam_instance_profile"
  role = "${aws_iam_role.ec2_codedeploy_role.name}"
}

# Create a notification role for Autoscaling groups at startup
#resource "aws_iam_policy" "asg_sns_policy" {
#  name        = "DC_sns_sqs_asg"
#  description = "Allow the autoscaling group to publish messages to SNS"
#
#  policy = <<EOF
#{
#    "Version": "2012-10-17",
#    "Statement": [
#        {
#            "Effect": "Allow",
#            "Resource": "*",
#            "Action": [
#                "sqs:SendMessage",
#                "sqs:GetQueueUrl",
#                "sns:Publish"
#            ]
#        }
#    ]
#}
#EOF
#}
#
#resource "aws_iam_role" "asg_sns_role" {
#  name = "DC_asg_sns_role"
#
#  assume_role_policy = <<EOF
#{
#    "Version": "2012-10-17",
#    "Statement": [
#        {
#            "Sid": "",
#            "Effect": "Allow",
#            "Principal": {
#                "Service": "autoscaling.amazonaws.com"
#            },
#            "Action": "sts:AssumeRole"
#        }
#    ]
#}
#EOF
#}
#
## Attach the DC_ec2_s3_bucket_get policy to this role
#resource "aws_iam_role_policy_attachment" "asg_sns_role_policy" {
#  role       = "${aws_iam_role.asg_sns_role.name}"
#  policy_arn = "${aws_iam_policy.asg_sns_policy.arn}"
#}

