"""
Get the public DNS of one of the bastion servers.  This will be used to gain access to all
components within
"""
import sys
import boto3
import pprint

def get_bastion_dns(bastion_host_name: str) -> str:

    client = boto3.client('ec2')    
    resp   = client.describe_instances()
    
    for inst in resp['Reservations']:
        
        for val in inst['Instances']:
            pub_dns = val['PublicDnsName']
            state   = val['State']['Name']

            if 'Tags' not in val.keys():
                continue

            for tag in val['Tags']:
                if tag['Key'] == 'Name':
                    name = tag['Value']
                    break
            
            # This instance was not tagged with Name
            if name is None:
                continue
    
            # This instance is not a bastion host
            if bastion_host_name not in name:
                continue
            
            # This instance is a bastion host but is no longer running
            if state != "running":
                continue
    
            # Found an instance
            return pub_dns

    return None

if __name__=="__main__":
    print(get_bastion_dns(sys.argv[1]))