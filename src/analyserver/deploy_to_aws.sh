#!/bin/bash

# #################################################################################################
#
# CAUTION: For some reason CodeDeploy has issues with file types other than *.zip from the command
#          line.  There are some topics on the AWS forums outlining this.  At the time of this 
#          development there didn't appear to be a solution so this deployment simply uses the
#          *.zip format to get around this issue.
#
# #################################################################################################

# #################################################################################################
# NOTE: Variables below are AWS specific variables.  These need to be updated for your specific
#       project for changes to apply
# #################################################################################################
#
CODE_DEPLOY_APP_NAME=DesIde-Cloud-Development
CODE_DEPLOY_DEVELOPMENT_GROUP=Development-Analyservers
S3_BUCKET=deside-cloud
S3_KEY=code-deploy/analyserverCode.zip
#
# #################################################################################################

# Check the environment variables to make sure that the AWS credentials have been set
if [[ -v "AWS_ACCESS_KEY_ID" ]] && [[ -v "AWS_SECRET_ACCESS_KEY" ]]
then
    echo "AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY found!"
else
    echo "AWS credentials not set in environment.  Please set AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY"
    echo "Quitting...."
    exit
fi

# Assemble full S3 path from bucket and key
S3_FULLPATH="s3://$S3_BUCKET/$S3_KEY"

# Push the latest code changes into the s3 bucket and register the revision
#resp=$(aws deploy push --application-name $CODE_DEPLOY_APP_NAME --ignore-hidden-files --s3-location $S3_FULLPATH --source `pwd`)
resp=$(aws deploy push --application-name $CODE_DEPLOY_APP_NAME --s3-location $S3_FULLPATH --source `pwd`)

# Get the metadata for the object pushed so we can create a deployment
obj_meta=$(aws s3api head-object --bucket $S3_BUCKET --key $S3_KEY --output json)

version=$(echo $obj_meta | jq '.VersionId')
eTag=$(echo $obj_meta | jq '.ETag')

# Tell AWS to deploy the code
aws deploy create-deployment --application-name $CODE_DEPLOY_APP_NAME --s3-location bucket=$S3_BUCKET,key=$S3_KEY,bundleType=zip,eTag=$eTag,version=$version --deployment-group-name=$CODE_DEPLOY_DEVELOPMENT_GROUP

