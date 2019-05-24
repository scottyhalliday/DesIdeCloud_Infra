#
# Manages the CodeDeploy infrastructure for deploying application code to 
# respective EC2 Instances.
#

# Create the code deploy application.  The CodeDeploy application is the top level item 
# for the CodeDeploy process.  The Application isa name that uniquely identifies the
# application you want to deploy.  CodeDeploy uses this name, which functions as a 
# container, to ensure that the correct comination of revision, deployment configuration
# and deployment group are referenced during a deployment.
#
# There are three different possible compute platforms, EC2/On-Premises, AWS Lambda and 
# Amazon ECS.  This application will be installed on an EC2 instance so 'Server' is 
# selected in the terraform
resource "aws_codedeploy_app" "development" {
  compute_platform = "Server"
  name             = "DesIde-Cloud-Development"
}

# Create the Development code deployment group.  A deployment group is a set of individual
# instances.  This deployment group will be tied to the autoscaling group which once the
# initial code-deploy is called will result in the ASG to always deploy code to new instances
resource "aws_codedeploy_deployment_group" "development" {
  app_name              = "${aws_codedeploy_app.development.name}"
  deployment_group_name = "Development"
  service_role_arn      = "${aws_iam_role.code_deploy_role.arn}"
  autoscaling_groups    = ["${aws_autoscaling_group.webserver_front_end1.name}"]

  deployment_style {
    deployment_option = "WITHOUT_TRAFFIC_CONTROL"
    deployment_type   = "IN_PLACE"
  }

  #  ec2_tag_set {
  #    ec2_tag_filter {
  #      key   = "Name"
  #      type  = "KEY_AND_VALUE"
  #      value = "${var.front_end_webserver1}"
  #    }
  #  }

  # Create a trigger to catch when the EC2 instance is ready
  #trigger_configuration {
  #  trigger_events     = ["InstanceSuccess"]
  #  trigger_name       = "trigger-front-end-webserver"
  #  trigger_target_arn = "${aws_sns_topic.ec2_started.arn}"
  #}
}

# TODO: Create production deployment group

