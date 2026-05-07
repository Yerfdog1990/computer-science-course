# Label-Based Access Control (LBAC)

## Overview

Label-based access control (LBAC) greatly increases the control you have over who can access your data. LBAC lets you decide exactly who has write access and who has read access to individual rows and individual columns.

### Key Features
- **Granular Control**: Provides row-level and column-level security
- **Flexible Configuration**: Highly configurable to match specific security environments
- **Security Administrator Control**: All LBAC configuration performed by users with SECADM authority
- **Policy-Based Protection**: Uses security policies to define access criteria

## What LBAC Does

### Security Label Components
A **security label component** is a database object that represents a criterion used to determine if a user should access data. Examples include:
- User's department membership
- User's project involvement
- User's trust level

### Security Policies
A **security policy** describes criteria used to decide who has access to what data. A security policy contains one or more security label components.

**Policy Rules:**
- Only one security policy can protect any one table
- Different tables can be protected by different security policies
- Data in a table can only be protected by security labels from that table's policy

### Security Labels
**Security labels** are objects that contain security label components and are applied to data to protect it.

**Label Characteristics:**
- Configured to represent organizational access criteria
- Can range from simple (high/low trust) to complex multi-factor criteria
- Associated with individual columns and rows
- Data protected by security labels is called **protected data**

### User Access Control
- Security administrators grant security labels to users for access
- When users access protected data, their security label is compared to the data's protecting label
- Protecting labels block some security labels and allow others
- Users can hold security labels for multiple security policies
- For any given policy, users can hold at most one label for read access and one for write access

### Exemptions
An **exemption** allows access to protected data that security labels might otherwise prevent. Together, security labels and exemptions are called **LBAC credentials**.

## Access Behavior

### Protected Column Access
If you try to access a protected column that your LBAC credentials don't allow:
- Access fails
- Error message is returned

### Protected Row Access
If you try to read protected rows that your LBAC credentials don't allow:
- Db2 acts as if those rows do not exist
- Rows cannot be selected in any SQL statement (SELECT, UPDATE, DELETE)
- Aggregate functions ignore inaccessible rows
- COUNT(*) returns count only of readable rows

## Views and LBAC

- Views can be defined on protected tables same as non-protected tables
- LBAC protection on underlying table is enforced when view is accessed
- LBAC credentials of session authorization ID are used
- Different users accessing same view might see different rows based on credentials

## Referential Integrity and LBAC

### Rule 1: Child Table Scans
LBAC read access rules are NOT applied for internally generated scans of child tables to avoid orphan children.

### Rule 2: Parent Table Scans
LBAC read access rules are NOT applied for internally generated scans of parent tables.

### Rule 3: Cascade Operations
LBAC write rules are applied when CASCADE operations are performed on child tables. If user deletes parent but cannot delete children due to LBAC write rule violation, delete is rolled back and error raised.

## Storage Overhead

### Row-Level Protection
- Additional storage cost is row security label column
- Cost depends on security label type
- Example: Security policy with two components = 16 bytes (8 bytes per component)
- Total cost = (N*8 + 4) bytes per row where N = number of components

### Column-Level Protection
- Column security label is metadata stored in SYSCOLUMNS catalog table
- User table incurs no storage overhead
- Metadata is simply the ID of the security label protecting the column

## What LBAC Does Not Do

### Discretionary Access Control Priority
LBAC will never allow access to data forbidden by discretionary access control. If you don't have permission to read a table, you cannot read data from that table regardless of LBAC permissions.

### Unprotected Data
LBAC credentials only limit access to protected data and have no effect on unprotected data access.

### Administrative Operations
- LBAC credentials are not checked when dropping tables or databases
- LBAC credentials are not checked during backup operations
- Data on backup media is not protected by LBAC
- Only data in the database is protected

### Unsupported Table Types
LBAC cannot protect:
- Staging tables
- Tables that staging tables depend on
- Typed tables
- Nicknames

## LBAC Security Policies

### Policy Components
A security policy includes:
- Security label components used in the policy
- Rules used when comparing security label components
- Optional behaviors used when accessing protected data
- Additional security labels and exemptions to consider

### Policy Rules
- Every protected table must have exactly one security policy
- Rows and columns can only be protected with labels from that table's policy
- Multiple security policies can exist in a single database
- No table can have more than one security policy

### Creating Security Policies
- Must be a security administrator (SECADM authority)
- Use CREATE SECURITY POLICY SQL statement
- Security label components must be created before policy creation
- Component order in policy doesn't indicate precedence but is important for built-in functions

### Altering and Dropping Policies
- ALTER SECURITY POLICY: Modify existing policies
- DROP SECURITY POLICY: Remove policies (cannot drop if associated with any table)

## Security Label Components

### Component Definition
A security label component represents any criteria used to decide user access, such as:
- Trust level
- Department membership
- Project involvement

