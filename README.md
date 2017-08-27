# Docker development environment creator

Console variant of https://github.com/phpdocker-io/phpdocker.io

## Installation

Download .phar archive from [last release](https://github.com/semin-lev/docker-devenv-creator/releases).
Place it to a project dir or to /usr/local/bin.

##Usage
Just call generate command with project name argument. Project name must be without spaces or special characters.

####Example

    docker-env-generator.phar generate test-project
    
Questions about your config will be asked in interactive mode. 
All docker configuration files will be stored at the directory and after you need just call

    composer up -d 
    

####Options
- -d, --save_dir
    
    A directory to save config files. The default value is dir from you call the command.
    
- -z, --zip

    Create zip archive with configs
    
- -n, --no-interaction
    
    Do not ask question and create default configuration