#
# Manage the subnets for each availability zone
#

# Contains the bastion, web and run instances in availibility zone 1
resource "aws_subnet" "az1_public" {
  vpc_id                  = "${aws_vpc.deside_vpc.id}"
  availability_zone       = "${var.az1}"
  cidr_block              = "${var.az1_public_cidr}"
  map_public_ip_on_launch = "true"

  tags = {
    Name = "DesIde Cloud AZ1 Public Subnet"
  }
}

# Contains the database for availability zone 1
resource "aws_subnet" "az1_private" {
  vpc_id            = "${aws_vpc.deside_vpc.id}"
  availability_zone = "${var.az1}"
  cidr_block        = "${var.az1_private_cidr}"

  tags = {
    Name = "DesIde Cloud AZ1 Private Subnet"
  }
}

## Contains the bastion, web and run instances in availibility zone 2
#resource "aws_subnet" "az2_public" {
#  vpc_id            = "${var.deside_cloud_vpc}"
#  availability_zone = "${var.az2}"
#  cidr_block        = "${var.az2_public_cidr}"
#
#  tags = {
#    Name = "DesIde Cloud AZ2 Public Subnet"
#  }
#}
#
## Contains the database for availability zone 1
#resource "aws_subnet" "az2_private" {
#  vpc_id            = "${var.deside_cloud_vpc}"
#  availability_zone = "${var.az2}"
#  cidr_block        = "${var.az2_private_cidr}"
#
#  tags = {
#    Name = "DesIde Cloud AZ2 Private Subnet"
#  }
#}

