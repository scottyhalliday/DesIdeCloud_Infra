#!/bin/bash

#
# DESIDE CLOUD
# Scott Hall
#
# An automatic integrated design environment infrastructure builder for cloud based application.  This
# script will build the AWS cloud infrastructure for a secure design environment that can be customized
# and maintained by any developer to meet their design needs.
#
# Deside Cloud takes care of the cumbersome implementation of cloud infrastructure and security and allows
# you, the developer, to focus on the application itself.  Being in the cloud you will also have complete
# control over scalability and size to meet your computation and analysis needs.
#
# This script will handle the installation of the AWS infrastructure and help you to customize the application
# to your needs.  Of course you can accept the defaults and change things later but the point is, is that it
# is all up to you.  

# #############################################################################
# VARIABLES
# #############################################################################

# TODO: Use these variables to construct the terreform variables.tf file

terraform_var_file="SMH__terraform_vars.tfvars"

# Bastion host names
bastion_az1_host_name="DesIde-Cloud1 Bastion"

# RDS database identifier
rds_db_identifier="deside-cloud-mysql-db"
rds_db_username="deside_admin"
rds_db_password="reset_this_password"
rds_db_name="DesideCloud"

# TODO: Update sample code to reflect inputs

# #############################################################################
# BUILD APPLICATION
# #############################################################################

#echo ""
#echo "BUILD DesIde Cloud AMI's"
#echo ""
#
#cd packer
#packer build deside_cloud_ami.json
#packer build deside_cloud_ami_bastion.json
#cd ..
#
#echo ""
#echo "Build the infrastructure"
#echo ""
#
#cd terraform
#terraform apply --var-file=$terraform_var_file
#
## Sleep for 60 seconds to ensure services are online and initialized
#echo -n "Sleeping for 60 seconds to give AWS resources time to fully initialize "
#time_cnt="0"
#while [ $time_cnt -lt 60 ]
#do
#    echo -n "."
#    sleep 1
#    time_cnt=$[$time_cnt+1]
#done
#echo "DONE!"

# Deploy code to front end webservers
#cd ..
cd src/authserver
./deploy_to_aws.sh

# Deploy code the the analysis servers (analyserver)
cd ../..
cd src/analyserver
./deploy_to_aws.sh

# Add some content to the database

# Get the public DNS of a bastion server to do this
cd ../..
pub_dns=$(python install/get_bastion_dns.py $bastion_az1_host_name)

# Get the database endpoint
rds=$(python install/get_rds_endpoint.py $rds_db_identifier)

echo "Bastion Server Public DNS Name: $pub_dns"
echo "RDS Endpoint                  : $rds"

# Secure copy the python file to setup a sample database
scp install/setup_database.py ubuntu@$pub_dns:setup_database.py
ssh ubuntu@$pub_dns chmod 755 setup_database.py 
ssh ubuntu@$pub_dns python3 setup_database.py $rds_db_username $rds_db_password $rds $rds_db_name


