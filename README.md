# Coverage

[![run-tests](https://github.com/willpower232/coverage/actions/workflows/run-tests.yml/badge.svg)](https://github.com/willpower232/coverage/actions/workflows/run-tests.yml)
![Coverage](https://laravel-coverage.s3.eu-west-2.amazonaws.com/willpower232/coverage/main.svg)

This is a reference implementation of willpower232/cloverparser-laravel and willpower232/cloverparser to generate coverage badges from CI output. I'm purposefully using as few files as possible as a challenge to myself.

---

A former casual user of [codecov](https://codecov.io) and interested in controlling my own data, I decided to see how complicated it would be to operate a similar setup myself after [their uploader script was compromised](https://www.theregister.com/2021/04/19/codecov_warns_of_stolen_credentials/).

This is the final part of this project, combining the previous parts with a basic layer of authentication for real world use.

---

## Usage

The quickest way from most CI environments would be the curl command.

```
curl -X POST -F "file=@<path to clover file>" -H "Authorization: Bearer <your token>" https://<your coverage site>/<username>/<project-name>/<branch-name>
```

## Configuration

Security is provided by a manually specified token `COVERAGE_AUTH_TOKEN` in the env, this value could be specified as query, input, password, or bearer (see AppServiceProvider). If you were in a multi-user environment you would want to swap that out with something more secure that prevented people overwriting other users coverage files.

File are stored in S3 in this example as it is cloud storage with its own hosting, concealing where you host this code, and storing only the few bytes required should not cost you much, if anything. There is nothing stopping you from configuring alternative disks, as long as there is a Laravel driver for them.
