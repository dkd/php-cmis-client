PHP CMIS Client
===============

[![Build Status](https://api.travis-ci.org/dkd/php-cmis-client.svg)](https://travis-ci.org/dkd/php-cmis-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dkd/php-cmis-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dkd/php-cmis-client/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dkd/php-cmis-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dkd/php-cmis-client/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/dkd/php-cmis/v/stable.svg)](https://packagist.org/packages/dkd/php-cmis)
[![Total Downloads](https://poser.pugx.org/dkd/php-cmis/downloads.svg)](https://packagist.org/packages/dkd/php-cmis)
[![Latest Unstable Version](https://poser.pugx.org/dkd/php-cmis/v/unstable.svg)](https://packagist.org/packages/dkd/php-cmis)
[![License](https://poser.pugx.org/dkd/php-cmis/license.svg)](https://packagist.org/packages/dkd/php-cmis)

PHP CMIS Client is a port of OpenCMIS (Java) to PHP.
Interfaces are mostly the same so most OpenCMIS examples should
be also usable for this PHP CMIS Library.

Some basic examples can be found in the example folder
(code is not nice but shows how it works).

The functionality is not complete yet but still under development.

Currently implemented Services
------------------------------

- RepositoryService
  - [x] getRepositories (getRepositoryInfos)
  - [x] getRepositoryInfo
  - [x] getTypeChildren
  - [x] getTypeDescendants
  - [x] getTypeDefinition
  - [x] updateType
  - [x] createType
  - [x] deleteType
- NavigationService
  - [x] getChildren
  - [x] getDescendants
  - [x] getFolderTree
  - [x] getFolderParent
  - [x] getObjectParents
  - [x] getCheckedOutDocs
- ObjectService
  - [x] createDocument
  - [x] createDocumentFromSource
  - [x] createFolder
  - [x] createItem
  - [x] createRelationship
  - [ ] createPolicy
  - [ ] getAllowableActions
  - [x] getObject
  - [x] getProperties
  - [x] getObjectByPath
  - [x] getContentStream
  - [x] getRenditions
  - [x] updateProperties
  - [ ] bulkUpdateProperties
  - [x] moveObject
  - [x] deleteObject
  - [x] deleteTree
  - [x] setContentStream
  - [ ] appendContentStream
  - [x] deleteContentStream
- MultifilingService
  - [x] addObjectToFolder
  - [x] removeObjectFromFolder
- DiscoveryService
  - [x] query
  - [x] getContentChanges
- VersioningService
  - [x] checkOut
  - [x] cancelCheckOut
  - [x] checkIn
  - [x] getObjectOfLatestVersion
  - [x] getPropertiesOfLatestVersion
  - [x] getAllVersions
- RelationshipService
  - [x] getObjectRelationships
- PolicyService
  - [ ] applyPolicies
  - [ ] removePolicy
  - [ ] getAppliedPolicies
- AclSservice
  - [ ] getACL
  - [ ] applyACL


32/64-bit
=======
  The library is mainly targeting 64-bit environments. 32-bit should work, but no extensive testing is done.

LICENSE
=======
   Copyright 2014-2015 Sascha Egerer - dkd Internet Service GmbH <http://www.dkd.de>
   Copyright 2015-2016 Johannes Goslar, Claus Due - dkd Internet Service GmbH <http://www.dkd.de>

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

   This PHP CMIS Client library is part of the ForgetIT project: <http://www.forgetit-project.eu/>

   The ForgetIT project is funded by the EC within the 7th Framework Programme under the objective "Digital Preservation" (GA 600826).

