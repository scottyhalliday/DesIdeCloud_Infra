#
# Build the infrastructure of an RDS database
#
#
## Create a database subnet group
#resource "aws_db_subnet_group" "db_subnets" {
#  name       = "deside-db-group"
#  subnet_ids = ["${aws_subnet.az1_private.id}", "${aws_subnet.az2_private.id}"]
#
#  tags = {
#    Name = "Deside Cloud DB Subnet Group"
#  }
#}
#
## Create the RDS Instance.  CAUTION: The administrator should change the master
## username and password once infrastructure is built.  The username and 
## password below will show up in the state file.
#resource "aws_db_instance" "run_case_db1" {
#  allocated_storage      = 20
#  storage_type           = "gp2"
#  engine                 = "mysql"
#  engine_version         = 5.7
#  identifier             = "deside-cloud-mysql-db"
#  instance_class         = "${var.database_instance}"
#  name                   = "${var.database_table_name}"
#  username               = "deside_admin"
#  password               = "reset_this_password"
#  parameter_group_name   = "default.mysql5.7"
#  db_subnet_group_name   = "${aws_db_subnet_group.db_subnets.id}"
#  vpc_security_group_ids = ["${aws_security_group.sg_db_access.id}"]
#  skip_final_snapshot    = "true"                                    # TODO: Make this an option
#
#  #final_snapshot_identifier = "DELETE-ME-SNAPSHOT"
#
#  #multi_az             = "true"
#}

