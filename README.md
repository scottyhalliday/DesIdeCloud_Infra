# DesIdeCloud

## Deployment

```bash
export AWS_ACCESS_KEY_ID=XXXXXXXXX
export AWS_SECRET_ACCESS_KEY=XXXXXXXXX
terraform plan --var-file=file_name
terraform apply --var-file=file_name
```

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