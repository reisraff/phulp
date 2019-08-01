# Phulp Change Log

## 2.1.1

- Avoiding emacs files by (@reisraff) (795a24698bbf62b4f61cd5d70a768a1c344f1591)

## 2.0.0

- glob pattern instead of Symfony\Finder by (@reisraff) (38f530f2630e324670ed25638d15c61ab684c279)

## 1.12.4

- both compatibility php 5.6 and 7 by (@reisraff) (7c7bef1542f5ee199adf912e3b41d90a24e52aa2)

## 1.12.3

- doctrine/collections 1.4 work with php 5.6 (#48) by (@igk1972) (b741ec7694615057eb8cbb1c272ce223a9e64896)
- Fix typo (#47) by (@igk1972) (2624c1643109c863636e42d163ca51fa5ecc2b0b)

## 1.12.2

- Fix for support on Alpine and others musl-based distros by (@igk1972) (#44) (9ebf88fb2f93320b81b00e7e05aa13b4612007b0)

## 1.12.1

- Fix autoload for custom vendor-dir by (@igk1972) (#43) (56e24174bbe17ccb7e5b1211924b36ad10cab3f0)

## 1.12.0

- adding a way to pass arguments, and a alternative autoload php file by (@reisraff) (1310707ebe9a50f83d04ad11d03eba2183d3c9a8)

## 1.11.0

- Run multiple tasks from CLI by (@reisraff) (1a7fc1dd5e73340f6a521a8f190121109a0080da)
- Sets default working directory of Phulp::exec to getcwd() by (@Schlaefer) (#34) (86795238cb7397590b2a303a074bef9c3d9be0f1)
- async not assync by (@glensc) (#33) (1391b07422af0049fdebd7f9eac07f5439d68d4f)

## 1.10.0

- Improving watch (f6d4dfa8561b28ff0e2bf01ad3ed2f6b82f06528)

## 1.9.2

- Fixing bin file, and readme (fe71ab384bc7b6dee1f7cb2655768f761f62a935)

## 1.9.1

- Fixing some stuff (a4e049e83a2d529c2d029060102ca96713aa7711)

## 1.9.0

- Adding in the core the method exec that executes sync and async commands (e6305e657355398e1b37ca78b62d9577e301f814)

## 1.8.1

- Readding the binary (13f471f)

## 1.8.0

- Removing deprecated stuff (1a292ab733e679448764ac77fa5a0e987fe13a78)
- Adding help message (f2f6c27b6dbee681b9dee12e005a5a32a1102ccf)

## 1.7.1

- Removing exit from src/ (403727bfbb76ec82fe5bae33937498bcffe0509d)
- bugfix: invalid object reference (85ea7c91a509001b0c9bdd882d8bb6a2ee720dc8)

## 1.7.0

- Lot of refactor by @reisraff

## 1.6.0

- Letting $dirs, and $distFiles be Collection by @reisraff

## 1.5.0

- Adding react by @reisraff

## 1.4.0

- Adding a Phulpfile naming convention (#30) by @raphaelstolt
- Upgrading and fixing export-ignore pattern (#31) by @raphaelstolt

## 1.3.0

- Improvement in the Output by (9325a3d) @reisraff
- Code maintainability (#27) (#28) (#29) by @raphaelstolt

## 1.2.0

- For all errors use Output::err, and when an error exit(1) (#25) by @reisraff
- Disabling output (#23) by @alexmsilva

## 1.1.2

- BUGFIX: we must ensure that the dest directory exists (#24) by @reisraff

## 1.0.1

- BUGFIX: removing trailing dash from DistFile::basepath by @reisraff

## 1.0.0

- Simplify PipeIterate::execute() (#20) by @tfrommen
- Phulp, the object-oriented way (#20) by @tfrommen
- Adding Unit/Integration tests (#20) by @tfrommen
- Code enhancements by (#20) @tfrommen

## 0.0.4

- BUGFIX: statament for php 5.6 (#18) by @alexmsilva

## 0.0.3

- Remove the need to have PhulpFile class (#9) by @oliveiramiguel

## 0.0.2

- Removed exec() with `rm -rf` for unlink() and rmdir() by @reisraff

## 0.0.1

- Initial Release by @reisraff
