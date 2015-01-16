# This document describes the internal development workflow of dkd.
# Feel free to do pull request on Github and ignore this guide!

---

## Code Review

We use [Phabricator](https://secure.phabricator.com/book/phabricator/) to 
manage our code reviews.

Phabricator has a commandline tool called [Arcanist]
(https://secure.phabricator.com/book/phabricator/article/arcanist_quick_start/)

This tool is used to do a pre commit review also called `Review`. 
To learn more about the difference to post commit reviews (called `Audit`)
please read [Reviews vs Audit]
(https://secure.phabricator.com/book/phabricator/article/reviews_vs_audit/)

## First steps

In the first step you should 
[install Arcanist](https://secure.phabricator.com/book/phabricator/article/arcanist/)
and understand the basics of this tool by reading the documentation.

**Important:** Please do never call `git push` to push any changes 
to the remote repository. This will all be handled by arcanist for you.

You should now have already installed your user certificate with 
`arc install-certificate`.

Now clone this repository, if not already done, and call the command 
`arc list` that should  give you a list of open Differential 
Revisions (code reviews).

To install the required dependencies you have to run `composer install`.
If you have not installed composer yet, please check the [documentation]
(https://getcomposer.org/doc/00-intro.md).

## Review Code

The easiest way to review code is to use the web interface of Phabricator 
located at https://phabricator.dkd.de/

The code review tool is called `Differential` and should be visible in the 
left-side application list.
When you open `Differential` you should see different queries in the left 
side to select what you want to see.
In the main area you should see currently active revisions that are waiting 
for a review. To open and review a revision simply select one by clicking 
on the title.

You should now see the details of the revision.
In the upper area you should see a `Apply Patch` command that can be used to
checkout and test this code in your local repository.
Below that you see the history of the revision and a `Revision Update History`
that shows you the history of code changes for this
revision.
The box `Local Commits` shows you the commits that have been done locally by 
the author to create this revision.
Below that you should see `Table of Contents` that shows you all files that 
are affected by this revision followed by a diff view that shows you the 
file content that has been changed.

You can now click on a line number to comment a specific line of code.

To submit your review you have to scroll down to the `Add Comment` box where
you can select a bunch of actions. One of them is `Accept Revision` if you 
think this change is good and can be landed into the repository.
If you want the author to change anything you have to select `Request Changes`.
If you want to change something by yourself you can commandeer the revision to
you by selection `Commandeer Revision`.

## Create a Patch 

To create a new patch (Revision) you can use this workflow.

* Go to you working copy
* call `arc feature myFeatureName` which is an alias for `git branch` and
creates a new branch with the given name.
* do some hacking
* to create a new revision you have now to call `arc diff`. 
This command will automatically:
    * execute the code linters (configured in the `.arclint` file)
    * execute the unit tests (configured in the `.arcconfig` file)
    * create a local commit
* Arcanist will ask you some questions like if you want to add untracked
files to the commit.
* When you have answered them you commit message editor will open and you can
fill in some stuff for the commit message
* After closing the editor you'll get notified that you have not added any 
reviewer. This is ok here becaue the Reviewers are automatically added by a
[Herald](https://secure.phabricator.com/book/phabricator/article/herald/) rule.
* When everything went fine you should get a link that points to you new 
created Revision that can now be reviewed by others.

## Merge a Patch

A Revision can be merged as soon as any reviewer has accepted the revision. 
The command to 'land' the revision is displayed in the phabricator web interface 
at the very top as `Next Step`. You should see an arcanist command like 
`arc land 'myFeatureName'`. When executing this command arcanist will apply 
this patch and push it to the remote repository. You're done.
