# DesIdeCloud

## Deployment

# Installation

Installing DesIde Cloud can be done by simply executing the script, **install_deside_cloud.sh** located
in the projects root directory.

```bash
./install_deside_cloud.sh
```

This script will handle all of the setup for you.  It assumes that you have not individually installed items
on your own.  It also assumes that the Amazon Machine Images (AMI) for DesIde Cloud have not already been created.
If they have you must either comment out the code in the script that creates them or go into your AWS account
and Deregister the images.  

In order to effectively run this script you must have some third party software installed.  This includes the 
following:
1. MySQL
2. HashiCorp Packer
3. HashiCorp Terraform
4. AWS CLI

This script will perform the following tasks:
1. Build AMI for webserver instances (Packer)
2. Build AMI for bastion hosts (Packer)
3. Build AWS infrastructure for application (Terraform)
4. Deploy code to front end login webserver autoscaling groups via AWS CodeDeploy
5. Deploy code to analysis webserver autoscaling groups via AWS CodeDeploy
6. Create a sample project database table

# AWS EC2 Webserver Instance (Packer)

DesIde Cloud infrastructure requires EC2 instances to have several third party dependencies installed
so that the application can perform as required.  This includes a webserver (apache), php, AWS CodeDeployAgent,
etc.  To make things simple and to avoid delays installing items for on-demand services DesIde Cloud uses
HashiCorp's Packer to build an EC2 instance for our needs.  This AMI needs to be created prior to building
the infrastructure for DesIde Cloud.  The instance can be built via the following code:

```bash
packer build deside_cloud_ami.json
```

# AWS EC2 Bastion Server (Packer)

DesIde Cloud creates a bastion server to act as an avenue for the developer to interact with all active
webservers.  For security, the webservers do not allow SSH from outside the VPC.  As a developer you will
need to get into these webservers for various reasons and the bastion server allows this.  The bastion
instance is a customized AMI which includes tools needed to interact with the infrastructure.  This
includes MySQL, Python, AWS, etc.  The instance can be built via the following code:

```bash
packer build deside_cloud_ami_bastion.json
```

# NOTE
These AMI's must not already exist in your AWS account.  If they do you need to deregister prior to building
new image.

# AWS Infrastructure (Terraform)
```bash
export AWS_ACCESS_KEY_ID=XXXXXXXXX
export AWS_SECRET_ACCESS_KEY=XXXXXXXXX
terraform plan --var-file=file_name
terraform apply --var-file=file_name
```

# Post Infrastructure build

Once the AWS infrastructure is built software needs to be deployed to these instances.  For instance
the webserver front-ends are blank and do not contain any code for the webpages.  Additionally, 
the database will be an empty.  You, as the developer, will need to decide how your database will
be structured to meet your applications needs.  

## Bastion Hosts

Each availability zone public subnet will contain a bastion server which will serve as a means to
log into the webserver and user instances in case debugging or investigations are necessary.  The
only route into these instances is by HTTP or SSH from bastion host.  This attempts to eliminate
vunerabilities.  

To log into bastion host you must first have your public key on the server.  At initial setup this
public key should be some sort of administrator key so that if multiple users need to be added 
this can be done after the infrastructure is built.  Second, you will need to ensure that your 
SSH credentials are forward to bastion host so that the administrator can log into web server and
user instances further down the line.  Note the **-A** option for ssh.

```bash
ssh -A ubuntu@<public dns>
```  