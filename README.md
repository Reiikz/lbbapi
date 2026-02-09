
# LBBAPI

#### Light and Basic Bind9 API

LBBAPI is a light PHP written API for interacting with a bind9 server zone file.

### Branches

- master - Stable (has been in production for at least a few days).
- RC - (Release Canidadate) Possibly broken Merge to master avoid if possible.
- Hotfix - Bugfix implementation testing possibly broken.
- dev - Development branch, most likely broken, do not use.
- Other branches or branches starting with dev- are development branches, do not use.

release process:
- dev* -> RC -> master
- Hotfix -> RC -> master

### Features

- All PHP!
- Basic cpanel
- API
  - Allows DNS record delition creation update
  - Wildcard supporting permission system for multi client API access
  - API client management (Authentication creation and permission management)
- Does not use or require an external database. The DNS database file is interacted with directly!
- Reloads Bind9 when a change is made and bumps the version number
- No JS

> if you doubt weather this works, note this gitlab instance's DNS (git.reiikz.net) is powered by this program.

### [Documentation](docs/)

### Reporting issues

Register an account on this gitlab instance (git.reiikz.net) verify it with the admin as per the instructions in the registration page.

Open an issue.

### Contributing

Register an account on this gitlab instance (git.reiikz.net) verify it with the admin as per the instructions in the registration page.

Make a pull request.