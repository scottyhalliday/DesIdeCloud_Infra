"""
Setup the database for an example set of users
"""
import sys
from mysql.connector import connection

def setup_database(user: str, password: str, host: str, database: str):
    
    try:
        #cnx = connection.MySQLConnection(user="deside_admin", password="reset_this_password",
        #            host="deside-cloud-mysql-db.cubk8axrpfzg.us-east-1.rds.amazonaws.com", database="DesideCloud")
        cnx = connection.MySQLConnection(user=user, password=password, host=host, database=database)
    except:
        print("Could not connect :(")
        sys.exit(1)
    
    cursor = cnx.cursor()
    
    print("Connected!!!")
    
    # Create a users table
    print("Creating Users Table")
    
    users_table = """
        CREATE TABLE users (
            userid   int          AUTO_INCREMENT PRIMARY KEY,
            username varchar(255) NOT NULL,
            password varchar(255) NOT NULL
        );
    """
    
    print(users_table)
    
    cursor.execute(users_table)
    
    # Add some users
    user = """
        INSERT INTO users (username, password)
        VALUES (%s, %s);
    """
    
    user1 = ("analyst1", "mypassword")
    user2 = ("analyst2", "somepassword")
    
    print(f'\nAdding User1')
    cursor.execute(user, user1)
    
    print(f'\nAdding User2')
    cursor.execute(user, user2)
    cnx.commit()
    
    # Create cases table (This will store metadata regarding users case)
    print("Creating Cases Table")
    case_table = """
        CREATE TABLE cases (
            caseid       int AUTO_INCREMENT PRIMARY KEY,
            s3bucket     varchar(255),
            s3key        varchar(255),
            datecreated  datetime,
            datemodfied  datetime
        ) 
    """
    
    cursor.execute(case_table)
    
    print("Done!")
    
    # Cleanup 
    cursor.close()
    cnx.close()

if __name__=='__main__':
    setup_database(sys.argv[1],sys.argv[2],sys.argv[3],sys.argv[4])