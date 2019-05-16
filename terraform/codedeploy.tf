#
# Manages the CodeDeploy infrastructure for deploying application code to 
# respective EC2 Instances.
#

resource "aws_codedeploy_app" "development" {
  compute_platform = "Server"
  name             = "DesIde-Cloud-Development"
}
