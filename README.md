# CMSC 121 Main Repo

This is a repo for the CMSC 121 course activities.

Includes a Nix Flake and envrc (for use with direnv) for
the entire LAMP stack.

## Guide

If you wish to use this template, download .envrc and flake.nix
then, in the same directory, run this command:

```bash
direnv allow
```

To start the LAMP Stack (after you've set up the direnv) type:

```bash
start-lamp
```

To connect to the databse:

```bash
local-mysql
```

To stop the LAMP stack, type:

```bash
stop-lamp
```
## Possible Questions You May Have (PQYMH)

### Where is my database?

It's in the `.data` folder. You should be able to access it there.

### I used the template and when I go to localhost:8080, I get a 404 Not Found Error!

Make an `index.php` or `index.html`. That's what Apache is told to try and access by default.
You can change that in `flake.nix`. 

### What's the point of this? Just install LAMP on Debian/Ubuntu. Or even just use a docker container.

Because Nix is the future. As for docker, that can work too, but I'm more familiar with this.
Honestly, I'd rather use nginx over Apache, but this is the course requirement so eh.
