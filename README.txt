1. Run the contents of ‘createCheckin.sql’ into your mySQL software to create the database
2. manually populate a user in your new mySQL database
3. change DB password in ‘checkinresultPos.php’ to your actual db password (line 11) **WARNING NOT SECURE
4. change DB password in ‘loginScreen.php’ to your actual db password (line 6) **WARNING NOT SECURE
5. IF you uncomment line 20 in ‘checkinresultPos.php’, behavior will be erratic if you do not have python3 properly installed in the path ‘/usr/local/bin/python3’ or the modules defined in the top of ‘sentiment2.py’