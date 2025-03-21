# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [3.0.4](https://github.com/Neunerlei/dbg/compare/v3.0.3...v3.0.4) (2025-03-19)


### Bug Fixes

* ensure StreamDumper does not throw exceptions ([2c6381f](https://github.com/Neunerlei/dbg/commit/2c6381fb47ccb078868369ac93b1e6d95de576b7))

### [3.0.3](https://github.com/Neunerlei/dbg/compare/v3.0.2...v3.0.3) (2025-02-20)


### Bug Fixes

* remove unwanted output from config loader ([ece57c2](https://github.com/Neunerlei/dbg/commit/ece57c254eb9c12fae08347f06f6cfd0e5ca2e0d))

### [3.0.2](https://github.com/Neunerlei/dbg/compare/v3.0.1...v3.0.2) (2025-02-20)


### Bug Fixes

* ensure correct require file scope ([9d6b04b](https://github.com/Neunerlei/dbg/commit/9d6b04b3fcc717d04eaa1df656c02c19d280f8a6))

### [3.0.1](https://github.com/Neunerlei/dbg/compare/v3.0.0...v3.0.1) (2025-02-20)


### Bug Fixes

* improve config loader ([9c1bb91](https://github.com/Neunerlei/dbg/commit/9c1bb91857ce84e18fe4dd9ecbaefcac9785e1a5))
* remove no longer required dedupe plugin ([7cb9d20](https://github.com/Neunerlei/dbg/commit/7cb9d2033460bea2c26b40642bb433ca1ea07d59))

## [2.3.0](https://github.com/Neunerlei/dbg/compare/v2.2.0...v2.3.0) (2025-02-19)


### Features

* upgrade to next major version ([265994b](https://github.com/Neunerlei/dbg/commit/265994b90a2075c8a829ef3cd7adcbdb82e088dc))

## [2.2.0](https://github.com/Neunerlei/dbg/compare/v2.1.0...v2.2.0) (2023-12-26)


### Features

* prepare package for unit tests ([1fb2e99](https://github.com/Neunerlei/dbg/commit/1fb2e9940bd25ac521ff8ce58eaf4d9aabfb20a5))

## [2.1.0](https://github.com/Neunerlei/dbg/compare/v2.0.1...v2.1.0) (2023-09-20)


### Features

* avoid throwing exceptions in iterator and toString Kint plugins ([c8882df](https://github.com/Neunerlei/dbg/commit/c8882df4a74f8b7dccf560a6be28a0749f85967e))
* update composer dependencies ([2938f36](https://github.com/Neunerlei/dbg/commit/2938f3672ab04aae5434fa028de1e04eb410e669))

### [2.0.1](https://github.com/Neunerlei/dbg/compare/v2.0.0...v2.0.1) (2022-12-14)


### Bug Fixes

* update composer.json php requirement ([cd5258b](https://github.com/Neunerlei/dbg/commit/cd5258bb228ed8c501f5db89ff07023d5739069c))

## [2.0.0](https://github.com/Neunerlei/dbg/compare/v1.12.0...v2.0.0) (2022-12-13)


### ⚠ BREAKING CHANGES

* major version update on kint debugger

### Features

* add editor file format config ([2d5e258](https://github.com/Neunerlei/dbg/commit/2d5e258ceab17e3e5d9ff85010e49d5c70948e27))
* enable environment detection by default ([83d4cde](https://github.com/Neunerlei/dbg/commit/83d4cdefdd00adfcb5b14aa45963d952cf177e1e))
* remove deprecated features ([4613619](https://github.com/Neunerlei/dbg/commit/4613619810077223344f12dc5ba621915d7ed8bc))
* update kint to version 5 ([2e6f1c4](https://github.com/Neunerlei/dbg/commit/2e6f1c4fb889d637686ba07e5ca47fd282514d78))
* use more common "APP_ENV" var for environment detection ([322f479](https://github.com/Neunerlei/dbg/commit/322f47969ef3640f527c134ba9931c81a5ed0eea))


### Bug Fixes

* make renderer implementations compatible with kint 5 ([dd39f29](https://github.com/Neunerlei/dbg/commit/dd39f292deb3dc9dd002690bfb5446eaef0c8868))

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
