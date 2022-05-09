# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.12.0](https://github.com/Neunerlei/dbg/compare/v1.11.1...v1.12.0) (2022-05-09)


### Features

* add unique request id to all log outputs ([c829765](https://github.com/Neunerlei/dbg/commit/c8297650781414c5e4774efea4e62fca8631b38f))

### [1.11.1](https://github.com/Neunerlei/dbg/compare/v1.11.0...v1.11.1) (2022-04-19)


### Bug Fixes

* **StreamDumper:** don't remove commas ([5464f52](https://github.com/Neunerlei/dbg/commit/5464f52c11a1b53ebe8e62264829cd8bf8727fc7))

## [1.11.0](https://github.com/Neunerlei/dbg/compare/v1.10.1...v1.11.0) (2022-04-19)


### Features

* **StreamDumper:** streamline config + output formatting ([407d53f](https://github.com/Neunerlei/dbg/commit/407d53fb4fd2c21f1ff0bdf28a2bbe7c19f67f5f))
* implement stream dumper and logStream ([3c8547b](https://github.com/Neunerlei/dbg/commit/3c8547bacc2e4dd34807b41da9cf26d08dd675a0))


### Bug Fixes

* **Dumper:** fix rendering issue in nested dbg(e) calls ([8f790b6](https://github.com/Neunerlei/dbg/commit/8f790b639fe72608f223abe6931ef484be32f229))

### [1.10.1](https://github.com/Neunerlei/dbg/compare/v1.10.0...v1.10.1) (2021-11-30)


### Bug Fixes

* fix an issue where callable hooks were not executed ([580c0b7](https://github.com/Neunerlei/dbg/commit/580c0b786ef64e48d0913dc039c23ed1c81b2a50))

## [1.10.0](https://github.com/Neunerlei/dbg/compare/v1.9.2...v1.10.0) (2021-11-27)


### Features

* major code cleanup + refactoring into smaller chunks + fix code styling issues ([789fea3](https://github.com/Neunerlei/dbg/commit/789fea3ea69a46569d198d1666a918944f200724))

### [1.9.2](https://github.com/Neunerlei/dbg/compare/v1.9.1...v1.9.2) (2020-02-27)

### [1.9.1](https://github.com/Neunerlei/dbg/compare/v1.9.0...v1.9.1) (2020-02-27)

## [1.9.0](https://github.com/Neunerlei/dbg/compare/v1.8.1...v1.9.0) (2020-02-27)


### Features

* update vendor name ([fdfa5fc](https://github.com/Neunerlei/dbg/commit/fdfa5fcf9659f2436a9b10c569b658c857fffc79))

### [1.8.1](https://github.com/neunerlei/dbg/compare/v1.8.0...v1.8.1) (2020-02-19)

## [1.8.0](https://github.com/neunerlei/dbg/compare/v1.7.0...v1.8.0) (2020-02-19)


### Features

* prepare for publication on github and packagist (016abee)

# 1.7.0 (2020-01-15)


### Features

* **trace:** add more options to the trace output (1119adc)



# 1.6.0 (2019-09-25)


### Features

* add additional $short option for backtrace generation (f4db405)



## 1.5.1 (2019-08-06)



# 1.5.0 (2019-07-18)


### Bug Fixes

* avoid duplicate definition of ExtendedCliRenderer and ExtendedTextRenderer classes (598adcb)


### Features

* add additional, low performance plugins back to Kint (8d04e98)



# 1.4.0 (2019-07-16)


### Features

* add de-dupe plugin to make sure kint will not render the same object over and over again (the children of an already rendered object will not be rendered again) (7e5d1eb)
* remove namespaces as they break kint to much to be used sustainably (2e5a497)



## 1.3.1 (2019-05-28)


### Bug Fixes

* ajax requests now correctly respond with the text renderer (237611c)



# 1.3.0 (2019-05-23)


### Features

* add better implemented configuration options (3f7b7a6)



## 1.2.2 (2019-05-16)


### Bug Fixes

* duplicate output when using dbge() (88abd6f)



## 1.2.1 (2019-05-16)


### Bug Fixes

* select correct renderer for default html requests (2177d17)



# 1.2.0 (2019-05-16)


### Features

* add main sources (2c649df)



# 1.1.0 (2019-05-16)


### Features

* initial commit (471be0b)



# Change Log
