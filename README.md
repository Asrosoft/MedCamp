# MedCamp
Web application to record patient registrations for a voluntary medical camp.

The complete system includes the minimal hardware to support the application in a remote environment with no internet access and just an electricity supply. The system is based on the use of a home wireless router, plus a number of laptops.

One of the laptops is configured as a master and hosts a virtual machine running the web application. All the remaining laptops connect to the virtual web server via it's IP address. It is recommended that  one or more of the other laptops also has the virtual machine installed so they can be used as a backup master in case of failure of the primary master.

Several times a day, it is recommended that multiple client laptops, as well as the master, select the backup option in the application to create and download a .zip backup of the registration database to the client laptop, so there are multiple backups on different machines, in case of failure.
