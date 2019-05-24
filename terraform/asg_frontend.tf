#
# Use an autoscaling group (ASG) to create the application front end
# (i.e. login screen, contact, etc)
#

# Create the ASG launch configuration using the custom built AMI that
# contains installed software to run CodeDeploy and other items
resource "aws_launch_configuration" "webserver_launch_config" {
  name_prefix          = "deside-cloud-lc-"
  image_id             = "${data.aws_ami.deside_cloud_ami.id}"
  instance_type        = "${var.webserver_instance}"
  key_name             = "${var.key_name}"
  security_groups      = ["${aws_security_group.sg_front_end_http.id}"]
  iam_instance_profile = "${aws_iam_instance_profile.ec2_iam_profile.name}"
}

# Create an autoscaling group for Availability Zone 1
resource "aws_autoscaling_group" "webserver_front_end1" {
  name_prefix          = "deside-cloud-webserver1-asg-"
  max_size             = 1
  min_size             = 1
  desired_capacity     = 1
  launch_configuration = "${aws_launch_configuration.webserver_launch_config.name}"
  vpc_zone_identifier  = ["${aws_subnet.az1_public.id}"]

  # Create tags for the EC2 instances.  They will all have this name depending how
  # many are created
  tag {
    key                 = "Name"
    value               = "Decide-Cloud-az1-asg-webserver"
    propagate_at_launch = true
  }
}

#resource "aws_autoscaling_lifecycle_hook" "asg_codedeploy_hook" {
#  name                    = "asg-codedeploy-hook"
#  autoscaling_group_name  = "${aws_autoscaling_group.webserver_front_end1.name}"
#  default_result          = "CONTINUE"
#  lifecycle_transition    = "autoscaling:EC2_INSTANCE_LAUNCHING"
#  notification_target_arn = "${aws_sns_topic.ec2_started.arn}"
#  role_arn                = "${aws_iam_role.asg_sns_role.arn}"
#}

