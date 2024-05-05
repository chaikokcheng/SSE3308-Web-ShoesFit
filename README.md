# SSE3308 Web Group 4 - ShoesFit

| Matric | Name          |
|--------|---------------|
| 217146 | WAI YI PEI    |
| 215169 | EMILY FRANCIS |
| 214921 | ANG YEE WEN   |
| 215035 | CHAN CI EN    |
| 217463 | CHAI KOK CHENG|

Live Website Link: https://sse3308-shoesfit.web.app/

Github Actions: any push to "Main" branch will auto deploy to hosting

PS: Follow below steps to do your own branch and avoid directly push/merge to "Main" 

---------------------------

# **Git Workflow Guide**

Welcome to our project! This **README** will guide you through our Git workflow, ensuring everyone is on the same page for version control. Follow these steps to set up your environment, create a new feature branch, work within that branch, and push your changes back to the remote repository.

## **Prerequisites**
Ensure you have Git installed on your machine. You can verify this by running `git --version` in your terminal. If Git is not installed, download and install it from [Git's official site](https://git-scm.com/downloads).

## **Step 1: Clone the Repository**
First, you need to clone the repository to have a local copy on your machine. Use the following command, replacing `URL` with the repository's URL provided by your project lead.

```bash
git clone URL
```

Example:
```bash
git clone https://github.com/chaikokcheng/SSE3308-Web-ShoesFit.git
```


## **Step 2: Create Your Own Branch**
Before you start making changes, create a new branch. This keeps your changes organized and separate from the main branch until they are ready to be merged. We name branches according to the feature or fix they are meant to address.

To create and switch to a new branch, use:
```bash
git checkout -b your-branch-name
```

Example:
```bash
git checkout -b cartpage
```

This command both creates the new branch and checks it out so you can start working on it immediately.

## **Step 3: Make Changes and Commit**
Make your changes in the code. Once you're done, stage your changes with Git add, and then commit them with a descriptive message.

Stage changes:
```bash
git add .
```

Commit changes:
```bash
git commit -m "Add a descriptive commit message"
```

Example commit:
```bash
git commit -m "Implement user login authentication system"
```

## **Step 4: Push Your Branch**
After committing your changes locally, push your branch to the remote repository.

```bash
git push origin your-branch-name
```

Example:
```bash
git push origin feature-login-authentication
```

## **Step 5: Create a Pull Request**
Once your branch is pushed, go to the repository on GitHub or another Git service you use. You should see an option to "Compare & pull request." Click on it, review your changes, and submit the pull request. This notifies team members that your code is ready to be reviewed before merging into the main branch.
