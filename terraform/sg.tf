#
# Manage security groups for front end infrastructure
#

resource "aws_security_group" "sg_front_end_http" {
  name        = "sg_front_end_http"
  description = "Allow port 80 (http) access to the front end application"
  vpc_id      = "${aws_vpc.deside_vpc.id}"

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["${var.az1_public_cidr}"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

resource "aws_security_group" "sg_ssh_bastion" {
  name        = "sg_ssh_bastion"
  description = "Allow ssh access from bastion server"
  vpc_id      = "${aws_vpc.deside_vpc.id}"

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = "${var.bastion_admin_cidr}"
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}
