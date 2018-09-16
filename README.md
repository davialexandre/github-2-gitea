## Overview

This is a simple script to help you migrate your Github repositories to Gitea.

It was designed only for my personal use and it has a some limitations that might make it not useful for your needs:
- It can only migrate your own repositories (or, more specifically, the repositories owned by the user related to the used Github token)
- It will mark all the repositories in Gitea as mirrors
- It will mark all the repositories in Gitea as public
- All the repositories will be migrated to the UID 1 in Gitea
- It doesn't handle errors. If something fails you'll have to run it again. The `IGNORE_REPO` env variable can be useful here to no try to migrate the same repo again
- It only migrates the git stuff (commits, branches and tags). It does not migrate Github stuff (Pull Requests, Issues, Wikis, etc)

## Usage

The script needs tokens to access the Github and Gitea APIs, as well as the endpoint to a Gitea installation. It reads this information from environment variables, which can be set with an `.env` file, put in the same folder as the script. Check the `.env.example` file for all the available variables.

After setting up the environment variables, you can just run the script with `php migrate.php`.

**Important**: Don't forget to install the dependencies by running `composer install`

### Ignoring repositories

As said in the Overview section, this script does not handle errors. If it fails during the middle of a migration, you'll need to run it again meaning it might end up trying to migrate repositories that have been migrated already. Gitea will complain about that and the script will fail. To avoid that, the `IGNORE_REPO` environment variable can be used to tell the script which repos it should not try to migrate again. The variable should be a comma separated list (no spaces) of repository names.

