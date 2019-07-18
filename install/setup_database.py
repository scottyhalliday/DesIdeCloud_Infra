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
            password varchar(255) NOT NULL,
            s3bucket varchar(255) NOT NULL,
            s3key    varchar(255) NOT NULL
        );
    """
   
    print(users_table)
   
    cursor.execute(users_table)
   
    # Add some users
    user = """
        INSERT INTO users (username, password, s3bucket, s3key)
        VALUES (%s, %s);
    """
   
    user1 = ("analyst1", "mypassword"  , "deside-cloud", "cases")
    user2 = ("analyst2", "somepassword", "deside-cloud", "cases")
   
    print(f'\nAdding User1')
    cursor.execute(user, user1)
   
    print(f'\nAdding User2')
    cursor.execute(user, user2)
    cnx.commit()
   
    # Create cases table (This will store metadata regarding users case)
    print("Creating Cases Table")
    case_table = """
        CREATE TABLE cases (
            case_id      int AUTO_INCREMENT PRIMARY KEY,
            name         varchar(255),
            description  varchar(1024),
            s3bucket     varchar(255),
            s3key        varchar(255),
            owner        varchar(255),
            datecreated  datetime default current_timestamp,
            datemodfied  datetime on update current_timestamp
        ); 
    """
   
    cursor.execute(case_table)

    # Create some cases
    case = """
        INSERT INTO cases (name, description, s3bucket, s3key, owner)
        VALUES (%s, %s, %s, %s, %s);
    """

    case1 = ("Circle Area"    , "Area of circle for customer x"           , "deside-cloud", "cases", "analyst1")
    case2 = ("Field Area"     , "Area of field for customer y"            , "deside-cloud", "cases", "analyst1")
    case3 = ("Triangle Area 2", "Area of triangular field for customer x" , "deside-cloud", "cases", "analyst2")
    case4 = ("Circle Area"    , "Area of circular field for customer xx"  , "deside-cloud", "cases", "analyst2")
    case5 = ("Field Area 6"   , "Area of field for customer yy"           , "deside-cloud", "cases", "analyst2")

    print(f'\nCreating sample case1')
    cursor.execute(case, case1)

    print(f'\nCreating sample case2')
    cursor.execute(case, case2)

    print(f'\nCreating sample case3')
    cursor.execute(case, case3)

    print(f'\nCreating sample case4')
    cursor.execute(case, case4)

    print(f'\nCreating sample case5')
    cursor.execute(case, case5)

    cnx.commit()

    #
    # Setup the Methods table.  Methods are stand alone programs that a user can add to a
    # case.  The IDE will manage these
    #
    print("Creating methods table")
    method_table = """
        CREATE TABLE methods (
            method_id   int AUTO_INCREMENT PRIMARY KEY,
            name        varchar(255) NOT NULL,
            description varchar(1024) NOT NULL,
            s3bucket    varchar(255) NOT NULL,
            s3key       varchar(255) NOT NULL,
            owner       varchar(255) NOT NULL,
            datecreated datetime default current_timestamp,
            datemodfied datetime on update current_timestamp
        );
    """
    
    cursor.execute(method_table)
    
    # Add some methods
    method = """
        INSERT INTO methods (name, description, s3bucket, s3key, owner)
        VALUES (%s, %s, %s, %s, %s);
    """

    method1 = ("Circular Field Area"   , "Calculate the area of a circular field"   , "deside-cloud", "methods", "analyst1")
    method2 = ("Rectangular Field Area", "Calculate the area of a rectangular field", "deside-cloud", "methods", "analyst2")
    method3 = ("Triangular Field Area" , "Calculate the area of a triangular field" , "deside-cloud", "methods", "analyst2")

    print(f'\nCreating sample method1')
    cursor.execute(method, method1)

    print(f'\nCreating sample method2')
    cursor.execute(method, method2)

    print(f'\nCreating sample method3')
    cursor.execute(method, method3)

    cnx.commit()

    print("Done!")
    
    # Cleanup 
    cursor.close()
    cnx.close()

if __name__=='__main__':
    setup_database(sys.argv[1],sys.argv[2],sys.argv[3],sys.argv[4])