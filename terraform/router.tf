#
# Creates the router table and approriate route table entries
#

#resource "aws_route_table" "rtb_webserver_1" {
#  vpc_id = "${aws_vpc.deside_vpc.id}"
#
#  route {
#    cidr_block = "0.0.0.0/0"
#    gateway_id = "${aws_internet_gateway.deside_igw.id}"
#  }
#
#  depends_on = ["aws_internet_gateway.deside_igw"]
#}

# Update the VPC's default route table to include the internet gateway
resource "aws_default_route_table" "vpc_rtb" {
  default_route_table_id = "${aws_vpc.deside_vpc.default_route_table_id}"

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = "${aws_internet_gateway.deside_igw.id}"
  }
}

#resource "aws_route" "route_webserver_1" {
#  route_table_id         = "${aws_route_table.rtb_webserver_1.id}"
#  destination_cidr_block = "${var.az1_public_cidr}"
#  depends_on             = ["aws_route_table.rtb_webserver_1"]
#}


#resource "aws_route_table_association" "route_association_webserver_1" {
#  subnet_id      = "${aws_subnet.az1_public.id}"
#  route_table_id = "${aws_route_table.rtb_webserver_1.id}"
#  depends_on     = ["aws_route_table.rtb_webserver_1"]
#}

