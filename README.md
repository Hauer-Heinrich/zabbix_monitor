# zabbix_monitor (Warning: Is under heavy development!)
zabbix_monitor is a TYPO3 extension.
For the zabbix_client: [svewap/zabbix_client](https://github.com/svewap/zabbix_client "TYPO3 Zabbix Client")

### Installation
... like any other TYPO3 extension [extensions.typo3.org](https://extensions.typo3.org/ "TYPO3 Extension Repository")
Don't forget to include PageTS!

Currently
data-input:
-> TYPO3 backend -> list-module -> add "Zabbix Client info"

data-outpur:
-> TYPO3 backend -> page-module -> shoose the page you want the ouput - add new Content-Element -> Plugin -> list_type: zabbixmonitor_listview

### Recommended
Use the scheduler command (e. g. crontab) to update the api-data.

### Features

### Todos

### Deprecated

### IMPORTENT NOTICE

#### List-view
![example picture from backend](.github/images/zabbix_monitor_list.jpg?raw=true "List")
#### Detail-view
![example picture from backend](.github/images/zabbix_monitor_detail.jpg?raw=true "Detail")

##### Copyright notice

This repository is part of the TYPO3 project. The TYPO3 project is
free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

The GNU General Public License can be found at
http://www.gnu.org/copyleft/gpl.html.

This repository is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

This copyright notice MUST APPEAR in all copies of the repository!

##### License
----
GNU GENERAL PUBLIC LICENSE Version 3
