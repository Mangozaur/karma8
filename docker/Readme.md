## Devel docker image

**Project setup**

### First run (create new project)
All commands are expecting to start from this (./docker) directory as root directory:

**Build images**: 
```
docker-compose build
docker-compose up --no-start
```

**Create containers**
```
docker-compose up app -d
```

**Start containers**:
```
docker-compose up -d
```
