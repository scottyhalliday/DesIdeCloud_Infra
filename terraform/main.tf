#
# Create the infrastructure for the main application interface
#

provider "aws" {
  region      = "us-east-1"
  version     = "1.60.0"
  max_retries = 5
}

terraform {
  backend "s3" {
    bucket = "deside-cloud"
    key    = "terraform-state/deside-cloud-terraform.tfstate"
    region = "us-east-1"
  }
}

# Create an internet gateway
resource "aws_internet_gateway" "deside_igw" {
  vpc_id = "${aws_vpc.deside_vpc.id}"

  tags = {
    Name = "DesIde-Cloud Internet Gateway"
  }
}

# EC2 Instance for the main login page
resource "aws_instance" "deside_cloud_front_end1" {
  ami           = "${var.ec2_instance_main}"
  instance_type = "${var.webserver_instance}"

  vpc_security_group_ids = ["${aws_security_group.sg_front_end_http.id}"]

  tags = {
    Name = "DesIde-Cloud1 Webserver"
  }

  subnet_id = "${aws_subnet.az1_public.id}"
  key_name  = "${var.key_name}"

  #  # Bootstrap the instance and get web server up and running
  #  provisioner "remote-exec" {
  #    inline = [
  #      "sudo apt update",
  #      "sudo apt install apache2 -y",
  #      "sudo apt install php -y",
  #      "sudo service apache2 start",
  #    ]
  #
  #    # Will need ssh credentials to log in (Default user is 'ubuntu')
  #    connection {
  #      type        = "ssh"
  #      user        = "ubuntu"
  #      private_key = "${file(var.decrypted_private_ssh_key)}"
  #    }
  #  }

  # Let's be explicit on what we need prior to building ec2
  depends_on = ["aws_security_group.sg_front_end_http",
    "aws_instance.deside_cloud_bastion1",
    "aws_internet_gateway.deside_igw",
  ]
}

# SSH Bastion Host
resource "aws_instance" "deside_cloud_bastion1" {
  ami = "${var.ec2_instance_bastion}"

  subnet_id     = "${aws_subnet.az1_public.id}"
  instance_type = "${var.bastion_instance}"

  vpc_security_group_ids = ["${aws_security_group.sg_ssh_bastion.id}"]

  tags = {
    Name = "DesIde-Cloud1 Bastion"
  }

  key_name = "${var.key_name}"

  # Let's be explicit on what we need prior to building ec2
  depends_on = ["aws_security_group.sg_ssh_bastion"]
}
