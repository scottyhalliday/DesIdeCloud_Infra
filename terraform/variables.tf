#
# Includes variables and data for building the main infrastructure
#

# Ubuntu 18.04
variable "ec2_instance_main" {
  default     = "ami-0a313d6098716f372"
  description = "EC2 Instance for the main application front end"
}

# Ubuntu 18.04
variable "ec2_instance_bastion" {
  default     = "ami-0a313d6098716f372"
  description = "EC2 Instance for the ssh bastion host"
}

# For ssh access the bastion host, enter the key pair name 
variable "key_name" {
  #default     = "siemens_public_key"
  description = "The name of the public key a stored AWS account for your ssh private key"
}

# Location of your ssh public key.  MUST BE DECRYPTED KEY AS TERRAFORM DOESN'T DO ENCYRPTED KEYS
variable "decrypted_private_ssh_key" {
  description = "The file path location of your decrypted ssh private key that matches the public key given by key_name"
}

# VPC ID where you want the DesIde Cloud to live
variable "deside_cloud_vpc" {
  description = "The VPC ID where you want the main application to be installed.  Recommend creating a seperate VPC for this application.  Also ensure you enable DNS hostname"
  default     = "vpc-03af4fdae0d93dee5"
}

# VPC CIDR for supplied vpc id
variable "deside_cloud_vpc_cidr" {
  description = "The VPC CIDR block for the VPC ID provided"
  default     = "172.32.0.0/16"
}

# Availability zone 1
variable "az1" {
  description = "The availability zone 1 for application.  There will be two for redundancy and availability"
  default     = "us-east-1a"
}

# Availability zone 2
variable "az2" {
  description = "The availability zone 2 for application.  There will be two for redundancy and availability"
  default     = "us-east-1b"
}

# Public subnet cidr block in availability zone 1
variable "az1_public_cidr" {
  description = "The cidr block for the public subnet in availability zone 1"
  default     = "172.32.1.0/24"
}

# Private subnet cidr block in availability zone 1
variable "az1_private_cidr" {
  description = "The cidr block for the private subnet in availability zone 1"
  default     = "172.32.2.0/24"
}

# Public subnet cidr block in availability zone 2
variable "az2_public_cidr" {
  description = "The cidr block for the public subnet in availability zone 2"
  default     = "172.32.3.0/24"
}

# Private subnet cidr block in availability zone 2
variable "az2_private_cidr" {
  description = "The cidr block for the private subnet in availability zone 2"
  default     = "172.32.4.0/24"
}

# EC2 Instance types
variable "webserver_instance" {
  description = "The instance type for the webserver"
  default     = "t2.micro"
}

# EC2 Instance types
variable "bastion_instance" {
  description = "The instance type for the bastion server"
  default     = "t2.micro"
}

# Admin CIDR Block
variable "bastion_admin_cidr" {
  description = "The CIDR blocks for allowed admin locations.  Can be set to 0.0.0.0/0 but then anyone can ssh into the server"
  default     = ["0.0.0.0/0"]
}

# The name of the front end web server in availability zone 1
variable "front_end_webserver1" {
  description = "The name to give to the web server EC2 instance in availability zone 1"
  default     = "DesIde-Cloud1 Webserver"
}

# The name of the end application
variable "database_table_name" {
  description = "The database name used when creating database instance"
  default     = "DesideCloud"
}

# The database instance
variable "database_instance" {
  description = "The database instance size.  See AWS documentation for available sizes"
  default     = "db.t2.micro"
}
