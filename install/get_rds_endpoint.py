"""
Get the RDS endpoint 
"""
import sys
import boto3
import pprint

def get_rds_endpoint(rds_identifier: str) -> str:

    client = boto3.client('rds')    
    resp   = client.describe_db_instances()

    for db in resp['DBInstances']:
        
        # This database does not belong to DesIde Cloud
        if db['DBInstanceIdentifier'] != rds_identifier:
            continue

        return db['Endpoint']['Address']

if __name__=='__main__':
    print(get_rds_endpoint(sys.argv[1]))