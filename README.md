# CMSC 121 Main Repo

This is a repo for the CMSC 121 course activities.

Includes a Nix Flake and envrc (for use with direnv) for
the entire LAMP stack, but without the A, since we're using Caddy instead.

## Guide

If you wish to use this template, download `.envrc` and `devenv.nix`
then, in the same directory, run this command:

```bash
direnv allow
```

To start the Caddy, MariaDB, and PHP Stack type:

```bash
devup
```

Follow the Terminal User Interface (TUI) for further instructions.
If you want to stop the processes, just press F10 and press Enter
then the process should probably be stopped.

## Additional Notes

If you want to run a similar style of server stack for your current project as I am,
I recommend downloading the `index.php` as well as that will go through your
subdirectories and list out all the different HTML and PHP files for you to access
in the browser after heading to `localhost:8080` or whatever port you set it to.

## Possible Questions You May Have (PQYMH)

### Where is my database?

It's in the `.data` folder. You should be able to access it there.

### I used the template and when I go to localhost:8080, I get a 404 Not Found Error!

Make an `index.php` or `index.html`. That's what ~~Apache~~ Caddy is told to try and access by default.
You can change that in `devenv.nix`.

### What's the point of this? Just install LAMP on Debian/Ubuntu. Or even just use a docker container.

Because Nix is the future. As for docker, that can work too, but I'm more familiar with this.
Honestly, I'd rather use nginx over Apache, but this is the course requirement so eh.
