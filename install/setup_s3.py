"""
Create S3 buckets
"""
import boto3;
from botocore.exceptions import ClientError

def create_bucket(bucket_name):
    
    try:
        s3 = boto3.client('s3')
        s3.create_bucket(Bucket=bucket_name)

    except ClientError as e:
        print(f'S3 Create Bucket Error')
        print(e)
        return False

    return True

def create_folder(bucket_name, folder_name):

    try:
        s3 = boto3.client('s3')
        s3.put_object(Bucket=bucket_name, Key=folder_name)

    except ClientError as e:
        print(f'S3 Create Folder Error')
        print(e)
        return False

    return True

def setup_deside_s3_structure(bucket):
    '''
    Setup the S3 environment that the demo Deside Cloud uses 
    '''
    create_bucket(bucket)
    create_folder(bucket, 'terraform-state/')
    create_folder(bucket, 'code-deploy/')
    create_folder(bucket, 'cases/')
    create_folder(bucket, 'cases/analyst1/')
    create_folder(bucket, 'cases/analyst2/')

if __name__=='__main__':
    # You will want to ensure that you use a unique name as this could be already taken
    bucket = 'create-a-delete-me-bucket-123456789'
    key    = 'test-key/'
    key2    = 'test-key/test-key2'

    create_bucket(bucket)
    create_folder(bucket, key)
    create_folder(bucket, key2)