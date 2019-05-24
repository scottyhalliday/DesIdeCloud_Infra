#
# Create and manage SNS related resources
#

# Create a topic which indicates when an EC2 instance has been started
# This will allow code to be deployed
resource "aws_sns_topic" "ec2_started" {
  name = "topic_ec2_started"
}