### Elements
An **element** is one particular "setting" allowed for a component. Example: Trust level component might have elements: Top Secret, Secret, Classified, Unclassified.

### Component Types

#### SET Components
- Unordered lists of elements
- Only comparison is element presence/absence in list

#### ARRAY Components
- Elements arranged in linear scale
- First element listed = highest value, last = lowest
- Relationships: Higher than, Lower than

#### TREE Components
- Elements arranged in tree structure
- Elements specified with parent-child relationships
- First element must be ROOT
- Relationships: Parent, Child, Sibling, Ancestor, Descendent

### Creating Components
- Must be security administrator
- Use CREATE SECURITY LABEL COMPONENT SQL statement
- Must provide: component name, type (ARRAY/TREE/SET), complete list of elements
- For ARRAY/TREE: describe element structure

## Security Labels

### Label Definition
A security label is a database object describing security criteria, applied to data for protection and granted to users for access.

### Label Structure
- Each security label is part of exactly one security policy
- Includes one value for each component in that policy
- A value is a list of zero or more elements allowed by the component
- ARRAY values can contain zero or one element
- Other types can have zero or more elements
- Empty value = value with no elements

### Label Comparison
When users access protected data, their security label is compared to protecting label. If user's label is blocked, access is denied.

### Creating Labels
- Must be security administrator
- Use CREATE SECURITY LABEL SQL statement
- Provide: label name, security policy, values for components
- Components not specified assume empty value
- Label must have at least one non-empty value

### Granting and Revoking Labels
- Grant: GRANT SECURITY LABEL (read, write, or both)
- Revoke: REVOKE SECURITY LABEL
- Users cannot hold more than one label from same policy for same access type

## Security Label Value Format

When represented as character string:
- Component values listed left to right in policy component order
- Elements represented by name
- Elements from different components separated by colon (:)
- Multiple elements for same component enclosed in parentheses and separated by comma
- Empty values represented by empty parentheses: ()

**Example Format:** `'Secret:():(Epsilon 37,Megaphone,Cloverleaf)'`

## Security Label Comparison

### Comparison Process
1. User's LBAC credentials compared to protecting security label
2. Only one user label used (matching policy and access type)
3. Labels compared component by component
4. Appropriate LBAC rule set rules determine if access is blocked
5. If any user values are blocked, credentials are blocked

### Exemptions Effect
If user holds exemption for rule being used, comparison is not done and protecting value assumed not to block user's value.

## LBAC Rule Sets

### Rule Set Definition
An LBAC rule set is a predefined set of rules used when comparing security labels. Currently only one supported rule set: **DB2LBACRULES**

### DB2LBACRULES Rules

#### Read Rules
- **DB2LBACREADARRAY** (ARRAY): Blocked when user's value is lower than protecting value
- **DB2LBACREADSET** (SET): Blocked when protecting values exist that user doesn't hold
- **DB2LBACREADTREE** (TREE): Blocked when none of user's values equal or ancestor of protecting values

#### Write Rules
- **DB2LBACWRITEARRAY** (ARRAY): Blocked when user's value is higher OR lower than protecting value (prevents write-up and write-down)
- **DB2LBACWRITESET** (SET): Blocked when protecting values exist that user doesn't hold
- **DB2LBACWRITETREE** (TREE): Blocked when none of user's values equal or ancestor of protecting values

### Empty Value Handling
All rules treat empty values the same:
- Empty values block no other values
- Empty values are blocked by any non-empty value

## Rule Exemptions

### Exemption Definition
When you hold an LBAC rule exemption on a particular rule of a particular security policy, that rule is not enforced when accessing data protected by that policy.

### Exemption Characteristics
- Only affects comparisons for the specific security policy
- Can hold multiple exemptions
- Holding exemption for every rule in policy = complete access to protected data

### Granting and Revoking
- Grant: GRANT EXEMPTION ON RULE
- Revoke: REVOKE EXEMPTION ON RULE
- Must be security administrator

## Built-in Functions

### SECLABEL
Builds security label by specifying policy and component values. Returns DB2SECURITYLABEL data type.

**Example:**
```sql
INSERT INTO T1 VALUES 
   ( SECLABEL( 'P1', 'UNCLASSIFIED:(ALPHA,SIGMA):G2' ), 22 )
```

### SECLABEL_BY_NAME
Accepts policy name and label name, returns security label as DB2SECURITYLABEL.

**Example:**
```sql
INSERT INTO T1 VALUES ( SECLABEL_BY_NAME( 'P1', 'L1' ), 22 )
```

### SECLABEL_TO_CHAR
Converts security label to character string representation.

## Data Protection Using LBAC

### Protection Levels
LBAC can protect:
- Rows of data
- Columns of data
- Both rows and columns

