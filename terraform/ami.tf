#
# Fetches the custom DesIde Cloud AMI.  NOTE: This must exist prior to building 
# infrastructure
#

# Get the custom AMI built by Packer for webserver instances.  TODO: Make this filter a bit more refined
data "aws_ami" "deside_cloud_ami" {
  most_recent = true

  filter {
    name   = "name"
    values = ["DesIde-Cloud WebServer AMI"]
  }

  #owners = ["${data.aws_caller_identity.current.account_id}"]
}

# Get the custom AMI built by Packer for bastion instances.  TODO: Make this filter a bit more refined
data "aws_ami" "deside_cloud_ami_bastion" {
  most_recent = true

  filter {
    name   = "name"
    values = ["DesIde-Cloud Bastion AMI"]
  }

  #owners = ["${data.aws_caller_identity.current.account_id}"]
}
