# Comparing Database Backups: Incremental, Differential, and Other Backup Strategies

## Introduction

Incremental and differential backups are two strategies for making regular and frequent copies of data for disaster recovery. IT systems can go down unexpectedly due to unforeseen circumstances such as power outages, natural events, or security issues. Data backup is essential to prevent loss of critical information for operations. However, data volume may make it impractical to take a full data backup at every instant.

- **Differential backup**: Only copies data changes since the last full backup
- **Incremental backup**: Copies data changes since the last backup (of any type)

## Backup Types and How They Work

### Full Backup

When backup software takes a full backup, it copies the entire dataset, regardless of whether any changes were made to the data.

**Characteristics:**
- Copies all data
- Generally taken less frequently for practical reasons
- Can be time-consuming
- Takes up a large amount of storage space
- Alternatives include differential or incremental backups

### Incremental Backup

An incremental backup only copies modified data since the last backup.

**Example:**
- Full backup on Sunday
- Incremental backup on Monday: copies changes since Sunday
- Incremental backup on Tuesday: copies changes since Monday (not Sunday)

**Characteristics:**
- Copies only changed data since last backup
- Backup files are consistently small
- Faster and more efficient backup process

### Differential Backup

A differential backup strategy copies only newly added and changed data since the last full backup.

**Example:**
- Full backup on Sunday
- Differential backup on Monday: copies all changes since Sunday
- Differential backup on Tuesday: also copies all changes since Sunday
- Backup file size increases progressively until the next full backup

**Characteristics:**
- Copies changed data since last full backup
- Backup image file size increases daily
- Requires more time to complete than incremental backups

## Key Differences: Incremental vs. Differential Backup

### Backup Speed

- **Differential backups**: Require more time to complete since the backup image file size increases daily
- **Incremental backups**: Usually quicker and more efficient due to consistently small backup files

### Storage Space Utilization

- **Incremental backups**: Require less storage space
- **Differential backups**: Take more storage space as time from the last full backup increases. The strategy aims to reduce restore time by trading off on storage space.

### Implementation Cost

- **Incremental backups**: Save both backup storage space and network bandwidth. In the long run, a full backup paired with frequent incremental backups is the more cost-effective option.
- **Differential backups**: Get costlier over time, requiring full backups more frequently to increase efficiency.

### Data Restoration Speed

- **Incremental backups**: Can be time-consuming and complex to restore. Requires the first full backup and all subsequent incremental backups to restore data.
  - Example: A crash on Wednesday would require going through all backups from Sunday to Tuesday, identifying changes, and restoring them cumulatively
  - Process gets more complex as time from the last full backup increases

- **Differential backups**: Only requires the first full and the latest differential backup. Much faster restoration process.

## When to Use Differential vs. Incremental Backup

### Frequency of Data Changes

- **Incremental backups**: More suitable if organization deals with substantial amount of data that undergoes frequent changes. Saves both time and backup costs.
- **Differential backups**: Costs could add up quickly with frequent data changes.

### Business Requirements

Decision should be based on available resources and company's backup and data recovery policy.

**Examples:**
- **Product data for ecommerce application**: Critical data—use differential backups for faster restore times and minimal downtime
- **Archive image files or video data**: May only need one full data backup if it doesn't change over time

## Other Backup Strategies

### Synthetic Full Backup

A synthetic full backup compares the data that has changed at the source with the original full backup and all incremental backups to create the next full synthesized backup.

**How it works:**
- Instead of storing only the incremental backup file, the backup server consolidates changes with the last full backup
- Creates a synthetic full backup
- Process is invisible to end users

**Benefits:**
- Doesn't save on storage space
- Saves on network bandwidth
- Only sends incremental changes to the server instead of all data
- Server uses data it already has to create the full backup copy

### Incremental Forever Backup

This strategy only takes an initial full backup, then a sequence of incremental backups indefinitely.

**How it works:**
- Only one initial full backup
- Subsequent (forever) incremental backups taken indefinitely
- Backup server stores all backup sets on a tape library or large disk array
- Automates the restoration process to mimic restoration from a full backup

## Summary of Differences

| Backup Type | Data Copied | Backup Speed | Storage Space | Restoration Speed |
|-------------|-------------|--------------|---------------|-------------------|
| **Active Full** | Copies all data | Slow | Substantial | Fast |
| **Incremental** | Copies only changed data since last backup | Faster than differential | Smaller than differential | Slower than differential (requires full + all incrementals) |
| **Differential** | Copies changed data since last full backup | Slower than incremental but faster than active full | Gets larger especially with subsequent backups | Faster than incremental (requires just full + last differential) |
| **Synthetic Full** | Copies changed data incrementally but consolidates with last full to create synthetic full | Faster than active full (copies only incremental changes) | About the same storage as active full | Similar to active full |
| **Incremental Forever** | Creates one full, then subsequent (forever) incrementals | Faster than synthetic full (never creates subsequent full backups) | Takes less space than active and synthetic full | Offers faster restoration than active and synthetic full |

## Conclusion

The choice of backup strategy depends on:
- Data volume and change frequency
- Available storage space
- Network bandwidth constraints
- Recovery time objectives (RTO)
- Recovery point objectives (RPO)
- Budget constraints
- Business criticality of data

Each strategy offers different trade-offs between backup speed, storage efficiency, and restoration complexity. Organizations should evaluate their specific requirements to choose the most appropriate backup strategy or combination of strategies.