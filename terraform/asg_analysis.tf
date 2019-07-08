#
# Use an autoscaling group (ASG) to create the individual users analysis
# screen
#

# Create the ASG launch configuration using the custom built AMI that
# contains installed software to run CodeDeploy and other items
resource "aws_launch_configuration" "analyserver_launch_config" {
  name_prefix          = "deside-cloud-analyserver-lc-"
  image_id             = "${data.aws_ami.deside_cloud_ami.id}"
  instance_type        = "${var.analyserver_instance}"
  key_name             = "${var.key_name}"
  security_groups      = ["${aws_security_group.sg_front_end_http.id}"]
  iam_instance_profile = "${aws_iam_instance_profile.ec2_iam_profile.name}"
}

# Create an autoscaling group for Availability Zone 1.  To keep services
# quickly available once logged in always have an instance waiting to be 
# grabbed.  This is not the most cost effective solution but I leave it
# to the application developers to come up with a more effective way to
# handle this for their needs.
resource "aws_autoscaling_group" "analyserver_front_end1" {
  name_prefix          = "deside-cloud-webserver1-analyserver-asg-"
  max_size             = 5
  min_size             = 0
  desired_capacity     = 1
  launch_configuration = "${aws_launch_configuration.analyserver_launch_config.name}"
  vpc_zone_identifier  = ["${aws_subnet.az1_public.id}"]

  # Create tags for the EC2 instances.  They will all have this name depending how
  # many are created
  tag {
    key                 = "Name"
    value               = "Decide-Cloud-az1-asg-analyserver"
    propagate_at_launch = true
  }

  depends_on = ["aws_subnet.az1_public"]
}
