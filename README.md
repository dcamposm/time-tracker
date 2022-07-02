# TIME TRACKER
 
# PREREQUISITS

- Install Docker Desktop

# INSTRUCTIONS

- Run docker-compose up -d --build to build images and display container
- Open http://localhost:8080 in your browser to use the application 
- Or from the docker terminal of php container, execute the command: 
 		- php bin/console app:create-task <start/end> // To Start/End a task
 		- php bin/console app:show-task // To show a list of all the tasks
