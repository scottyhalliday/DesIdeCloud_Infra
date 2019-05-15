#
# Create a VPC for application
#

resource "aws_vpc" "deside_vpc" {
  cidr_block           = "172.32.0.0/16"
  enable_dns_support   = "true"
  enable_dns_hostnames = "true"

  tags = {
    Name = "DesIde Cloud"
  }
}
