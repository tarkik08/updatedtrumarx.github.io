for posting, try create a seperate file that holds the details and when clicked on careers
tab it loads that file with the details of posting and any editing is to be done in the file
itself and then it reflects in the careers page (use env var if possible) --> CREATE A DATABASE

for the login and logout, 1. admin login button to be in same shape as that of consultation 
                          2. error message for wrong credentials doesnt need a button on top(the red cross button)
                          3. only viewing to be allowed in normal mode, when logged in with admin credentials then the edit buttons to appear and the one for adding new positions.
                          4. the fields to enter admin credentials to be cleared after every consecutive attemot whether its a login fail or login pass
                          5. MAKE SURE MAILTO LINK IS ATTATCHED FOR THE APPLY NOW BUTTON IN FULL TIME POSITIONS AND THAT BUTTON IS TO APPEAR IF LOGGED OUT (JUST THE EDIT AND DELETE BUTTONS ARE TO DISAPPEAR WHEN LOGGED OUT)  
                          6. sucess button for sucessful posting doesnt need the big ass cross button
                          7. after every reload or direct redirecting to the career (for every new instance login must be cleared and is to be done again)
                          8. deadline to be in dd-mm-yyyy and also add a small text in the filling part of the date in creating a new full time post saying deadline for appication and add any other fields as it is relevant.

For Hosting:

make sure t =o configure the env var in the smtp protocall and also for the .json databse for job posting to match to the server you are hosting on