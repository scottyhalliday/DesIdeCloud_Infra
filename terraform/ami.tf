#
# Fetches the custom DesIde Cloud AMI.  NOTE: This must exist prior to building 
# infrastructure
#

# Get the custom AMI built by Packer.  TODO: Make this filter a bit more refined
data "aws_ami" "deside_cloud_ami" {
  most_recent = true

  filter {
    name   = "name"
    values = ["DesIde*"]
  }

  #owners = ["${data.aws_caller_identity.current.account_id}"]
}
