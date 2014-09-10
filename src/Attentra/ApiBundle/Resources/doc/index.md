## /api/resourcegroups ##

### `GET` /api/resourcegroups.{_format} ###

_List all entities._

List all entities.

#### Requirements ####

**_format**

  - Requirement: json|xml|html

#### Filters ####

offset:

  * Requirement: \d+
  * Description: Offset from which to start listing pages.

limit:

  * Requirement: \d+
  * Description: How many pages to return.
  * Default: 5

#### Response ####

[]:

  * type: array of objects (ResourceGroup)

[][id]:

  * type: integer

[][name]:

  * type: string

[][description]:

  * type: string


### `POST` /api/resourcegroups.{_format} ###

_Creates a new entity from the submitted data._

#### Requirements ####

**_format**

  - Requirement: json|xml|html

#### Parameters ####

name:

  * type: string
  * required: true

description:

  * type: string
  * required: false


## /api/resourcegroups/new ##

### `GET` /api/resourcegroups/new.{_format} ###

_Presents the form to use to create a new entity._

Presents the form to use to create a new entity.

#### Requirements ####

**_format**

  - Requirement: json|xml|html


## /api/resourcegroups/{id} ##

### `GET` /api/resourcegroups/{id}.{_format} ###

_Gets an entity for a given id_

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Response ####

id:

  * type: integer

name:

  * type: string

description:

  * type: string


### `PATCH` /api/resourcegroups/{id}.{_format} ###

_Update existing entity from the submitted data._

Update existing entity from the submitted data.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Parameters ####

name:

  * type: string
  * required: true

description:

  * type: string
  * required: false


### `PUT` /api/resourcegroups/{id}.{_format} ###

_Update existing entity from the submitted data or create a new entity at a specific location._

Update existing entity from the submitted data or create a new entity at a specific location.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Parameters ####

name:

  * type: string
  * required: true

description:

  * type: string
  * required: false


### `DELETE` /api/resourcegroups/{id}.{_format} ###

_Delete an existing entity._

Delete an existing entity.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id


## /api/resources ##

### `GET` /api/resources.{_format} ###

_List all entities._

List all entities.

#### Requirements ####

**_format**

  - Requirement: json|xml|html

#### Filters ####

offset:

  * Requirement: \d+
  * Description: Offset from which to start listing pages.

limit:

  * Requirement: \d+
  * Description: How many pages to return.
  * Default: 5

#### Response ####

[]:

  * type: array of objects (Resource)

[][id]:

  * type: integer

[][name]:

  * type: string

[][identifier]:

  * type: string

[][group]:

  * type: object (ResourceGroup)

[][group][id]:

  * type: integer

[][group][name]:

  * type: string

[][group][description]:

  * type: string

[][description]:

  * type: string

[][created_at]:

  * type: DateTime

[][updated_at]:

  * type: DateTime

[][deleted_at]:

  * type: DateTime


### `POST` /api/resources.{_format} ###

_Creates a new entity from the submitted data._

#### Requirements ####

**_format**

  - Requirement: json|xml|html

#### Parameters ####

name:

  * type: string
  * required: true

identifier:

  * type: string
  * required: false

group:

  * type: choice
  * required: false

description:

  * type: string
  * required: false


## /api/resources/new ##

### `GET` /api/resources/new.{_format} ###

_Presents the form to use to create a new entity._

Presents the form to use to create a new entity.

#### Requirements ####

**_format**

  - Requirement: json|xml|html


## /api/resources/{id} ##

### `GET` /api/resources/{id}.{_format} ###

_Gets an entity for a given id_

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Response ####

id:

  * type: integer

name:

  * type: string

identifier:

  * type: string

group:

  * type: object (ResourceGroup)

group[id]:

  * type: integer

group[name]:

  * type: string

group[description]:

  * type: string

description:

  * type: string

created_at:

  * type: DateTime

updated_at:

  * type: DateTime

deleted_at:

  * type: DateTime


### `PATCH` /api/resources/{id}.{_format} ###

_Update existing entity from the submitted data._

Update existing entity from the submitted data.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Parameters ####

name:

  * type: string
  * required: true

identifier:

  * type: string
  * required: false

group:

  * type: choice
  * required: false

description:

  * type: string
  * required: false


### `PUT` /api/resources/{id}.{_format} ###

_Update existing entity from the submitted data or create a new entity at a specific location._

Update existing entity from the submitted data or create a new entity at a specific location.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Parameters ####

name:

  * type: string
  * required: true

identifier:

  * type: string
  * required: false

group:

  * type: choice
  * required: false

description:

  * type: string
  * required: false


### `DELETE` /api/resources/{id}.{_format} ###

_Delete an existing entity._

Delete an existing entity.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id


## /api/timeinputs ##

### `GET` /api/timeinputs.{_format} ###

_List all entities._

List all entities.

#### Requirements ####

**_format**

  - Requirement: json|xml|html

#### Filters ####

offset:

  * Requirement: \d+
  * Description: Offset from which to start listing pages.

limit:

  * Requirement: \d+
  * Description: How many pages to return.
  * Default: 5

#### Response ####

[]:

  * type: array of objects (TimeInput)

[][id]:

  * type: integer

[][datetime]:

  * type: DateTime

[][identifier]:

  * type: string

[][type]:

  * type: string

[][description]:

  * type: string


### `POST` /api/timeinputs.{_format} ###

_Creates a new entity from the submitted data._

#### Requirements ####

**_format**

  - Requirement: json|xml|html

#### Parameters ####

datetime:

  * type: datetime
  * required: true

identifier:

  * type: string
  * required: true

type:

  * type: string
  * required: false

description:

  * type: string
  * required: false


## /api/timeinputs/new ##

### `GET` /api/timeinputs/new.{_format} ###

_Presents the form to use to create a new entity._

Presents the form to use to create a new entity.

#### Requirements ####

**_format**

  - Requirement: json|xml|html


## /api/timeinputs/{id} ##

### `GET` /api/timeinputs/{id}.{_format} ###

_Gets an entity for a given id_

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Response ####

id:

  * type: integer

datetime:

  * type: DateTime

identifier:

  * type: string

type:

  * type: string

description:

  * type: string


### `PATCH` /api/timeinputs/{id}.{_format} ###

_Update existing entity from the submitted data._

Update existing entity from the submitted data.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Parameters ####

datetime:

  * type: datetime
  * required: true

identifier:

  * type: string
  * required: true

type:

  * type: string
  * required: false

description:

  * type: string
  * required: false


### `PUT` /api/timeinputs/{id}.{_format} ###

_Update existing entity from the submitted data or create a new entity at a specific location._

Update existing entity from the submitted data or create a new entity at a specific location.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id

#### Parameters ####

datetime:

  * type: datetime
  * required: true

identifier:

  * type: string
  * required: true

type:

  * type: string
  * required: false

description:

  * type: string
  * required: false


### `DELETE` /api/timeinputs/{id}.{_format} ###

_Delete an existing entity._

Delete an existing entity.

#### Requirements ####

**_format**

  - Requirement: json|xml|html
**id**

  - Type: int
  - Description: The entity id