### Adding Security Policy to Tables
- New tables: Use SECURITY POLICY clause in CREATE TABLE
- Existing tables: Use ADD SECURITY POLICY clause in ALTER TABLE
- No SECADM authority or LBAC credentials required
- Adding policy doesn't activate protection by itself

### Protecting Rows
- New tables: Include column with DB2SECURITYLABEL data type
- Existing tables: Add DB2SECURITYLABEL column
- Existing rows protected with security label user holds for write access
- New rows protected by storing security label in DB2SECURITYLABEL column

### Protecting Columns
- New tables: Use SECURED WITH column option in CREATE TABLE
- Existing columns: Use SECURED WITH option in ALTER TABLE
- Must have LBAC credentials allowing write to data protected by the label
- Columns can only be protected by labels from table's security policy
- Column can be protected by no more than one security label

## Reading LBAC Protected Data

### Reading Protected Columns
- User's LBAC credentials compared with column's protecting security label
- If access blocked: error returned, statement fails
- If access allowed: statement proceeds normally
- Trying to read inaccessible column causes entire statement to fail

### Reading Protected Rows
- Rows user cannot read are treated as non-existent
- Only rows with readable security labels returned
- Different users may see different rows in same table
- Affects SELECT, UPDATE, DELETE statements
- Aggregate functions only include readable rows

### Reading Protected Rows with Protected Columns
- Column access checked before row access
- If column access blocked: entire statement fails
- If column access allowed: only readable rows returned

## Inserting LBAC Protected Data

### Inserting to Protected Columns
- User's LBAC credentials for writing compared with column's protecting label
- If access blocked: insert fails, error returned
- If access allowed: statement proceeds normally
- Default values inserted even without write access if available

### Inserting to Protected Rows
- If no security label provided: user's write access label automatically used
- If no write access label: error returned
- Can explicitly provide security label using built-in functions
- Provided label only used if credentials allow write to that protection level
- If policy has RESTRICT NOT AUTHORIZED WRITE SECURITY LABEL: unauthorized write labels cause failure
- If policy has OVERRIDE NOT AUTHORIZED WRITE SECURITY LABEL: user's write label used instead

## Updating LBAC Protected Data

### Updating Protected Columns
- User's LBAC credentials for writing compared with column's protecting label
- If write access blocked: error returned, statement fails
- If write access allowed: update continues
- Read access not required for column updates

### Updating Protected Rows
- Must have read access to row (otherwise row doesn't exist for user)
- Must have write access to row
- User's write credentials compared with row's protecting label
- If write access blocked: update fails, error returned
- DB2SECURITYLABEL column automatically set to user's write label if not explicitly set
- Explicit label changes subject to write access verification

### Updating Protected Rows with Protected Columns
- Must have write access to all affected protected columns
- Must have read and write access to rows
- DB2SECURITYLABEL column handling same as other row updates

## Deleting LBAC Protected Data

### Deleting Protected Rows
- Must have read access to row (otherwise row doesn't exist)
- Must have write access to row
- Write credentials compared with row's protecting label
- If write access blocked: DELETE fails, no rows deleted
- Only rows user can read are considered for deletion

### Deleting Rows with Protected Columns
- Must have write access to all protected columns in table
- If any row exists that user cannot write to: delete fails
- For tables with both protected columns and rows: need write access to columns and read/write access to target rows

### Dropping Protected Data
- Cannot drop protected column unless LBAC credentials allow write to that column
- DB2SECURITYLABEL columns cannot be dropped directly
- Must drop security policy first (converts column to VARCHAR(128) FOR BIT DATA)
- LBAC credentials not required to drop entire tables or databases

## Removing LBAC Protection

### Removing Protection from Rows
- Every row in protected table must be protected by security label
- No way to remove LBAC protection from individual rows
- DB2SECURITYLABEL columns cannot be altered except by removing security policy

### Removing Protection from Columns
- Use DROP COLUMN SECURITY clause of ALTER TABLE
- Must have LBAC credentials allowing read and write access to column
- Must have normal privileges and authorities for table alteration

### Removing Security Policy
- Must have SECADM authority
- Use DROP SECURITY POLICY clause of ALTER TABLE
- Automatically removes protection from all rows and columns
- Converts DB2SECURITYLABEL columns to VARCHAR(128) FOR BIT DATA

## Summary

Label-Based Access Control provides extremely granular security control at both row and column levels. By implementing a comprehensive system of security policies, components, labels, and rules, LBAC enables organizations to implement sophisticated access control mechanisms that can match complex security requirements. The system's flexibility allows for simple implementations (high/low trust levels) or complex multi-factor access decisions based on organizational structure, project involvement, and trust levels.

LBAC integrates seamlessly with existing database security mechanisms while providing additional layers of protection that complement discretionary access controls. The rule-based comparison system, exemption mechanisms, and support for various data structures (SET, ARRAY, TREE) make LBAC a powerful tool for implementing enterprise-grade data security policies.